<?php
/**
 * Addon license related functions
 *
 * @package    Bulk_Delete
 * @subpackage addon
 * @author     Sudar
 * @since      5.0
 */
class BD_License {
    /**
     * Output addon page content
     *
     * @since 5.0
     * @static
     */
    public static function display_addon_page() {
        if( !class_exists( 'WP_List_Table' ) ){
            require_once( ABSPATH . WPINC . '/class-wp-list-table.php' );
        }

        if ( !class_exists( 'License_List_Table' ) ) {
            require_once Bulk_Delete::$PLUGIN_DIR . '/include/class-license-list-table.php';
        }

        $license_list_table = new License_List_Table();
        $license_list_table->prepare_items();
?>
        <div class="wrap">
            <h2><?php _e( 'Addon Licenses', 'bulk-delete' );?></h2>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
<?php
            $license_list_table->display();
            do_action( 'bd_license_form' );
            BD_License::display_available_addon_list();
?>
            </form>
        </div>
<?php
        /**
         * Runs just before displaying the footer text in the "Addon" admin page.
         *
         * This action is primarily for adding extra content in the footer of "Addon" admin page.
         *
         * @since 5.0
         */
        do_action( 'bd_admin_footer_addon_page' );
    }

    /**
     * Display License form
     *
     * @since 5.0
     * @static
     */
    public static function display_activate_license_form() {
        $bd = BULK_DELETE();
        if ( isset( $bd->display_activate_license_form ) && TRUE == $bd->display_activate_license_form ) {
            // This prints out all hidden setting fields
            settings_fields( Bulk_Delete::SETTING_OPTION_GROUP );
            do_settings_sections( Bulk_Delete::ADDON_PAGE_SLUG );
            submit_button( __( 'Activate License', 'bulk-delete' ) );
        }
    }

    /**
     * Check if an addon has a valid license or not
     *
     * @since  5.0
     * @static
     * @param  string $addon_name Addon Name
     * @param  string $addon_code Addon short Name
     * @return bool   True if addon has a valid license, False otherwise
     */
    public static function has_valid_license( $addon_name, $addon_code ) {
        $key = Bulk_Delete::LICENSE_CACHE_KEY_PREFIX . $addon_code;
        $license_data = get_option( $key, FALSE );

        if ( ! $license_data ) {
            // if data about license is not present, then fetch it.
            // ideally this should not happen
            $licenses = get_option( Bulk_Delete::SETTING_OPTION_NAME );
            if ( is_array( $licenses ) && key_exists( $addon_code, $licenses ) ) {
                $license_data = BD_EDD_API_Wrapper::check_license( $addon_name, $licenses[ $addon_code ] );
                update_option( $key, $license_data );
            }
        }

        // TODO Encapsulate below code into a separate function
        if ( $license_data && is_array( $license_data ) && key_exists( 'validity', $license_data ) ) {
            if ( 'valid' == $license_data['validity'] ) {
                if ( strtotime( 'now' ) < strtotime( $license_data['expires'] ) ) {
                    return TRUE;
                } else {
                    $license_data['validity'] = 'expired';
                    update_option( $key, $license_data );
                }
            }
        }

        return FALSE;
    }

    /**
     * Get the list of all licenses information to be displayed in the license page
     *
     * @since 5.0
     * @static
     * @return array $license_data License information
     */
    public static function get_licenses() {
        $licenses = get_option( Bulk_Delete::SETTING_OPTION_NAME );
        $license_data = array();

        if ( is_array( $licenses ) ) {
            foreach ( $licenses as $addon_code => $license ) {
                $license_data[ $addon_code ] = self::get_license( $addon_code, $license );
            }
        }

        return $license_data;
    }

    /**
     * Retrieve license information about an addon
     *
     * @since  5.0
     * @static
     * @param  string $addon_code   Addon short name
     * @return object $license_data License information
     */
    public static function get_license( $addon_code ) {
        $bd = BULK_DELETE();
        $key = Bulk_Delete::LICENSE_CACHE_KEY_PREFIX . $addon_code;
        $license_data = get_option( $key, FALSE );

        if ( $license_data && is_array( $license_data ) && key_exists( 'validity', $license_data ) ) {
            if ( 'valid' == $license_data['validity'] ) {
                if ( strtotime( 'now' ) < strtotime( $license_data['expires'] ) ) {
                    // valid license
                } else {
                    $license_data['validity'] = 'expired';
                    update_option( $key, $license_data );
                }
            }
        }

        return $license_data;
    }

    /**
     * Get license code of an addon
     *
     * @since 5.0
     * @static
     * @param string $addon_code Addon code
     * @return bool|string License code of the addon, False otherwise
     */
    public static function get_license_code( $addon_code ) {
        $licenses = get_option( Bulk_Delete::SETTING_OPTION_NAME );

        if ( is_array($licenses ) && key_exists( $addon_code, $licenses ) ) {
            return $licenses[ $addon_code ];
        }
        else {
            return FALSE;
        }
    }

