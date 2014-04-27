<?php
/**
 * Encapsulates the settings API for Bulk Delete Plugin
 *
 * @package    Bulk_Delete
 * @subpackage Settings
 * @author     Sudar
 * @since      5.0
 */
class BD_Settings {
    /**
     * Register settings used by the plugin
     *
     * @since 5.0
     * @static
     */
    public static function create_settings() {
        $bd = BULK_DELETE();

        register_setting(
            $bd::SETTING_OPTION_GROUP,                       // Option group
            $bd::SETTING_OPTION_NAME,                        // Option name
            array( 'BD_Settings', 'check_license' ) // Sanitize
        );

        add_settings_section(
            $bd::SETTING_SECTION_ID,                  // ID
            __( 'Add Addon License', 'bulk-delete' ), // Title
            '__return_null',                          // Callback
            $bd::ADDON_PAGE_SLUG                      // Page
        );

        /**
         * Runs just after registering license form fields
         *
         * This action is primarily for adding more fields to the license form
         * @since 5.0
         */
        do_action( 'bd_license_field' );
    }

    /**
     * Callback for sanitizing settings
     *
     * @since 5.0
     * @static
     */
    public static function check_license( $input ) {
        /**
         * Filter license form inputs
         *
         * @since 5.0
         */
        return apply_filters( 'bd_license_input', $input );
    }
}

// hooks
add_action( 'admin_init', array( 'BD_Settings', 'create_settings' ), 100 );
?>
