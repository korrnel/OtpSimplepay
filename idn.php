<?php 
	require_once("sdk/config.php");
	require_once('sdk/SimplePayment.class.php');
	
	$orderCurrency = (isset($_REQUEST['ORDER_CURRENCY'])) ? $_REQUEST['ORDER_CURRENCY'] : 'HUF';	
	$idn = new SimpleIdn($config, $orderCurrency);
	
	$data['REFNOEXT'] = $_REQUEST['order_ref'];	 
	$data['ORDER_REF'] = (isset($_REQUEST['payrefno'])) ? $_REQUEST['payrefno'] : 'N/A';
	$data['ORDER_AMOUNT'] = (isset($_REQUEST['ORDER_AMOUNT'])) ? $_REQUEST['ORDER_AMOUNT'] : 'N/A';	
	$data['ORDER_CURRENCY'] = $orderCurrency;
	$data['IDN_DATE'] = @date("Y-m-d H:i:s");
	$response = $idn->requestIdn($data);
	
	if (isset($response['RESPONSE_CODE'])) {	
		$check = $idn->checkResponseHash($response);		
	}
	$idn->errorLogger();
	
	$message = 'ORDER_ID: <b>' . $response['ORDER_REF'] . '</b><br/>';
	$message .= 'RESPONSECODE: <b>' . $response['RESPONSE_CODE'] . '</b><br/>';
	$message .= 'RESPONSEMSG: <b>' . $response['RESPONSE_MSG'] . '</b><br/>';
	$message .= 'DATE: <b>' . $response['IDN_DATE'] . '</b><br/>';
	
	$response['PAYREFNO'] = $response['ORDER_REF'];
	$response['REFNOEXT'] = $_REQUEST['order_ref'];
	echo $message;
?>