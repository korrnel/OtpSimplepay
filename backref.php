<?php
 	require_once 'sdk/config.php';
	require_once 'sdk/SimplePayment.class.php';	
	
	$orderCurrency = (isset($_REQUEST['order_currency'])) ? $_REQUEST['order_currency'] : 'N/A';
	$orderRef = (isset($_REQUEST['order_ref'])) ? $_REQUEST['order_ref'] : 'N/A'; 	
	
	$backref = new SimpleBackRef($config, $orderCurrency );		
	$backref->order_ref = $orderRef;	
	
	$links = "";	
	if($backref->checkResponse()){	
		$backStatus = $backref->backStatusArray;
		$message = '';		 
		//CCVISAMC
		if ($backStatus['PAYMETHOD'] == 'Visa/MasterCard/Eurocard') {
			$message .= '<b><font color="green">' . SUCCESSFUL_CARD_AUTHORIZATION . '</font></b><br/>';
			if ($backStatus['ORDER_STATUS'] == 'IN_PROGRESS') {
				$message .= '<b><font color="green">' . WAITING_FOR_IPN . '</font></b><br/>';
			} elseif ($backStatus['ORDER_STATUS' ] == 'PAYMENT_AUTHORIZED') {
				$message .= '<b><font color="green">' . WAITING_FOR_IPN . '</font></b><br/>';
			} elseif ($backStatus['ORDER_STATUS'] == 'COMPLETE') {
				$message .= '<b><font color="green">' . CONFIRMED_IPN . '</font></b><br/>';
			}
		}
		//WIRE
		elseif ($backStatus['PAYMETHOD'] == 'Bank/Wire transfer') {
			$message = '<b><font color="green">' . SUCCESSFUL_WIRE . '</font></b><br/>';
			if ($backStatus['ORDER_STATUS'] == 'PAYMENT_AUTHORIZED' || $backStatus['ORDER_STATUS'] == 'COMPLETE') {
				$message .= '<b><font color="green">' . CONFIRMED_WIRE . '</font></b><br/>';
			} 			
		}
		$links .= '<a href="irn.php?order_ref=' . $backStatus['REFNOEXT'] . '&payrefno=' . $backStatus['PAYREFNO'] . '&ORDER_AMOUNT=331&AMOUNT=331&ORDER_CURRENCY=' . $orderCurrency . '">IRN</a>';
		$links .= ' | <a href="idn.php?order_ref=' . $backStatus['REFNOEXT'] . '&payrefno=' . $backStatus['PAYREFNO'] . '&ORDER_AMOUNT=331&ORDER_CURRENCY=' . $orderCurrency . '">IDN</a>';
	} else {
		$backStatus = $backref->backStatusArray;		
		$message = '<b><font color="red">' . UNSUCCESSFUL_TRANSACTION . '</font></b><br/>';
		$message .= '<b><font color="red">' . END_OF_TRANSACTION . '</font></b><br/>';
		$message .= UNSUCCESSFUL_NOTICE . '<br/><br/>';		
	}
	
	$links .= ' | <a href="ios.php?simpleid=' . $backStatus['PAYREFNO'] . '&order_ref=' . $backStatus['REFNOEXT'] . '&ORDER_CURRENCY=' . $orderCurrency . '">IOS</a>';	
	$message .= 'PAYREFNO: <b>' . $backStatus['PAYREFNO'] . '</b><br/>'; 
	$message .= 'ORDER ID: <b>' . $backStatus['REFNOEXT'] . '</b><br/>';
	$message .= 'BACKREF DATE: <b>' . $backStatus['BACKREF_DATE'] . '</b><br/>';
	$backref->errorLogger();  
	echo $message.$links;	 
?>

			
