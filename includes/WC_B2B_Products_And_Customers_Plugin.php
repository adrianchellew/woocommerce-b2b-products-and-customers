<?php

/**
 * The main plugin class
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit(); 
}

final class WC_B2B_Products_And_Customers_Plugin
{
    // Properties
    private string $plugin_version;
    private string $plugin_name;
    private string $admin_meta_box_content_template;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plugin_version = defined( 'WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_NAME' ) ? WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_NAME : 'woocommerce-b2b-products-and-customers';
        $this->plugin_name = defined( 'WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_VERSION' ) ? WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_VERSION : '1.0.0';
        $this->admin_meta_box_content_template = WC_B2B_PRODUCTS_AND_CUSTOMERS_PLUGIN_PATH . 'templates/admin/admin-meta-box-content.php';
    }

    /**
     * Add user roles
     */
    private function wcb2b_add_user_roles() : void
    {
        add_role(
            'wcb2b_customer', // System name of the role.
            __( 'B2B Customer' ), // Display name of the role.
            [
                'read' => true
            ]
        );
    }

    /**
     *  Display custom meta box HTML
     */
    public function wcb2b_render_meta_box_content( object $post ) : void
    {
        $value = get_post_meta( $post->ID, '_wcb2b_visibility', true );

        include $this->admin_meta_box_content_template;
    }
    
    /**
     *  Add custom meta boxes
     */
    public function wcb2b_add_meta_boxes() : void
    {
        add_meta_box(
            'wcb2b_meta_box',
            __( 'B2B Customer Settings', $this->plugin_name ),
            [ $this, 'wcb2b_render_meta_box_content' ],
            [ 'page', 'product' ],
            'side'
        );
    }

    /**
     *  Save custom meta box data
     */
    public function wcb2b_save_post_meta( int $post_id ) : void
    {
        if ( isset( $_POST['wcb2b_save_visibility_field_nonce'] )
            && wp_verify_nonce( $_POST['wcb2b_save_visibility_field_nonce'], 'wcb2b_save_visibility_field_nonce_action')
        ) {
            if ( isset( $_POST['wcb2b_visibility_field'] ) ) {
                update_post_meta(
                    $post_id,
                    '_wcb2b_visibility',
                    $_POST['wcb2b_visibility_field']
                );
            }
        }
    }

    /**
     *  Hide relevant products from non-B2B cusstomers
     */
    public function wcb2b_product_is_visible( bool $visible, string $product_id ) : bool
    {
        $wcb2b_visibility_enabled = get_post_meta( $product_id, '_wcb2b_visibility', true );

        if ( ! empty( $wcb2b_visibility_enabled ) &&  $wcb2b_visibility_enabled === 'on' ) {
            if ( ! is_user_logged_in() ) {
                $visible = false;
            } elseif ( is_user_logged_in() )  {
                $current_user = wp_get_current_user();
                $user_roles = $current_user->roles;

                if ( empty( $user_roles ) || in_array( 'customer', $user_roles ) ) {
                    if ( ! in_array( 'wcb2b_customer', $user_roles ) ) {
                        $visible = false;
                    }
                }
            }
        }
        return $visible;
    }

    /**
     *  Hide relevant pages from non-B2B customers
     */
    public function wcb2b_template_redirect() : void
    {   
        global $post;

        if ( is_page() || is_product() ) {
            $wcb2b_visibility_enabled = get_post_meta( $post->ID, '_wcb2b_visibility', true );

            if ( ! empty( $wcb2b_visibility_enabled ) &&  $wcb2b_visibility_enabled === 'on') {

                if ( ! is_user_logged_in() ) {
                    wp_redirect( home_url() );
                    exit();
                } elseif ( is_user_logged_in() )  {
                    $current_user = wp_get_current_user();
                    $user_roles = $current_user->roles;

                    if ( empty($user_roles) || in_array('customer', $user_roles ) ) {
                        if ( ! in_array('wcb2b_customer', $user_roles ) ) {
                            wp_redirect( home_url() );
                            exit();
                        }
                    }
                }
            }
        }
    }

    /**
     *  On show relevant menu items to B2B customers
     */
    public function wcb2b_setup_nav_menu_item( object $menu_item ) : object
    {
        if ( isset( $menu_item->post_type ) ) {
            
            if ( 'post_type' === $menu_item->type ) {
                $original_object = get_post( $menu_item->object_id );
                
                if ( $original_object->post_type === 'products' || $original_object->post_type  === 'page') {
                    $wcb2b_visibility_enabled = get_post_meta( $original_object->ID, '_wcb2b_visibility', true );
                    
                    if ( ! empty( $wcb2b_visibility_enabled ) &&  $wcb2b_visibility_enabled === 'on') {
                        
                        if ( ! is_user_logged_in() ) {
                            $menu_item->_invalid = true;
                        } elseif ( is_user_logged_in() )  {
                            $current_user = wp_get_current_user();
                            $user_roles = $current_user->roles;

                            if ( empty( $user_roles ) || in_array( 'customer', $user_roles ) ) {
                                if ( ! in_array( 'wcb2b_customer', $user_roles ) ) {
                                    $menu_item->_invalid = true;
                                }
                            }

                        }
                    }

                }
                
            }
        }
        return $menu_item;
    }

    /**
     * Action hooks
     */
    private function wcb2b_add_actions() : void
    {
        add_action( 'add_meta_boxes', [ $this, 'wcb2b_add_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'wcb2b_save_post_meta' ] );
        add_action( 'template_redirect', [ $this, 'wcb2b_template_redirect' ] );
    }

    /**
     * Filter hooks
     */
    private function wcb2b_add_filters() : void
    {
        add_filter( 'woocommerce_product_is_visible', [ $this, 'wcb2b_product_is_visible' ], PHP_INT_MAX, 2 );
        add_filter( 'wp_setup_nav_menu_item', [ $this, 'wcb2b_setup_nav_menu_item' ] );
    }

    /**
     * Initialiser
     */
    public function wcb2b_run() : void
    {
        $this->wcb2b_add_user_roles();
        $this->wcb2b_add_actions();
        $this->wcb2b_add_filters();
    }
}
