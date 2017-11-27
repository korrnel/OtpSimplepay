<?php

require_once 'WResponse.php';
require_once 'RequestUtils.php';
require_once 'WebShopXmlUtils.php';
require_once 'SignatureUtils.php';
require_once 'SoapUtils.php';
require_once 'DefineConst.php';
require_once 'ConfigUtils.php';

class WebShopService {

    var $soapClient;

    var $lastInputXml = NULL;

    var $lastOutputXml = NULL;

    var $operationLogNames = array(
        'fizetesiTranzakcio' => 'fizetesiTranzakcio',
        'tranzakcioStatuszLekerdezes' => 'tranzakcioStatuszLekerdezes'
    );

    function WebShopService() {
        $this->soapClient = SoapUtils::createSoapClient();
    }

    /**
     * @desc A banki fel�let Ping szolg�ltat�s�nak megh�v�sa.
     * Mivel tranzakci� ind�t�s nem t�rt�nik, a sikeres ping
     * eset�n sem garant�lt az, hogy az egyes fizet�si tranzakci�k
     * sikeresen el is ind�that�k -  csup�n az biztos, hogy a
     * h�l�zati architekt�r�n kereszt�l sikeresen el�rhet� a
     * banki fel�let.
     *
     * Digit�lis al��r�s nem k�pz�dik.
     *
     * @return boolean true sikeres ping-et�s eset�n, egy�bk�nt false.
     */
    function ping() {
        $result = SoapUtils::ping($this->soapClient);
        return $result;
    }

