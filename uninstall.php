<?php
/**
 * Code to run when the plugin is uninstalled
 */

// Exit if plugin has not been installed
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Include the uninstaller class file
require_once( plugin_dir_path( __FILE__ ) . 'includes/WC_B2B_Products_And_Customers_Uninstaller.php' );

// Instantiate the uninstaller class
$uninstaller = new WC_B2B_Products_And_Customers_Uninstaller();

// Start the execution of the plugin
$uninstaller->wcb2b_run();

// Clear any cached data that has been removed
wp_cache_flush();