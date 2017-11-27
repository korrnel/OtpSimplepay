<?php

session_start();

if (isset($_REQUEST['guilang'])) {
	$_SESSION['guilang']=$_REQUEST['guilang'];
}
if (isset($_SESSION['guilang'])) {
	$config['LANGUAGE']=$_SESSION['guilang'];
}

if (isset($_REQUEST['METHOD']) && $_REQUEST['METHOD']!='') {
	$config['METHOD'] = $_REQUEST['METHOD'];
}
	
	
/*
 * Language settings -- ONLY FOR TEST!
 */	
if (!isset($config['LANGUAGE']) || $config['LANGUAGE'] == 'HU' || $config['LANGUAGE'] == '' || $config['LANGUAGE'] != 'EN') {

	$langActiveClassHu = 'class="active"';
	$langActiveClassEn = '';
	define("LANGUAGE", "HU");
			
	define("SUCCESFUL_PAYMENT", "Sikeres fizetés");
	define("UNSUCCESFUL_PAYMENT", "Sikertelen fizetés");
		
	//General
	define("TEST", "teszt");	
	define("TEST_UPPER", "Teszt");
	define("DEVELOPER_GUIDE", "Fejlesztési, tesztelési segédlet az online fizetési megoldásokhoz");
	define("MORE", "Tovább");	
	define("BACK", "Vissza");	

	define("CALL_TO_START_TRANSACTION", "Az alábbi gomb megnyomásával azonnal végezhet teszt fizetési tranzakciót");
	define("METHOD_CCVISAMC_DESC", "A tranzakció során Ön átkerül a SimplePay fizető oldalára, ahol a kártyaadatok megadása után lezajlik a teszt fizetés, majd vissza lesz irányítva ide.");
	define("METHOD_WIRE_DESC", "A tranzakció során Ön átkerül a SimplePay fizető oldalára, ahol az utalás végrehajtásához szükséges adatokat találja.");

	//Redirect page
	define("TRANSACTION_STARTED", "A tranzakció elindult");
	define("TRANSACTION_ABORTED", "A tranzakció meg lett szakítva!");		
	define("PLEASE_WAIT", "Kérjük, várjon!");	
	define("WILL_BE_REDIRECTED", "Hamarosan át lesz irányítva a SimplePay fizető oldalára.");
	define("ABORT_DESC_1", "A megszakítást az sdk/config.php DEBUG_LIVEUPDATE_PAGE változója szabályozza");
	define("ABORT_DESC_2", "Normál tranzakciók esetén ennek false értéket adjon!");	
	define("NO_REDIRECT_NOTICE", "Ha 30 műsodpercig nem történik meg az átirányítás, akkor kérjük nyomja meg a lenti gombot.");
	define("DO_REDIRECT_NOTICE", "A lenti gombra kattintva tovább folytatható a tranzakció");	
	define("THANK_YOU", "Köszönjük");
	
	//buttons title
	define("NEW_TEST_PAYMENT_BTN", "Új teszt fizetés indítása");
	define("TRX_LOG_BTN", "Tranzakció log");
	define("HUF_PAYMETN_BTN", "HUF fizetés");
	define("EUR_PAYMETN_BTN", "EUR fizetés");
	define("USD_PAYMETN_BTN", "USD fizetés");
	define("MISSING_PARAMS", "<b>Hiányzó kereskedői adatok</b><br>Az sdk/config.php fájlban legalább egy devizanemhez állítson be MERCHANT és SECRET_KEY adatokat, mert csak ezek ismeretében tud fizetési tranzakciót indítani. Ezek az adatok csak akkor állnak rendelkezésére, ha szerződött partnere a <a href=\"http://simplepartner.hu/online_fizetesi_szolgaltatas.html\" target=\"_blank\">OTP Mobil Kft</a>-nek.<br>");
	define("IRN_TEXT", "<p>Az IRN (Instant Refund Notification) lehetővé teszi, hogy a kereskedő közvetlenül a saját adminisztrációs felületéről indítson visszatérítés kérést.</p><p>Nem igényel SimplePay oldali beállítást!</p>");
	define("IRN_TEXT2", "IRN indítása a jelenlegi (<b> %s </b>) tranzakcióra");
	define("IDN_TEXT", "<p>Az IDN (Instant Delivery Notification) lehetővé teszi, hogy a kereskedő közvetlenül a saját adminisztrációs felületéről indítson rendelés jóváhagyási kérést.</p><p>SimplePay oldali beállítást is igényel!</p>");
	define("IDN_TEXT2", "IDN indítása a jelenlegi (<b> %s </b>) tranzakcióra");
	define("IOS_TEXT", "<p>Az IOS (Instant Order Status) lehetővé teszi, hogy a kereskedő közvetlenül a saját adminisztrációs felületéről kérje le a tranzakció státuszát.</p><p>Nem igényel SimplePay oldali beállítást!</p>");
	define("IOS_TEXT2", "IOS indítása a jelenlegi (<b> %s </b>) tranzakcióra");
	define("IRN_TEXT3","IRN");
	define("IDN_TEXT3","IDN");
	define("IOS_TEXT3","IOS");
	define("RUN_TEXT","indítás");
	define("NO_ORDERREF_TEXT","Nincs rendelésszám az REQUEST-ben.");
	
	define("LOGTEXT1","Megrendelés (order_ref)");
	define("LOGTEXT2","Művelet");
	define("LOGTEXT3","Dátum");
	define("LOGTEXT4","Változó");
	define("LOGTEXT5","Érték");
	
	define("IRNTEXTTPL","<p>Az IRN eredményét a kereskedői vezérlőpulton a <b>%s</b> tranzakció adatainál tudja nyomon követni</p>");
	

	//LiveUpdate
	define("LIVEUPDATE_TEST_TRANSACTION", "Teszt tranzakció");
	define("BACKREF_REDIRECT_PAGE", "tájékoztató oldal");
	define("TIMEOUT_PAGE", "Időtúllépés");
	define("SET_UP_MERCHANT", "Az sdk/config.php fájlban állítsa be a kereskedői azonosítóját (MERCHANT)");
	define("SET_UP_SECRET_KEY", "Az sdk/config.php fájlban állítsa be a titkos kulcsát (SECRET_KEY)");	
	define("METHOD_CCVISAMC", "Bankkártya");
	define("METHOD_WIRE", "Átutalás");
	define("START_CCVISAMC", "Bankkártyás fizetés indítása");
	define("START_WIRE", "Átutalásos fizetés indítása");
	define("PAYMENT_METHOD", "Fizetési mód");
	define("SETTINGS", "Beállítás");
	define("PAYMENT_BUTTON", "SimplePay online fizetés indítása");
		
	//BackRef
	define("SUCCESSFUL_CARD_AUTHORIZATION", "Sikeres kártya ellenőrzés.");
	define("SUCCESSFUL_WIRE", "Sikeres megrendelés. <br/>Az utalás megérkezése után lesz teljesítve a megrendelés");
	define("WAITING_FOR_IPN", "Megerősítésre vár!");
	define("CONFIRMED_IPN", "IPN megerősítve, sikeres fizetés!");
	define("CONFIRMED_WIRE", "Beérkezett átutalás");
	define("UNSUCCESSFUL_TRANSACTION", "Sikertelen tranzakció.");
	define("UNSUCCESSFUL_NOTICE", "Kérjük, ellenőrizze a tranzakció során megadott adatok helyességét.<br>Amennyiben minden adatot helyesen adott meg, a visszautasítás okának kivizsgálása kapcsán kérjük, szíveskedjen kapcsolatba lépni kártyakibocsátó bankjával.");
	define("END_OF_TRANSACTION", "Vége a tranzakciónak.");
	define("BACKREF_DATE", "Dátum");
	define("DATE", "Dátum");
	define("HASHTEXT", "HASH");
	define("ORDER_ID", "Megrendelés azonosító");
	define("PAYREFNO", "SimplePay referenciaszám");
	define("STATUS", "Státusz");
	define("RESPONSECODE", "Válaszkód");
	define("RESPONSEMSG", "Válasz üzenet");
	define("PAYMENT_TEST", "Teszt fizetés");
	
	//Timeout
	define("ABORTED_TRANSACTION", "Megszakított tranzakció");
	define("TIMEOUT_TRANSACTION", "Időtúllépéses tranzakció	");
	define("TIMEOUT_PAGE_TITLE", "Időtúllépés, vagy megszakítás");
	define("TIMEOUT_NOTICE", "Ön megszakította a fizetést, vagy lejárt a tranzakció maximális ideje!");

	//IRN
	define("IRN_PAGE_TITLE", "Instant Refund Notification");
	
	//IDN
	define("IDN_PAGE_TITLE", "Instant Delivery Notification");
	

} elseif ($config['LANGUAGE']=='EN' ) {

	$langActiveClassHu = '';
	$langActiveClassEn = 'class="active"';
	define("LANGUAGE", "EN");
	
	//General
	define("TEST", "test");	
	define("TEST_UPPER", "Test");
	define("DEVELOPER_GUIDE", "Developer and testing guide for online payment solutions");
	define("MORE", "More");	
	define("BACK", "Back");	

	define("CALL_TO_START_TRANSACTION", "By clicking on button below you can start test payment transaction immediately");
	define("METHOD_CCVISAMC_DESC", "During the transaction you will be redirected to SimplePay payment page. On this page you can fill your card data and then you return here automatically.");
	define("METHOD_WIRE_DESC", "Due to the payment process you will be redirected to SimplePay Payment Page where You can get an information in order to make a transfer.");

	//Redirect page
	define("TRANSACTION_STARTED", "Transaction started");
	define("TRANSACTION_ABORTED", "Transaction was aborted in purpose of debug!");	
	define("PLEASE_WAIT", "Please wait!");			
	define("WILL_BE_REDIRECTED", "You will be redirect shortly.");
	define("ABORT_DESC_1", "DEBUG_LIVEUPDATE_PAGE variable in sdk/config.php controlls the debug process.");
	define("ABORT_DESC_2", "In case of general transaction switch it to false!");
	define("NO_REDIRECT_NOTICE", "If you do not redirect more than 30 sec, please push the button below.");
	define("DO_REDIRECT_NOTICE", "By clicking on the button bellow you can continue the transaction!");
	define("THANK_YOU", "Thank you");

	//buttons title
	define("NEW_TEST_PAYMENT_BTN", "Start new test payment");
	define("TRX_LOG_BTN", "Transaction log");
	define("HUF_PAYMETN_BTN", "HUF payment");
	define("EUR_PAYMETN_BTN", "EUR payment");
	define("USD_PAYMETN_BTN", "USD payment");
	define("MISSING_PARAMS", "<b>Missing merchant data</b><br>Please set up a MERCHANT and a SECRET_KEY data in sdk/config.php. You can start transaction with them only. You have this data if you have a contract with <a href=\"http://simplepartner.hu/online_fizetesi_szolgaltatas.html\" target=\"_blank\">OTP Mobil Kft</a><br>");
	
	
	define("IRN_TEXT", "<p>IRN (Instant Refund Notification) allows for merchants to send refund from their own admin system.</p><p>No need any SimplePay settings.</p>");
	define("IRN_TEXT2", "Start IRN to this (<b> %s </b>) transaction!");
	define("IDN_TEXT", "<p>IDN (Instant Delivery Notification) allows for merchants to send confirm from their own admin system. This funcion is only for two step payment.</p><p>SimplePay has to turning on this feature!</p>");
	define("IDN_TEXT2", "Start IDN to this (<b> %s </b>) transaction!");
	define("IOS_TEXT", "<p>IOS (Instant Order Status) allows for merchants to send order status query from their own admin system.</p><p>No need any SimplePay settings!</p>");
	define("IOS_TEXT2", "Start IOS to this (<b> %s </b>) transaction!");
	
	define("IRN_TEXT3","IRN");
	define("IDN_TEXT3","IDN");
	define("IOS_TEXT3","IOS");
	define("RUN_TEXT","start");
	define("NO_ORDERREF_TEXT","There is no order ref in REQUEST.");
	
	define("LOGTEXT1","Order (order_ref)");
	define("LOGTEXT2","Type");
	define("LOGTEXT3","Date");
	define("LOGTEXT4","Parameter");
	define("LOGTEXT5","Value");
	
	define("IRNTEXTTPL","You can check the result of the IRN (<b>%s</b>) on the admin system .");
						
	//LiveUpdate
	define("LIVEUPDATE_TEST_TRANSACTION", "LiveUpdate test transaction");
	define("BACKREF_REDIRECT_PAGE", "notification page");
	define("TIMEOUT_PAGE", "Timeout page");
	define("SET_UP_MERCHANT", "Please define merchant id (MERCHANT) in sdk/config.php.");
	define("SET_UP_SECRET_KEY", "Please define secret key (SECRET_KEY) in sdk/config.php.");
	define("METHOD_CCVISAMC", "Credit card");
	define("METHOD_WIRE", "Wire transfer");
	define("METHOD_ON_PAYMENT_PAGE", "Select on payment page");
	define("START_CCVISAMC", "Start credit card payment");
	define("START_SIMPLE_MOBILE", "Start SimplePay mobil payment");
	define("START_WIRE", "Start wire payment");
	define("PAYMENT_METHOD", "Payment method");
	define("SETTINGS", "Settings");
	define("PAYMENT_BUTTON", "Start SimplePay online payment");

	//BackRef
	define("SUCCESSFUL_CARD_AUTHORIZATION", "Successful card authorization.");
	define("SUCCESSFUL_WIRE", "Successful order.<br/>After successful wire transfer will be fulfilled your order.");
	define("WAITING_FOR_IPN", "Waiting for confirmation!");
	define("CONFIRMED_IPN", "IPN was confirmed, payment is successful!");
	define("CONFIRMED_WIRE", "Wire transfer has been received");	
	define("UNSUCCESSFUL_TRANSACTION", "Unsuccessful transaction.");
	define("UNSUCCESSFUL_NOTICE", "Please check the data entered during the transaction.<br/>If you submitted every data correctly, please contact your account holder financial institute.");
	define("END_OF_TRANSACTION", "End of transaction.");
	define("BACKREF_DATE", "Date");
	define("DATE", "Date");
	define("ORDER_ID", "Order ID");
	define("PAYREFNO", "SimplePay reference number");
	define("STATUS", "Status");
	define("PAYMENT_TEST", "Payment test");
	define("HASHTEXT", "HASH");
	define("RESPONSECODE", "Response code");
	define("RESPONSEMSG", "Response message");
	 
	//Timeout
	define("ABORTED_TRANSACTION", "Cancel on payment page");
	define("TIMEOUT_TRANSACTION", "Timeout");
	define("TIMEOUT_PAGE_TITLE", "Timeout or cancel");
	define("TIMEOUT_NOTICE", "You canceled the payment or you have ran out of time!");

	//IRN
	define("IRN_PAGE_TITLE", "Instant Refund Notification");	
	
	//IDN
	define("IDN_PAGE_TITLE", "Instant Delivery Notification");			
} 
				
	
?>