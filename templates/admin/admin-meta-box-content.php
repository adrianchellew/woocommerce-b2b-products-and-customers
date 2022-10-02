<?php

/**
 * Admin meta box content template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit(); 
}

?>
<input type="checkbox" name="wcb2b_visibility_field" id="wcb2b-visibility" <?php echo ( ! empty( $value ) && $value === 'on' ) ? 'checked="checked"' : ''; ?>>
<label for="wcb2b-visibility">Only visible for B2B customers</label>
<?php wp_nonce_field( 'wcb2b_save_visibility_field_nonce_action', 'wcb2b_save_visibility_field_nonce' ); ?>