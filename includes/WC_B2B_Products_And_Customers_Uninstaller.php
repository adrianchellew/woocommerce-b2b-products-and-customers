<?php

/**
 * The plugin uninstaller class
**/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit(); 
}

final class WC_B2B_Products_And_Customers_Uninstaller
{
    // Remove the 'b2b_customer' user role and capability
    private function wcb2b_remove_user_roles_and_capabilities() : void
    {
        $b2b_customer = get_role('wcb2b_customer');
        $b2b_customer->remove_cap('wcb2b_customer');
        remove_role( 'wcb2b_customer' );
    }

    // Remove any existing meta data created by the plugin
    private function wcb2b_remove_custom_meta_data() : void
    {
        delete_metadata( 'post', 0, '_wcb2b_visibility', '', true );
    }

    // Run all uninstall methods
    public function wcb2b_run() : void
    {
        $this->wcb2b_remove_user_roles_and_capabilities();
        $this->wcb2b_remove_custom_meta_data();
    }

}