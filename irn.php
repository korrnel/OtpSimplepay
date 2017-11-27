<?php  
	require_once("sdk/config.php");
	require_once('sdk/SimplePayment.class.php');
	
	$orderCurrency = (isset($_REQUEST['ORDER_CURRENCY'])) ? $_REQUEST['ORDER_CURRENCY'] : 'HUF';
	$irn = new SimpleIrn($config, $orderCurrency);
	
	$data['REFNOEXT']  = isset($_REQUEST['order_ref']) ? $_REQUEST['order_ref'] : 'N/A';	
	$data['ORDER_REF'] = isset($_REQUEST['payrefno']) ? $_REQUEST['payrefno'] : 'N/A';
	$data['ORDER_AMOUNT'] =  isset($_REQUEST['ORDER_AMOUNT']) ? $_REQUEST['ORDER_AMOUNT'] : 'N/A';	
	$data['ORDER_CURRENCY'] = $orderCurrency;
	$data['IRN_DATE'] = @date("Y-m-d H:i:s");
	$data['AMOUNT'] =  isset($_REQUEST['AMOUNT']) ? $_REQUEST['AMOUNT'] : 'N/A';	
	$response = $irn->requestIrn($data);		
	
	if (isset($response['RESPONSE_CODE'])) {
		$check = $irn->checkResponseHash($response);
	}	 
	$irn->errorLogger();  
	
	$message = 'PAYREFNO: <b>' . $response['ORDER_REF'] . '</b><br/>';
	$message .= 'RESPONSECODE: <b>' . $response['RESPONSE_CODE'] . '</b><br/>';
	$message .= 'RESPONSEMSG: <b>' . $response['RESPONSE_MSG'] . '</b><br/>';
	$message .= 'DATE: <b>' . $response['IRN_DATE'] . '</b><br/>';
						
	$response['PAYREFNO'] = $response['ORDER_REF'];	
	$response['REFNOEXT'] = $_REQUEST['order_ref'];	
				
	echo $message;
?>