    /**
     * H�romszerepl�s fizet�si folyamat (WEBSHOPFIZETES) szinkron ind�t�sa.
     *
     * @param string $posId
     *        webshop azonos�t�
     * @param string $tranzakcioAzonosito
     *        fizet�si tranzakci� azonos�t�
     * @param mixed $osszeg
     *        Fizetend� �sszeg, (num, max. 13+2), opcion�lis tizedesponttal.
     *        Nulla is lehet, ha a regisztraltUgyfelId param�ter ki van
     *        t�ltve, �s az ugyfelRegisztracioKell �rt�ke igaz. �gy kell
     *        ugyanis jelezni azt, hogy nem t�nyleges v�s�rl�si tranzakci�t
     *        kell ind�tani, hanem egy �gyf�l regisztr�l�st, vagyis az
     *        �gyf�l k�rtyaadatainak bek�r�st �s elt�rol�s�t a banki
     *        oldalon.
     * @param string $devizanem
     *            fizetend� devizanem
     * @param string $nyelvkod
     *            a megjelen�tend� vev� oldali fel�let nyelve
     * @param mixed $nevKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            nev�t
     * @param mixed $orszagKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "orsz�g r�sz�t"
     * @param mixed $megyeKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "megye r�sz�t"
     * @param mixed $telepulesKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "telep�l�s r�sz�t"
     * @param mixed $iranyitoszamKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "ir�ny�t�sz�m r�sz�t"
     * @param mixed $utcaHazszamKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "utca/h�zsz�m r�sz�t"
     * @param mixed $mailCimKell
     *            a megjelen�tend� vev� oldali fel�leten be kell�k�rni a vev�
     *            e-mail c�m�t
     * @param mixed $kozlemenyKell
     *            a megjelen�tend� vev� oldali fel�leten fel kell k�n�lni a
     *            k�zlem�ny megad�s�nak lehet�s�g�t
     * @param mixed $vevoVisszaigazolasKell
     *            a tranzakci� eredm�ny�t a vev� oldalon meg kell jelen�teni
     *            (azaz nem a backURL-re kell ir�ny�tani)
     * @param mixed $ugyfelRegisztracioKell
     *            ha a regisztraltUgyfelId �rt�ke nem �res, akkor megadja, hogy
     *            a megadott azonos�t� �jonnan regisztr�land�-e, vagy m�r
     *            regisztr�l�sra ker�lt az OTP Internetes Fizet� fel�let�n.
     *            El�bbi esetben a kliens oldali b�ng�sz�ben olyan fizet� oldal
     *            fog megjelenni, melyen meg kell adni az azonos�t�hoz tartoz�
     *            jelsz�t, illetve a k�rtyaadatokat. Ut�bbi esetben csak az
     *            azonos�t�hoz tartoz� jelsz� ker�l beolvas�sra az �rtes�t�si
     *            c�men k�v�l. Ha a regisztraltUgyfelId �rt�ke �res, a pamar�ter
     *            �rt�ke nem ker�l felhaszn�l�sra.
     * @param string $regisztraltUgyfelId
     *            az OTP fizet�fel�leten regisztr�land� vagy regisztr�lt �gyf�l
     *            azonos�t� k�dja.
     * @param string $shopMegjegyzes
     *            a webshop megjegyz�se a tranzakci�hoz a vev� r�sz�re
     * @param string $backURL
     *            a tranzakci� v�grehajt�sa ut�n erre az internet c�mre kell
     *            ir�ny�tani a vev� oldalon az �gyfelet (ha a
     *            vevoVisszaigazolasKell hamis)
     * @param string $zsebAzonosito
     * 			  a cafeteria k�rtya zseb azonos�t�ja.
     * @param mixed $ketlepcsosFizetes
     * 			  megadja, hogy k�tl�pcs�s fizet�s ind�tand�-e.
     *            True �rt�k eset�n a fizet�si tranzakci� k�tl�pcs�s lesz,
     *            azaz a terhelend� �sszeg csup�n z�rol�sra ker�l,
     *            s �gy is marad a bolt �ltal ind�tott lez�r� tranzakci�
     *            ind�t�s�ig avagy a z�rol�s el�v�l�s�ig.
     *            Az alap�rtelmezett (�res) �rt�k a Bank oldalon r�gz�tett
     *            alap�rtelmezett m�dot jel�li.
     *
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopFizetesAdatok
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function fizetesiTranzakcio(
            $posId,
            $azonosito,
            $osszeg,
            $devizanem,
            $nyelvkod,
            $nevKell,
            $orszagKell,
            $megyeKell,
            $telepulesKell,
            $iranyitoszamKell,
            $utcaHazszamKell,
            $mailCimKell,
            $kozlemenyKell,
            $vevoVisszaigazolasKell,
            $ugyfelRegisztracioKell,
            $regisztraltUgyfelId,
            $shopMegjegyzes,
            $backURL,
            $zsebAzonosito,
            $ketlepcsosFizetes,
            $key_original) {


        $dom = WebShopXmlUtils::getRequestSkeleton(WF_HAROMSZEREPLOSFIZETESINDITAS, $variables);

        // default �rt�kek feldolgoz�sa
        if (is_null($devizanem) || (trim($devizanem) == "")) {
            $devizanem = DEFAULT_DEVIZANEM;
        }

        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, TRANSACTIONID, $azonosito);
        WebShopXmlUtils::addParameter($dom, $variables, AMOUNT, $osszeg);
        WebShopXmlUtils::addParameter($dom, $variables, EXCHANGE, $devizanem);
        WebShopXmlUtils::addParameter($dom, $variables, LANGUAGECODE, $nyelvkod);

        WebShopXmlUtils::addParameter($dom, $variables, NAMENEEDED, RequestUtils::booleanToString($nevKell));
        WebShopXmlUtils::addParameter($dom, $variables, COUNTRYNEEDED, RequestUtils::booleanToString($orszagKell));
        WebShopXmlUtils::addParameter($dom, $variables, COUNTYNEEDED, RequestUtils::booleanToString($megyeKell));
        WebShopXmlUtils::addParameter($dom, $variables, SETTLEMENTNEEDED, RequestUtils::booleanToString($telepulesKell));
        WebShopXmlUtils::addParameter($dom, $variables, ZIPCODENEEDED, RequestUtils::booleanToString($iranyitoszamKell));
        WebShopXmlUtils::addParameter($dom, $variables, STREETNEEDED, RequestUtils::booleanToString($utcaHazszamKell));
        WebShopXmlUtils::addParameter($dom, $variables, MAILADDRESSNEEDED, RequestUtils::booleanToString($mailCimKell));
        WebShopXmlUtils::addParameter($dom, $variables, NARRATIONNEEDED, RequestUtils::booleanToString($kozlemenyKell));
        WebShopXmlUtils::addParameter($dom, $variables, CONSUMERRECEIPTNEEDED, RequestUtils::booleanToString($vevoVisszaigazolasKell));

        WebShopXmlUtils::addParameter($dom, $variables, BACKURL, $backURL);

        WebShopXmlUtils::addParameter($dom, $variables, SHOPCOMMENT, $shopMegjegyzes);

        WebShopXmlUtils::addParameter($dom, $variables, CONSUMERREGISTRATIONNEEDED, $ugyfelRegisztracioKell);
        WebShopXmlUtils::addParameter($dom, $variables, CONSUMERREGISTRATIONID, $regisztraltUgyfelId);

        WebShopXmlUtils::addParameter($dom, $variables, TWOSTAGED, RequestUtils::booleanToString($ketlepcsosFizetes, NULL));
        WebShopXmlUtils::addParameter($dom, $variables, CARDPOCKETID, $zsebAzonosito);

        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 =>
            $posId, $azonosito, $osszeg, $devizanem, $regisztraltUgyfelId);
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($key_original);
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey);

        $attrName = null;
		$attrValue = null;

		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}

        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);

        /* Tranzakci� adatainak napl�z�sa egy k�l�n f�jlba */