    /**
     * Deactivate license
     *
     * @since 5.0
     * @static
     */
    public static function deactivate_license() {
        if ( check_admin_referer( 'bd-deactivate-license', 'bd-deactivate-license-nonce' ) ) {
            $msg          = array( 'msg' => '', 'type' => 'error' );
            $addon_code   = $_GET['addon-code'];
            $license_data = self::get_license( $addon_code );

            $license      = $license_data['license'];
            $addon_name   = $license_data['addon-name'];

            $deactivated  = BD_EDD_API_Wrapper::deactivate_license( $addon_name, $license );

            if ( $deactivated ) {
                self::delete_license_from_cache( $addon_code );
                $msg['msg']  = sprintf( __( 'The license key for "%s" addon was successfully deactivated', 'bulk-delete' ), $addon_name );
                $msg['type'] = 'updated';

            } else {
                self::validate_license( $addon_code, $addon_name );
                $msg['msg'] = sprintf( __( 'There was some problem while trying to deactivate license key for "%s" addon. Kindly try again', 'bulk-delete' ), $addon_name );
            }

            add_settings_error(
                Bulk_Delete::ADDON_PAGE_SLUG,
                'license-deactivation',
                $msg['msg'],
                $msg['type']
            );
        }
    }

    /**
     * Delete license
     *
     * @since 5.0
     * @static
     */
    public static function delete_license() {
        if ( check_admin_referer( 'bd-deactivate-license', 'bd-deactivate-license-nonce' ) ) {
            $msg          = array( 'msg' => '', 'type' => 'updated' );
            $addon_code   = $_GET['addon-code'];

            self::delete_license_from_cache( $addon_code );

            $msg['msg']  = __( 'The license key was successfully deleted', 'bulk-delete' );

            add_settings_error(
                Bulk_Delete::ADDON_PAGE_SLUG,
                'license-deleted',
                $msg['msg'],
                $msg['type']
            );
        }
    }

    /**
     * Delete license information from cache
     *
     * @since 5.0
     * @static
     * @param string $addon_code Addon code
     */
    private static function delete_license_from_cache( $addon_code ) {
        $key = Bulk_Delete::LICENSE_CACHE_KEY_PREFIX . $addon_code;
        delete_option( $key );

        $licenses = get_option( Bulk_Delete::SETTING_OPTION_NAME );

        if ( is_array( $licenses ) && key_exists( $addon_code, $licenses ) ) {
            unset( $licenses[ $addon_code ] );
        }
        update_option( Bulk_Delete::SETTING_OPTION_NAME, $licenses );
    }

    /*
     * Activate license
     *
     * @since  5.0
     * @static
     * @param  string $addon_name Addon name
     * @param  string $addon_code Addon code
     * @param  string $license    License code
     * @return bool   $valid      True if valid, False otherwise
     */
    public static function activate_license( $addon_name, $addon_code, $license ) {
        $license_data = BD_EDD_API_Wrapper::activate_license( $addon_name, $license );
        $valid        = FALSE;
        $msg          = array(
            'msg'  => sprintf( __( 'There was some problem in contacting our store to activate the license key for "%s" addon', 'bulk-delete' ), $addon_name ),
            'type' => 'error'
        );

        if ( $license_data && is_array( $license_data ) && key_exists( 'validity', $license_data ) ) {
            if ( 'valid' == $license_data['validity'] ) {
                $key = Bulk_Delete::LICENSE_CACHE_KEY_PREFIX . $addon_code;
                $license_data['addon-code'] = $addon_code;
                update_option( $key, $license_data );

                $msg['msg']  = sprintf( __( 'The license key for "%s" addon was successfully activated. The addon will get updates automatically till the license key is valid.', 'bulk-delete' ), $addon_name );
                $msg['type'] = 'updated';
                $valid = TRUE;
            } else {
                if ( key_exists( 'error', $license_data ) ) {
                    switch( $license_data['error'] ) {

                        case 'no_activations_left':
                            $msg['msg'] = sprintf( __( 'The license key for "%s" addon doesn\'t have any more activations left. Kindly buy a new license.', 'bulk-delete' ), $addon_name );
                            break;

                        case 'revoked':
                            $msg['msg'] = sprintf( __( 'The license key for "%s" addon is revoked. Kindly buy a new license.', 'bulk-delete' ), $addon_name );
                            break;

                        case 'expired':
                            $msg['msg'] = sprintf( __( 'The license key for "%s" addon has expired. Kindly buy a new license.', 'bulk-delete' ), $addon_name );
                            break;

                        default:
                            $msg['msg'] = sprintf( __( 'The license key for "%s" addon is invalid', 'bulk-delete' ), $addon_name );
                            break;
                    }
                }
            }
        }

        add_settings_error(
            Bulk_Delete::ADDON_PAGE_SLUG,
            'license-activation',
            $msg['msg'],
            $msg['type']
        );

        if ( !$valid && isset( $key ) ) {
            delete_option( $key );
        }
        return $valid;
    }

