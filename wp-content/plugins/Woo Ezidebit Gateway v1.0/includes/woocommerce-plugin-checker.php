<?php 

	// Exit if accessed directly
	if ( ! defined( 'WPINC' ) ) exit;

	/**
	*  Woocommerce Checker
	*/
	class WooChecker
	{
		/**
		 * is_woocommerce_active - Check if WooCommerce is active.
		 * @return  bool
		 */
		public function is_woocommerce_active() {

			// get network active plugins for multi site
			$network_active = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );

			// get wordpress site active plugins
			$site_active = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );

			// checking woocommerce plugin actived
			$active_plugins = ( is_multisite() ) ? array_merge( $network_active, $site_active ) : $site_active;
			foreach ( $active_plugins as $active_plugin ) {
				$active_plugin = explode( '/', $active_plugin );
				if ( isset( $active_plugin[1] ) && 'woocommerce.php' === $active_plugin[1] ) {
					return true;
				}
			}
			return false;
		}
	}

?>