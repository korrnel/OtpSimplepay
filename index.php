<?php

	//Optional error riporting
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
 
     //Import config data    
    require_once 'sdk/config.php';

    //Import SimplePayment class
    require_once 'sdk/SimplePayment.class.php';

    //Set merchant account data by currency
    $orderCurrency = 'HUF';
    $testOrderId = str_replace(array('.', ':'), "", $_SERVER['SERVER_ADDR']) . @date("U", time()) . rand(1000, 9999); 
	
    //Test helper functions  -- ONLY FOR TEST!
    require_once 'sdk/demo_functions.php';
    if (isset($_REQUEST['testcurrency'])) {
        $orderCurrency = $_REQUEST['testcurrency'];
    }
	      
    //Start LiveUpdate
    $lu = new SimpleLiveUpdate($config, $orderCurrency);     

    //Order global data (need to fill by YOUR order data)    	
    $lu->setField("ORDER_REF", $testOrderId);
	
    //optional fields
	$lu->setField("LANGUAGE", LANGUAGE);						//DEFAULT: HU
	//$lu->setField("ORDER_DATE", @date("Y-m-d H:i:s"));		//DEFAULT: current date
	//$lu->setField("ORDER_TIMEOUT", 600);						//DEFAULT: 300
	//$lu->setField("PAY_METHOD", 'WIRE');						//DEFAULT: CCVISAMC
	//$lu->setField("DISCOUNT", 10); 							//DEFAULT: 0
	//$lu->setField("ORDER_SHIPPING", 70);						//DEFAULT: 0
	//$lu->setField("BACK_REF", $config['BACK_REF']);			//DEFAULT: $config['BACK_REF']
	//$lu->setField("TIMEOUT_URL", $config['TIMEOUT_URL']);		//DEFAULT: $config['TIMEOUT_URL']
	//$lu->setField("LU_ENABLE_TOKEN", true);					//Only case of uniq contract with OTP Mobil Kft.! DO NOT USE WITHOUT IT!
 
    //Sample product with gross price
    foreach ($_GET['ORDER_PRICE'] as $key=>$elem) 
    $lu->addProduct(array(
        'name' => $_GET['ORDER_PNAME'][$key],                            		//product name [ string ]
        'code' => $_GET['ORDER_PCODE'][$key],                            		//merchant systemwide unique product ID [ string ]
        'info' => $_GET['ORDER_PNAME'][$key],     				//product description [ string ]
        'price' => $_GET['ORDER_PRICE'][$key],                              			//product price [ HUF: integer | EUR, USD decimal 0.00 ]
        'vat' => $_GET['ORDER_VAT'][$key],                                     		//product tax rate [ in case of gross price: 0 ] (percent)
        'qty' => $_GET['ORDER_QTY'][$key]                                      		//product quantity [ integer ] 
    ));

    //Billing data
    $lu->setField("BILL_FNAME", $_GET['BILL_FNAME']);
    $lu->setField("BILL_LNAME", $_GET['BILL_LNAME']);
    $lu->setField("BILL_EMAIL", $_GET['BILL_EMAIL']); 
    $lu->setField("BILL_PHONE", $_GET['BILL_PHONE']);
    //$lu->setField("BILL_COMPANY", "Company name");          	//optional
    //$lu->setField("BILL_FISCALCODE", " ");                  	//optional
    $lu->setField("BILL_COUNTRYCODE",  $_GET['BILL_COUNTRYCODE']);
    $lu->setField("BILL_STATE",  $_GET['BILL_STATE']);
    $lu->setField("BILL_CITY",  $_GET['BILL_CITY']); 
    $lu->setField("BILL_ADDRESS",  $_GET['BILL_ADDRESS']); 
    $lu->setField("BILL_ADDRESS2",  $_GET['BILL_ADDRESS2']);    //optional
    $lu->setField("BILL_ZIPCODE",  $_GET['BILL_ZIPCODE']); 
            
    //Delivery data
    $lu->setField("DELIVERY_FNAME", "Tester"); 
    $lu->setField("DELIVERY_LNAME", "SimplePay"); 
    //$lu->setField("DELIVERY_EMAIL", ""); 						//optional
    $lu->setField("DELIVERY_PHONE", "36201234567"); 
    $lu->setField("DELIVERY_COUNTRYCODE", "HU");
    $lu->setField("DELIVERY_STATE", "State");
    $lu->setField("DELIVERY_CITY", "City");
    $lu->setField("DELIVERY_ADDRESS", "First line address"); 
    //$lu->setField("DELIVERY_ADDRESS2", "Second line address");//optional
    $lu->setField("DELIVERY_ZIPCODE", "1234"); 
    
    /*
     * Generate fields and print form
     * In the test environment no need to use it because it will be handled in HTML demo page 
     * Must have to use it in your environment
     */     
         
    $display = $lu->createHtmlForm('SimplePayForm', 'auto', PAYMENT_BUTTON);   // format: link, button, auto (auto is redirects to payment page immediately )
	$lu->errorLogger(); 
	if ($lu->debug_liveupdate_page) {
	    print "<pre>";
		print $lu->getDebugMessage();
		print "</pre>";
		exit; 		
	}
	if (count($lu->errorMessage) > 0) {
	    print "<pre>";
		print $lu->getErrorMessage();
		print "</pre>";
		exit; 
	} 
	echo $display;
 

?>

