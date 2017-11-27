<?php
/*
 * Plugin Name: Woocommerce Payment Gateway for SimplepayOTP
 * Plugin URI:  http://www.wooh.hu/
 * Description: Adds SimplepayOTP Payment Gateway to Woocommerce e-commerce plugin
 * Author:      KornélKiss
 * Author URI:  http://www.korrnel.hu
 * Version:     1.0
 * Text Domain: woocommerce-simplepayotpbank
 * Domain Path: /languages/
 * License:     GPLv2
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

add_action('plugins_loaded', 'init_kissko_otpbank', 0);

function init_kissko_otpbank() {

    if ((!class_exists('WC_Payment_Gateway'))) {
        return;
    }

    include_once "nogui_class.php";

    add_filter('woocommerce_payment_gateways', 'wooh_add_kissko_otpbank_gateway');
    function wooh_add_kissko_otpbank_gateway( $methods ) {
        $methods[] = 'WC_Gateway_SIMPLEOTPBank';
        return $methods;
    }
}

// Add custom action links
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ),'kissko_otpbank_action_links');

function kissko_otpbank_action_links($links) {
    $plugin_links = array('<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout').'">'.__('Settings', 'otpbank').'</a>',);
    return array_merge($plugin_links,$links);
}

?>
