<?php
	require_once 'sdk/config.php';
	require_once 'sdk/SimplePayment.class.php';
	
	$orderCurrency = (isset($_REQUEST['CURRENCY'])) ? $_REQUEST['CURRENCY'] : 'N/A';
	
	$ipn = new SimpleIpn($config, $orderCurrency);		
	if($ipn->validateReceived()){	
		$ipn->confirmReceived();	 
	}
	$ipn->errorLogger();
	
    if ($ipn->debug) {
        print $ipn->getDebugMessage();
    }
    if (count($ipn->errorMessage) > 0) {
        print $ipn->getErrorMessage();
    }
?>


