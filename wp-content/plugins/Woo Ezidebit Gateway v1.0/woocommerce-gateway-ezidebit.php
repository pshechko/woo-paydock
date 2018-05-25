<?php
/*
Plugin Name: WooCommerce Ezidebit Gateway
Plugin URI: 
Description: A simple, secure( Fully PCI Level 1 Compliant ) and lightweight credit card payment for Ezidebit to WooCommerce.
Version: 1.0
Author: Danryl Carpio
Author URI:
*/

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) exit;

// load WooCommerce Checker
if( ! class_exists( 'WooChecker' ) )
	require_once( 'includes/woocommerce-plugin-checker.php' );

// initialize WooChecker Class
$woo_checker = new WooChecker();

/**
* Check if WooCommerce is active
*/
if ( $woo_checker->is_woocommerce_active() ) {

	/**
	* Ezidebit Integration To WooCommerce
	*/
	function wc_ezidebit_init_gateway() {

		// Plugin PATH and URL
		define('WC_EZIDEBIT_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
		define('WC_EZIDEBIT_PLUGIN_URL', plugin_dir_url( __FILE__ ));

		// ezidebit gateway
		require_once('includes/woocommerce-gateway-ezidebit-class.php');
	}

	add_action( 'init', 'wc_ezidebit_init_gateway' );

	/**
	 * Add Ezidebit Gateway to WooCommerce
	 */
	function wc_ezidebit_add_to_gateways( $gateways ) {
		$gateways[] = 'WC_Gateway_Ezidebit';
		return $gateways;
	}

	add_filter( 'woocommerce_payment_gateways', 'wc_ezidebit_add_to_gateways' );

	/**
	 * Adds ezidebit gateway plugin page links
	 */
	function wc_ezidebit_gateway_plugin_links( $links ) {

		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ezidebit' ) . '">' . __( 'Configure', 'wc-gateway-ezidebit' ) . '</a>'
		);

		return array_merge( $plugin_links, $links );
	}

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_ezidebit_gateway_plugin_links' );
}