        /* A tranzakci� ind�t�sa */
        $startTime = time();
        $workflowState = SoapUtils::startWorkflowSynch(WF_HAROMSZEREPLOSFIZETESINDITAS, $this->lastInputXml, $this->soapClient);

        if (!is_null($workflowState)) {
            $response = new WResponse(WF_HAROMSZEREPLOSFIZETES, $workflowState);
        }
        else {
            // A tranzakci� megszakadt, a banki fel�let v�lasz�t nem
            // tudta a kliens fogadni
            $poll = true;
            $resendDelay = 20;
            do {
                $tranzAdatok = $this->tranzakcioPoll($posId, $azonosito, $startTime, $key_original);
                if ($tranzAdatok === false) {
                    // nem siker�lt a lek�rdez�s, �jrapr�b�lkozunk
                    $poll = true;
                }
                else {
                    if ($tranzAdatok->isFizetesFeldolgozasAlatt()) {
                        // a tranzakci� feldolgoz�s alatt van
                        // mindenk�pp �rdemes kicsit v�rni, �s �jra pollozni
                    }
                    else {
                        // a tranzakci� feldolgoz�sa befejez�d�tt
                        // (lehet sikeres vagy sikertelen az eredm�ny)
                        $poll = false;
                        $response = new WResponse(WF_HAROMSZEREPLOSFIZETES, null);
                        // a folyamat v�lasz�nak napl�z�sa
                        $response->loadAnswerModel($tranzAdatok, $tranzAdatok->isSuccessful(), $tranzAdatok->getPosValaszkod());
                        return $response;
                    }
                }
                $retryCount++;
                sleep($resendDelay);
            } while ($poll && ($startTime + 660 > time()));
            // pollozunk, am�g van �rtelme, de legfeljebb 11 percig!

        }

        // a folyamat v�lasz�nak napl�z�sa
        if ($response->isFinished()) {
            $responseDom = $response->getResponseDOM();
            $this->lastOutputXml = WebShopXmlUtils::xmlToString($responseDom);
        }
        else {
        }

