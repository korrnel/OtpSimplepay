<?php
	require_once("sdk/config.php");
	require_once 'sdk/SimplePayment.class.php';
	
	$timeOut = new SimpleLiveUpdate($config);
	
	$message = "";
	if (@$_REQUEST['redirect'] == 1) {
		$message = '<b><font color="red">ABORTED TRANSACTION</font></b><br/>';
		$log['TRANSACTION'] = 'ABORT';
	} else {
		$message = '<b><font color="red">TIMEOUT TRANSACTION</font></b><br/>';
		$log['TRANSACTION'] = 'TIMEOUT';
	} 
	
	$message .= 'DATE: <b>' . date('Y-m-d H:i:s', time()) . '</b><br/>';
	$message .= 'ORDER ID: <b>' . $_REQUEST['order_ref'] . '</b><br/>';
	
	$log['ORDER_ID'] = (isset($_REQUEST['order_ref'])) ? $_REQUEST['order_ref'] : 'N/A';
	$log['CURRENCY'] = (isset($_REQUEST['order_currency'])) ? $_REQUEST['order_currency'] : 'N/A';
	$log['REDIRECT'] = (isset($_REQUEST['redirect'])) ? $_REQUEST['redirect'] : '0';
	$timeOut->logFunc("Timeout", $log, $log['ORDER_ID']); 
	$timeOut->errorLogger(); 
	
	echo $message;			 
?>

