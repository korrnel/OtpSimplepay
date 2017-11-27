<?php
 
$config = array(
	
	'HUF_MERCHANT' => "11111111111",			//merchant account ID (HUF)
    'HUF_SECRET_KEY' => "111111",			//secret key for account ID (HUF)
    'EUR_MERCHANT' => "",			//merchant account ID (EUR)
    'EUR_SECRET_KEY' => "",			//secret key for account ID (EUR)
    'USD_MERCHANT' => "",			//merchant account ID (USD)
    'USD_SECRET_KEY' => "",			//secret key for account ID (USD)

	'CURL' => true,					//use cURL or not
      'SANDBOX' => true,				//true: sandbox transaction, false: live transaction
	'PROTOCOL' => 'http',			//http or https
	
    'BACK_REF' => $_GET['BACK_REF'],//,		   //url of payment backref page
  'TIMEOUT_URL' => $_GET['TIMEOUT_URL'],//     //url of payment timeout page
//    'IRN_BACK_URL' => $_SERVER['HTTP_HOST'] . '/irn.php',        //url of payment irn page
//    'IDN_BACK_URL' => $_SERVER['HTTP_HOST'] . '/idn.php',        //url of payment idn page
//    'IOS_BACK_URL' => $_SERVER['HTTP_HOST'] . '/ios.php',        //url of payment idn page
	
    'GET_DATA' => $_GET,
    'POST_DATA' => $_POST,
    'SERVER_DATA' => $_SERVER,    
	
	'LOGGER' => true,                                   //basic transaction log
    'LOG_PATH' => 'log',  								//path of log file
	
	'DEBUG_LIVEUPDATE_PAGE' => false,					//Debug message on demo LiveUpdate page (only for development purpose)
	'DEBUG_LIVEUPDATE' => false,						//LiveUpdate debug into log file
	'DEBUG_BACKREF' => false,							//BackRef debug into log file
	'DEBUG_IPN' => false,								//IPN debug into log file
	'DEBUG_IRN' => false,								//IRN debug into log file
	'DEBUG_IDN' => false,								//IDN debug into log file
	'DEBUG_IOS' => false,								//IOS debug into log file
	'DEBUG_ONECLICK' => false,							//OneClick debug into log file
	'DEBUG_ALU' => false,								//ALU debug into log file
);


?>
