<?php
    require_once 'sdk/config.php';
    require_once 'sdk/SimplePayment.class.php';
	
    $orderCurrency = 'HUF';
	$testOrderId = str_replace(array('.', ':'), "", $_SERVER['SERVER_ADDR']) . @date("U", time()) . rand(1000, 9999);
	
    $lu = new SimpleLiveUpdate($config, $orderCurrency);       	
    $lu->setField("ORDER_REF", $testOrderId);       
	
    $lu->addProduct(array(
        'name' => 'Lorem 1',                            			//product name [ string ]
        'code' => 'sku0002',                            			//merchant systemwide unique product ID [ string ]
        'info' => 'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP',     					//product description [ string ]
        'price' => 406,                              				//product price [ HUF: integer | EUR, USD decimal 0.00 ]
        'vat' => 0,                                     			//product tax rate [ in case of gross price: 0 ] (percent)
        'qty' => 1                                      			//product quantity [ integer ] 
    ));
	
	//Billing data
    $lu->setField("BILL_FNAME", "Tester");
    $lu->setField("BILL_LNAME", "SimplePay Nogui");
    $lu->setField("BILL_EMAIL", "sdk_test@otpmobil.com"); 
    $lu->setField("BILL_PHONE", "36201234567");
    //$lu->setField("BILL_COMPANY", "Company name");          		// optional
    //$lu->setField("BILL_FISCALCODE", " ");                  		// optional
    $lu->setField("BILL_COUNTRYCODE", "HU");
    $lu->setField("BILL_STATE", "State");
    $lu->setField("BILL_CITY", "City"); 
    $lu->setField("BILL_ADDRESS", 'First line address'); 
    //$lu->setField("BILL_ADDRESS2", "Second line address");      	// optional
    $lu->setField("BILL_ZIPCODE", "1234");          
	
    //Delivery data
    $lu->setField("DELIVERY_FNAME", "Tester"); 
    $lu->setField("DELIVERY_LNAME", "SimplePay Nogui"); 
    $lu->setField("DELIVERY_EMAIL", ""); 
    $lu->setField("DELIVERY_PHONE", "36201234567"); 
    $lu->setField("DELIVERY_COUNTRYCODE", "HU");
    $lu->setField("DELIVERY_STATE", "State");
    $lu->setField("DELIVERY_CITY", "City");
    $lu->setField("DELIVERY_ADDRESS", "First line address"); 
    //$lu->setField("DELIVERY_ADDRESS2", "Second line address");  	// optional
    $lu->setField("DELIVERY_ZIPCODE", "1234"); 
	
    $display = $lu->createHtmlForm('SimplePayForm', 'button', 'Start Payment');   // format: link, button, auto (auto is redirects to payment page immediately )
	$lu->errorLogger(); 	
	echo $display;
?>
