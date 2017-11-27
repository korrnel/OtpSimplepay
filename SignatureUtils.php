<?php

class SignatureUtils {

    public static function loadPrivateKey($key_original) {
          $key = openssl_get_privatekey(str_replace("\r\n", "\n", $key_original));
          return $key;
    }

    /**
    * Al��rand� sz�veg el��ll�t�sa az al��rand� sz�veg �rt�kek list�j�b�l:
    * [s1, s2, s3, s4]  ->  's1|s2|s3|s4'
    *
    * @param array al��rand� mez�k
    * @return string al��rand� sz�veg
    */
    public static function getSignatureText($signatureFields) {
        $signatureText = '';
        foreach ($signatureFields as $data) {
            $signatureText = $signatureText.$data.'|';
        }

        if (strlen($signatureText) > 0) {
            $signatureText = substr($signatureText, 0, strlen($signatureText) - 1);
        }

        return $signatureText;
    }

    /**
    * Digit�lis al��r�s gener�l�sa a Bank �ltal elv�rt form�ban.
    * Az al��r�s sor�n az MD5 hash algoritmust haszn�ljuk 5.4.8-n�l kisebb verzi�j� PHP
    * eset�n, egy�bk�nt SHA-512 algoritmust.
    *
    * @param string $data az al��rand� sz�veg
    * @param resource $pkcs8PrivateKey priv�t kulcs
    *
    * @return string digit�lis al��r�s, hexadecim�lis form�ban (ahogy a banki fel�let elv�rja).
    */
    public static function generateSignature($data, $pkcs8PrivateKey) {

    	global $signature;

    	if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
        	openssl_sign($data, $signature, $pkcs8PrivateKey, OPENSSL_ALGO_SHA512);
    	}
    	else {
    		openssl_sign($data, $signature, $pkcs8PrivateKey, OPENSSL_ALGO_MD5);
    	}

        return bin2hex($signature);
    }

}

?>
