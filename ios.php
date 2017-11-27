<?php
	require_once("sdk/config.php");
	require_once('sdk/SimplePayment.class.php');
	
	$orderCurrency = (isset($_REQUEST['currency'])) ? $_REQUEST['currency'] : 'HUF';
	$orderexternalId = (isset($_REQUEST['order_ref'])) ? $_REQUEST['order_ref'] : 'N/A';
	
	$ios = new SimpleIos($config, $orderCurrency, $orderexternalId);
	$ios->errorLogger(); 
	
	$message = 'DATE:<b>' . $ios->status['ORDER_DATE'] . '</b><br/>';	
	$message .= 'PAYREFNO:<b>' . $ios->status['REFNO'] . '</b><br/>';	
	$message .= 'ORDER_ID:<b>' . $ios->status['REFNOEXT'] . '</b><br/>';	
	$message .= 'STATUS:<b>' . $ios->status['ORDER_STATUS'] . '</b><br/>';	
	$message .= 'PAYMENT METHOD:<b>' . $ios->status['PAYMETHOD'] . '</b><br/>';	
	$message .= 'HASHTEXT:<b>' . $ios->status['HASH'] . '</b><br/>';
	
	echo $message;
?>