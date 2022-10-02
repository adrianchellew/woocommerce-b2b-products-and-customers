<?php

/**
 * Plugin Name: WooCommerce B2B Products and Customers
 * Description: Adds a WooCommerce B2B customer user role and user-role-specific visibility options to products and pages.
 * Version: 1.0.0
 * Author: Adrian Chellew
 * Author URI: https://github.com/adrianchellew
 * Text Domain: woocommerce-b2b-products-and-customers
 * Domain Path: /languages
 * Requires PHP: 7.2
 * 
 * WC tested up to: 6.9.0
 * 
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Define constants
define( 'WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_NAME', 'woocommerce-b2b-products-and-customers' );
define( 'WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_VERSION', '1.0.0' );
define( 'WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Include the main plugin class
require_once( WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_PATH . 'includes/WC_B2B_Products_And_Customers_Plugin.php' );

/**
 * Displays a "WooCommerce required" notice
 */ 
function wcb2b_woocommerce_required_notice() : void
{
	$message = sprintf(
		esc_html__( 'WooCommerce B2B Products and Customers requires %s to be installed and active.', WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_NAME ),
		'<a href="https://woocommerce.com" target="_blank">WooCommerce</a>'
	);
	
	echo '<div class="error"><p>' . $message . '</p></div>';
}

/**
 * The plugin init function
 */ 
function wcb2b_plugin_init() : void
{
	// Notify the user if WooCommerce is not active, else run the plugin
	if ( ! class_exists( 'woocommerce' ) ) {
		// Display the "WooCommerce required" notice
		add_action( 'admin_notices', 'wcb2b_woocommerce_required_notice' );
	} else {
		// Instantiate the plugin class
		$plugin = new WC_B2B_Products_And_Customers_Plugin();
		// Start the execution of the plugin
		$plugin->wcb2b_run();
	}
}

// Plugin init hook that calls the plugin init function after all other plugins have loaded
add_action( 'plugins_loaded', 'wcb2b_plugin_init' );