        return $response;
    }

    /**
     * WEBSHOPTRANZAKCIOLEKERDEZES folyamat szinkron ind�t�sa.
     *
     * @param string $posId webshop azonos�t�
     * @param string $azonosito lek�rdezend� tranzakci� azonos�t�
     * @param mixed $maxRekordSzam maxim�lis rekordsz�m (int / string)
     * @param mixed $idoszakEleje lek�rdezend� id�szak eleje
     *        ����.HH.NN ��:PP:MM alak� string �rt�k vagy int timestamp
     * @param mixed $idoszakEleje lek�rdezend� id�szak v�ge
     *        ����.HH.NN ��:PP:MM alak� string �rt�k vagy int timestamp
     *
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopAdatokLista
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function tranzakcioStatuszLekerdezes(
            $posId,
            $azonosito,
            $maxRekordSzam,
            $idoszakEleje,
            $idoszakVege,
            $key_original) {


        $dom = WebShopXmlUtils::getRequestSkeleton(WF_TRANZAKCIOSTATUSZ, $variables);

        $idoszakEleje = RequestUtils::dateToString($idoszakEleje);
        $idoszakVege = RequestUtils::dateToString($idoszakVege);

        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, TRANSACTIONID, $azonosito);
        WebShopXmlUtils::addParameter($dom, $variables, QUERYMAXRECORDS, $maxRekordSzam);
        WebShopXmlUtils::addParameter($dom, $variables, QUERYSTARTDATE, $idoszakEleje);
        WebShopXmlUtils::addParameter($dom, $variables, QUERYENDDATE, $idoszakVege);

        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 =>
            $posId, $azonosito,
            $maxRekordSzam, $idoszakEleje, $idoszakVege );
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($key_original);
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey);

        $attrName = null;
		$attrValue = null;

		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}

        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);

        /* a folyamat ind�t�sa */
        $workflowState = SoapUtils::startWorkflowSynch(WF_TRANZAKCIOSTATUSZ, $this->lastInputXml, $this->soapClient);
        $response = new WResponse(WF_TRANZAKCIOSTATUSZ, $workflowState);

        /* a folyamat v�lasz�nak napl�z�sa */
        if ($response->isFinished()) {

            $responseDom = $response->getResponseDOM();
            $this->lastOutputXml = WebShopXmlUtils::xmlToString($responseDom);
        }
        else {
        }

        return $response;
    }

    /**
     * WEBSHOPTRANZAKCIOLEKERDEZES folyamat szinkron ind�t�sa polloz�s c�lj�b�l.
     * A bank nem javasolja, hogy polloz�sos technik�val t�rt�njen a fizet�si
     * tranzakci�k eredm�ny�nek lek�rdez�se - mindazon�ltal kommunik�ci�s vagy
     * egy�b hiba eset�n ez az egyetlen m�dja annak, hogy a tranzakci� v�lasz�t
     * ut�lag le lehessen k�rdezni.
     *
     * @param string $posId webshop azonos�t�
     * @param string $azonosito lek�rdezend� tranzakci� azonos�t�
     * @param int $inditas a tranzakci� ind�t�sa az ind�t� kliens �r�ja szerint
     *                     (a lek�rdez�s +-24 �r�ra fog korl�toz�dni)
     *
     * @return mixed Sikeres lek�rdez�s �s l�tez� tranzakci� eset�n
     *               a vonatkoz� WebShopFizetesAdatok. A tranzakci� �llapot�t
     *               ez az objektum fogja tartalmazni - ami utalhat p�ld�ul
     *               vev� oldali input v�rakoz�sra vagy feldolgozott st�tuszra.
     *               FALSE hib�s lek�rdez�s eset�n. (Pl. nem l�tezik tranzakci�)
     */
    function tranzakcioPoll($posId, $azonosito,  $inditas, $key_original) {

        $maxRekordSzam = "1";
        $idoszakEleje = $inditas - 60*60*24;
        $idoszakVege = $inditas + 60*60*24;

        $tranzAdatok = false;
        $response = $this->tranzakcioStatuszLekerdezes($posId, $azonosito, $maxRekordSzam, $idoszakEleje, $idoszakVege, $key_original);
        if ($response) {
            $answer = $response->getAnswer();
            if ($response->isSuccessful()
                    && $response->getAnswer()
                    && count($answer->getWebShopFizetesAdatok()) > 0) {

                // Siker�lt lek�rdezni az adott tranzakci� adat�t
                $fizetesAdatok = $answer->getWebShopFizetesAdatok();
                $tranzAdatok = reset($fizetesAdatok);
            }
        }
        return $tranzAdatok;
    }
}

?>
