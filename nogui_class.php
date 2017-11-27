<?php

/* SimplepayOTPBank Payment Gateway Class */
class WC_Gateway_SIMPLEOTPBank extends WC_Payment_Gateway {


    var $operationLogNames = array(
        'fizetesiTranzakcio'            => 'fizetesiTranzakcio',
        'tranzakcioStatuszLekerdezes'   => 'tranzakcioStatuszLekerdezes',
        'kulcsLekerdezes'               => 'kulcsLekerdezes'
        );

    function __construct() {

        // hello
        $this->id                   = "kissko_otpbank";
        $this->method_title         = __( "SimplepayOTPBank", 'kissko-otpbank' );
        $this->method_description   = __("SimplepayOTPBank Payment Gateway Plug-in for WooCommerce",'kissko-otpbank');
        $this->title                = __( "SimplepayOTPBank", 'kissko-otpbank' );
        $this->icon                 = plugins_url() . "/nogui/sdk/simplepay_logo_240.png";
        $this->has_fields           = true;
        $this->init_form_fields();
        $this->init_settings();
        $this->log                  = new WC_Logger();
        $this->pos_id               = $this->get_option( 'shop_id' );
        $this->private_key          = $this->get_option( 'shop_key' );
        $this->shop_lang            = $this->get_option( 'shop_lang' );
        $this->shop_currency        = $this->get_option( 'shop_currency' );

        foreach ( $this->settings as $setting_key => $value ) {
            $this->$setting_key = $value;
        }
        if (is_admin()) {
          add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        add_action( 'woocommerce_api_WC_Gateway_SIMPLESimplepayOTPBank', array( $this, 'check_otp_response' ) );

    } // End __construct()

    public function check_otp_response() {
        require_once 'WebShopService.php';
        global $woocommerce;


        $tranzAzon      = $_REQUEST['tranzakcioAzonosito'];
        $order_id       = intval(substr($tranzAzon, 16));
        $customer_order = new WC_Order($order_id);

        $service    = new WebShopService();
        $response   = $service->tranzakcioStatuszLekerdezes($this->pos_id, $tranzAzon, 1, time() - 60*60*24, time() + 60*60*24, $this->private_key);

        if ($response) {

            $answer = $response->getAnswer();
            if ($response->isSuccessful() && $response->getAnswer() && count($answer->getWebShopFizetesAdatok()) > 0) {

                $fizetesAdatok = $answer->getWebShopFizetesAdatok();
                $tranzAdatok = current($fizetesAdatok);
        
                $this->log->add("otpbank", "Fizetes tranzakcio adat lekerdezes befejezve: " . $this->pos_id . " - " . $tranzAzon );
        
                $responseCode = $tranzAdatok->getPosValaszkod();

                if ($tranzAdatok->isSuccessful()) {
                    $this->log->add("otpbank", "Sikeres fizetes " . $this->pos_id . " - " . $tranzAzon );
        
                    $return_url = $this->get_return_url( $customer_order );
                    $customer_order->add_order_note( __( 'OTP payment completed', 'woocommerce' ) );
                    $woocommerce->cart->empty_cart();
                    $customer_order->payment_complete( $order_id );

                    wp_redirect( $return_url );
                } else if ("VISSZAUTASITOTTFIZETES" == $responseCode) {
                    $this->log->add("otpbank", "Rejected pament " . $this->pos_id . " - " . $tranzAzon );
                    $customer_order->add_order_note( __( 'Rejected payment', 'woocommerce' ) );
                    $customer_order->update_status( 'failed', "Rejected payment" );
                    $return_url = $this->get_return_url( $customer_order );
                    wp_redirect( $return_url );
                } else {
                    $this->log->add("otpbank", "Sikertelen fizetes " . $this->pos_id . " - " . $tranzAzon );
                    $customer_order->add_order_note( __( 'Unsuccessful payment', 'woocommerce' ) );
                    $customer_order->update_status( 'failed', "Unsuccessful payment" );
                    $return_url = $this->get_return_url( $customer_order );
                    wp_redirect( $return_url );
                }
            } else {
                $this->log->add("otpbank", "Nincs valasz " . $this->pos_id . " - " . $tranzAzon );
                $customer_order->add_order_note( __( 'Nem jott valasz az OTP servertol', 'woocommerce' ) );
                $customer_order->update_status( 'failed', "Unsuccessful payment - failed to communicate with the server" );
                $return_url = $this->get_return_url( $customer_order );
                wp_redirect( $return_url );
            }
        }

    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __('Enable / Disable', 'kissko-otpbank'),
                'label'     => __('Enable this payment gateway','kissko-otpbank'),
                'type'      => 'checkbox',
                'default'   => 'no',
                ),
            'title' => array(
                'title'     => __('Title', 'kissko-otpbank'),
                'type'      => 'text',
                'desc_tip'  => __( 'Payment title the customer will see during the checkout process.', 'kissko-otpbank' ),
                'default'   => __( 'Pay with OTP', 'kissko-otpbank' ),
                ),
            'description' => array(
                'title'     => __( 'Description', 'kissko-otpbank' ),
                'type'      => 'textarea',
                'desc_tip'  => __( 'Payment description the customer will see during the checkout process.', 'kissko-otpbank' ),
                'default'   => __( 'Pay securely using OTP Bank', 'kissko-otpbank' ),
                'css'       => 'max-width:350px;'
                ),
            'shop_id' => array(
                'title'     => __( 'SimplepayOTPBank ShopID', 'kissko-otpbank' ),
                'type'      => 'text',
                'desc_tip'  => __( 'This is the OTPbank ShopID.', 'kissko-otpbank' ),
                'default'   => '#02299991',
                ),
            'shop_key' => array(
                'title'     => __( 'SimplepayOTPBank Key', 'kissko-otpbank' ),
                'type'      => 'text',
                'desc_tip'  => __( 'This is the key for the shop.', 'kissko-otpbank' ),
                ),
            'shop_lang' => array(
                'title'       => __('Shop language', 'kissko-otpbank'),
                'type'        => 'select',
                'description' => __('Please choose a language', 'kissko-otpbank'),
                'options'     => array(
                    'hu'    => __('Hungarian', 'kissko-otpbank'),
                    'en'    => __('English', 'kissko-otpbank')
                    ),
                'desc_tip'    => true,
                ),
            'shop_currency' => array(
                'title'       => __('Shop currency', 'kissko-otpbank'),
                'type'        => 'select',
                'description' => __('Please choose a currency', 'kissko-otpbank'),
                'options'     => array(
                    'HUF'   => __('HUF', 'kissko-otpbank'),
                    'EUR'   => __('EUR', 'kissko-otpbank'),
                    'USD'   => __('USD', 'kissko-otpbank')
                    ),
                'desc_tip'    => true,
                )
            );
    }

    public function process_payment( $order_id ) {

      	$order = wc_get_order( $order_id );
		
	// Mark as on-hold (we're awaiting the payment)
	// $order->update_status( 'on-hold', __( 'Awaiting offline payment', 'wc-gateway-offline' ) );
	
	$ORDER_PCODE  =array();
	$ORDER_PINFO =array();
	$ORDER_PRICE =array();
	$ORDER_QTY =array();
	$ORDER_VAT  =array();
	$ORDER_PNAME =array();


	// Iterating through each "line" items in the order
	foreach ($order->get_items() as $item_id => $item_data) {

	    // Get an instance of corresponding the WC_Product object
	    	$product = $item_data->get_product();
		$product_name = $product->get_name(); // Get the product name

		$item_quantity = $item_data->get_quantity(); // Get the item quantity

		$item_total = $item_data->get_total(); // Get the item line total

	    // Displaying this data (to check)
	$ORDER_PCODE[]  = urlencode($item_id);
	$ORDER_PINFO[] = urlencode($product_name);
	$ORDER_PRICE[] = urlencode(number_format($item_total,0,',',''));
	$ORDER_QTY[] =urlencode($item_quantity);
	$ORDER_VAT[]  = urlencode('0');
	$ORDER_PNAME[] = urlencode($product_name);

	}


// Iterating through order shipping items

foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
  $ORDER_PNAME[]           = $shipping_item_obj->get_method_id();
//    $order_item_type           = $shipping_item_obj->get_type();
    $ORDER_PINFO[]     = $shipping_item_obj->get_method_title();
    $ORDER_PCODE[]        = $shipping_item_obj->get_type(); // The method ID
    $ORDER_PRICE[]     = $shipping_item_obj->get_total();
    $ORDER_VAT[] = 0;
    $ORDER_QTY[]=1;
}

	$fields = array(  

	'PRICES_CURRENCY' => $this->get_option( 'shop_currency' ),
	'ORDER_SHIPPING' => urlencode('0'),
	'DISCOUNT'  => urlencode('0'),
	'PAY_METHOD'  => urlencode('CCVISAMC'),
	'LANGUAGE'  => urlencode('HU'),
	'ORDER_TIMEOUT'  => urlencode('300'),
	'BACK_REF'  => urlencode(substr($this->get_return_url($order),7)),
	'TIMEOUT_URL'  => urlencode(substr($order->get_cancel_endpoint(),7)),
	'BILL_FNAME'  => urlencode($_POST['billing_first_name']),
	'BILL_LNAME'  => urlencode($_POST['billing_last_name']),
	'BILL_EMAIL'  => urlencode($_POST['billing_email']),
	'BILL_PHONE'  => urlencode($_POST['billing_phone']),
	'BILL_ADDRESS'  => urlencode($_POST['billing_address_1']),
	'BILL_ADDRESS2'  => urlencode($_POST['billing_address_2']),
	'BILL_ZIPCODE'  => urlencode($_POST['billing_postcode']),
	'BILL_CITY'  => urlencode($_POST['billing_city']),
	'BILL_STATE'  => urlencode($_POST['billing_state']),
	'BILL_COUNTRYCODE' => urlencode($_POST['billing_country']),
	'DELIVERY_FNAME'  => urlencode($_POST['shipping_first_name']),
	'DELIVERY_LNAME'  => urlencode($_POST['shipping_last_name']),
	'DELIVERY_PHONE'  => urlencode($_POST['billing_phone']),
	'DELIVERY_ADDRESS'  => urlencode($_POST['shipping_address_1']),
	'DELIVERY_ADDRESS2'  => urlencode($_POST['shipping_address_2']),
	'DELIVERY_ZIPCODE'  => urlencode($_POST['shipping_postcode']),
	'DELIVERY_CITY' => urlencode($_POST['shipping_city']),
	'DELIVERY_STATE' => urlencode($_POST['shipping_state']),
	'DELIVERY_COUNTRYCODE' => urlencode($_POST['shipping_postcode'])

	);
	$fields_string="";
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	foreach($ORDER_PCODE  as $key=>$value) { $fields_string .= 'ORDER_PCODE[]='.$value.'&'; }
	foreach($ORDER_PINFO  as $key=>$value) { $fields_string .= 'ORDER_PINFO[]='.$value.'&'; }
	foreach($ORDER_PRICE  as $key=>$value) { $fields_string .= 'ORDER_PRICE[]='.$value.'&'; }
	foreach($ORDER_QTY  as $key=>$value) { $fields_string .= 'ORDER_QTY[]='.$value.'&'; }
	foreach($ORDER_VAT  as $key=>$value) { $fields_string .= 'ORDER_VAT[]='.$value.'&'; }
	foreach($ORDER_PNAME  as $key=>$value) { $fields_string .= 'ORDER_PNAME[]='.$value.'&'; }


	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	  return array(
              'result' 	    => 'success',
	             'redirect'	=> "/wp-content/plugins/nogui/index.php?".$fields_string
              );
  
    }


    public function validate_fields() {
        return true;
    }

} // End of SimplepayOTPBank

?>
