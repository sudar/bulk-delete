<?php
/**
 * License Handler for Bulk Delete Addons
 *
 * @package    Bulk_Delete
 * @subpackage License
 * @author     Sudar
 * @since      5.0
 */
class BD_License_Handler {

    /**
     * Name of the addon
     *
     * @since 5.0
     */
    private $addon_name;

    /**
     * Code of the addon
     *
     * @since 5.0
     */
    private $addon_code;

    /**
     * Version of the plugin
     *
     * @since 5.0
     */
    private $version;

    /**
     * plugin file name
     *
     * @since 5.0
     */
    private $plugin_file;

    /**
     * Author of the plugin
     *
     * @since 5.0
     */
    private $author;

    /**
     * Constructor
     *
     * @since 5.0
     *
     * @param string $addon_name  Name of the addon
     * @param string $addon_code  Code of the addon
     * @param string $version     Version of the addon
     * @param string $plugin_file Addon file name
     * @param string $author      Author of the addon
     */
    function __construct( $addon_name, $addon_code, $version, $plugin_file, $author = 'Sudar Muthu' ) {

        $this->addon_name  = $addon_name;
        $this->addon_code  = $addon_code;
        $this->version     = $version;
        $this->plugin_file = $plugin_file;
        $this->author      = $author;

        $this->hooks();

        if ( BD_License::has_valid_license( $this->addon_name, $this->addon_code ) ) {
            $license_code = BD_License::get_license_code( $this->addon_code );
            if ( FALSE != $license_code ) {
                $this->hook_updater( $license_code );
            }
        }
    }

    /**
     * Start the updater
     *
     * @since 5.0
     * @access private
     * @param string $license_code License Code
     */
    private function hook_updater( $license_code ) {
        $bd = BULK_DELETE();

        if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            require_once Bulk_Delete::$PLUGIN_DIR . '/include/libraries/EDD_SL_Plugin_Updater.php';
        }

        $this->updater = new EDD_SL_Plugin_Updater( BD_EDD_API_Wrapper::STORE_URL, $this->plugin_file, array(
            'version'    => $this->version,
            'license'    => $license_code,
            'item_name'  => $this->addon_name,
            'addon_code' => $this->addon_code,
            'author'     => $this->author,
            'url'        => home_url()
        ));
    }

    /**
     * setup hooks
     *
     * @access private
     * @since 5.0
     */
    private function hooks() {
        add_action( 'bd_license_form' , array( &$this, 'display_license_form' ) );
        add_action( 'bd_license_field', array( &$this, 'add_license_field' ) );
        add_filter( 'bd_license_input', array( &$this, 'parse_license_input' ), 1 );
    }

    /**
     * Decide whether to display the license form or not
     *
     * @since 5.0
     */
    public function display_license_form() {
        if ( ! BD_License::has_valid_license( $this->addon_name, $this->addon_code ) ) {
            $bd = BULK_DELETE();
            $bd->display_activate_license_form = TRUE;
        }
    }

    /**
     * Add the license field to license form
     *
     * @since 5.0
     */
    public function add_license_field() {
        if ( ! BD_License::has_valid_license( $this->addon_name, $this->addon_code ) ) {
            add_settings_field(
                $this->addon_code, // ID
                '"' . $this->addon_name . '" ' . __( 'Addon License Key', 'bulk-delete' ), // Title
                array( &$this, 'print_license_key_field' ), // Callback
                Bulk_Delete::ADDON_PAGE_SLUG, // Page
                Bulk_Delete::SETTING_SECTION_ID // Section
            );
        }
    }

    /**
     * Print the license field
     *
     * @since 5.0
     */
    public function print_license_key_field() {
        if ( ! BD_License::has_valid_license( $this->addon_name, $this->addon_code ) ) {
            printf(
                '<input type="text" id="%s" name="%s[%s]" placeholder="%s">',
                $this->addon_code,
                Bulk_Delete::SETTING_OPTION_NAME,
                $this->addon_code,
                __( 'Enter license key', 'bulk-delete' )
            );
        }
    }

    /**
     * Parse the license key and activate it if needed.
     * If the key is invalid, then don't save it in the setting option
     *
     * @since 5.0
     */
    public function parse_license_input( $input ) {
        if ( is_array( $input ) && key_exists( $this->addon_code, $input ) ) {
            $license_code = trim( $input[ $this->addon_code ] );

            if ( ! empty( $license_code ) ) {
                if ( ! BD_License::has_valid_license( $this->addon_name, $this->addon_code ) ) {
                    $activated = BD_License::activate_license( $this->addon_name, $this->addon_code, $license_code );
                    if ( ! $activated ) {
                        unset( $input[ $this->addon_code ] );
                    }
                }
            } else {
                unset( $input[ $this->addon_code ] );
            }
        } else {
            if ( BD_License::has_valid_license( $this->addon_name, $this->addon_code ) ) {
                $license_code = BD_License::get_license_code( $this->addon_code );
                $input[ $this->addon_code ] = $license_code;
            }
        }
        return $input;
    }
}
?>