    /**
     * Validate the license for the given addon
     *
     * @since 5.0
     * @static
     * @param  string $addon_name Addon name
     * @param  string $addon_code Addon code
     */
    public static function validate_license( $addon_code, $addon_name ) {
        $key = Bulk_Delete::LICENSE_CACHE_KEY_PREFIX . $addon_code;

        $licenses = get_option( Bulk_Delete::SETTING_OPTION_NAME );
        if ( is_array( $licenses ) && key_exists( $addon_code, $licenses ) ) {
            $license_data = BD_EDD_API_Wrapper::check_license( $addon_name, $licenses[ $addon_code ] );
            if ( $license_data ) {
                $license_data['addon-code'] = $addon_code;
                $license_data['addon-name'] = $license_data['item_name'];
                update_option( $key, $license_data );
            } else {
                delete_option( $key );
            }
        }

        if ( $license_data && is_array( $license_data ) && key_exists( 'validity', $license_data ) ) {
            if ( 'valid' == $license_data['validity'] ) {
                if ( strtotime( 'now' ) > strtotime( $license_data['expires'] ) ) {
                    $license_data['validity'] = 'expired';
                    update_option( $key, $license_data );
                }
            }
        }
    }

    /**
     * Display information about all available addons
     *
     * @since 5.0
     * @static
     */
    public static function display_available_addon_list() {

        echo '<p>';
        _e('The following are the list of pro addons that are currently available for purchase.', 'bulk-delete');
        echo '</p>';

        echo '<ul style="list-style:disc; padding-left:35px">';

        echo '<li>';
        echo '<strong>', __('Delete posts by custom field', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to delete posts based on custom fields', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-custom-field/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-cf">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete posts by title', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete posts based on title', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-title/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-ti">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete posts by duplicate title', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete posts based on duplicate title', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-duplicate-title/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-dti">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete posts by attachment', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete posts based on whether it contains attachment or not', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-attachment/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-p-at">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete posts by user role', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete posts based on user role', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-user-role/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-ur">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete from trash', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete posts and pages from trash', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-from-trash/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-th">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Scheduler Email', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Sends an email every time a Bulk WP scheduler runs', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/scheduler-email/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-se">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __('Scheduler for deleting Posts by Category', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to schedule auto delete of posts based on category', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-category/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-sc">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __('Scheduler for deleting Posts by Tag', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to schedule auto delete of posts based on tag', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-tag/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-st">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __('Scheduler for deleting Posts by Custom Taxonomy', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to schedule auto delete of posts based on custom taxonomy', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-taxonomy/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-stx">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __('Scheduler for deleting Posts by Custom Post Type', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to schedule auto delete of posts based on custom post type', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-post-type/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-spt">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __('Scheduler for deleting Posts by Post Status', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to schedule auto delete of posts based on post status like drafts, pending posts, scheduled posts etc.', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-sps">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __('Scheduler for deleting Pages by Status', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to schedule auto delete pages based on status', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/scheduler-for-deleting-pages-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-sp">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __('Scheduler for deleting Users by User Role', 'bulk-delete'), '</strong>', ' - ';
        echo __('Adds the ability to schedule auto delete of users based on user role', 'bulk-delete');
        echo ' <a href = "http://bulkwp.com/addons/scheduler-for-deleting-users-by-role/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-u-ur">', __('More Info', 'bulk-delete'), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete Post Meta Fields', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete post meta fields based on value and to schedule automatic deletion', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-post-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-m-p">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete Comment Meta Fields', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete comment meta fields based on value and to schedule automatic deletion', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-comment-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-m-c">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete User Meta Fields', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete user meta fields based on value and to schedule automatic deletion', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-user-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-m-u">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '<li>';
        echo '<strong>', __( 'Delete Jetpack Contact Form Messages', 'bulk-delete' ), '</strong>', ' - ';
        echo __( 'Adds the ability to delete Jetpack Contact Form Messages based on filters and to schedule automatic deletion', 'bulk-delete' );
        echo ' <a href = "http://bulkwp.com/addons/bulk-delete-jetpack-contact-form-messages/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-jcm">', __( 'More Info', 'bulk-delete' ), '</a>.';
        echo '</li>';

        echo '</ul>';
    }
}

// hooks
add_action( 'bd_license_form'      , array( 'BD_License', 'display_activate_license_form' ), 100 );
add_action( 'bd_deactivate_license', array( 'BD_License', 'deactivate_license' ) );
add_action( 'bd_delete_license'    , array( 'BD_License', 'delete_license' ) );
add_action( 'bd_validate_license'  , array( 'BD_License', 'validate_license' ), 10, 2 );
?>
