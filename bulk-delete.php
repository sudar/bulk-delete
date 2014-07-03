<?php
/**
Plugin Name: Bulk Delete
Plugin Script: bulk-delete.php
Plugin URI: http://bulkwp.com
Description: Bulk delete users and posts from selected categories, tags, post types, custom taxonomies or by post status like drafts, scheduled posts, revisions etc.
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
Version: 5.2
License: GPL
Author: Sudar
Author URI: http://sudarmuthu.com/
Text Domain: bulk-delete
Domain Path: languages/

=== RELEASE NOTES ===
Check readme file for full release notes
*/

/*  Copyright 2009  Sudar Muthu  (email : sudar@sudarmuthu.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * @package    Bulk_Delete
 * @subpackage core
 * @author     Sudar
 * @version    5.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Bulk_Delete' ) ) :

/**
 * Main Bulk_Delete class
 *
 * Singleton @since 5.0
 */
final class Bulk_Delete {
    /**
     * @var Bulk_Delete The one true Bulk_Delete
     * @since 5.0
     */
    private static $instance;

    const VERSION                   = '5.2';

    // page slugs
    const POSTS_PAGE_SLUG           = 'bulk-delete-posts';
    const PAGES_PAGE_SLUG           = 'bulk-delete-pages';
    const USERS_PAGE_SLUG           = 'bulk-delete-users';
    const CRON_PAGE_SLUG            = 'bulk-delete-cron';
    const ADDON_PAGE_SLUG           = 'bulk-delete-addon';
    const INFO_PAGE_SLUG            = 'bulk-delete-info';

    // JS constants
    const JS_HANDLE                 = 'bulk-delete';
    const JS_VARIABLE               = 'BULK_DELETE';

    // Cron hooks
    const CRON_HOOK_CATEGORY        = 'do-bulk-delete-cat';
    const CRON_HOOK_POST_STATUS     = 'do-bulk-delete-post-status';
    const CRON_HOOK_TAG             = 'do-bulk-delete-tag';
    const CRON_HOOK_TAXONOMY        = 'do-bulk-delete-taxonomy';
    const CRON_HOOK_POST_TYPE       = 'do-bulk-delete-post-type';
    const CRON_HOOK_CUSTOM_FIELD    = 'do-bulk-delete-custom-field';
    const CRON_HOOK_TITLE           = 'do-bulk-delete-by-title';
    const CRON_HOOK_DUPLICATE_TITLE = 'do-bulk-delete-by-duplicate-title';
    const CRON_HOOK_POST_BY_ROLE    = 'do-bulk-delete-posts-by-role';

    const CRON_HOOK_PAGES_STATUS    = 'do-bulk-delete-pages-by-status';

    const CRON_HOOK_USER_ROLE       = 'do-bulk-delete-users-by-role';

    // meta boxes for delete posts
    const BOX_POST_STATUS           = 'bd_by_post_status';
    const BOX_CATEGORY              = 'bd_by_category';
    const BOX_TAG                   = 'bd_by_tag';
    const BOX_TAX                   = 'bd_by_tax';
    const BOX_POST_TYPE             = 'bd_by_post_type';
    const BOX_URL                   = 'bd_by_url';
    const BOX_POST_REVISION         = 'bd_by_post_revision';
    const BOX_CUSTOM_FIELD          = 'bd_by_custom_field';
    const BOX_TITLE                 = 'bd_by_title';
    const BOX_DUPLICATE_TITLE       = 'bd_by_duplicate_title';
    const BOX_POST_FROM_TRASH       = 'bd_posts_from_trash';
    const BOX_POST_BY_ROLE          = 'bd_post_by_user_role';

    // meta boxes for delete pages
    const BOX_PAGE_STATUS           = 'bd_by_page_status';
    const BOX_PAGE_FROM_TRASH       = 'bd_pages_from_trash';

    // meta boxes for delete users
    const BOX_USERS                 = 'bdu_by_users';

    // Settings constants
    const SETTING_OPTION_GROUP      = 'bd_settings';
    const SETTING_OPTION_NAME       = 'bd_licenses';
    const SETTING_SECTION_ID        = 'bd_license_section';

    // Transient keys
    const LICENSE_CACHE_KEY_PREFIX  = 'bd-license_';

    // path variables
    // Ideally these should be constants, but because of PHP's limitations, these are static variables
    static $PLUGIN_DIR;
    static $PLUGIN_URL;
    static $PLUGIN_FILE;

    /**
     * Main Bulk_Delete Instance
     *
     * Insures that only one instance of Bulk_Delete exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since 5.0
     * @static
     * @staticvar array $instance
     * @uses Bulk_Delete::setup_paths() Setup the plugin paths
     * @uses Bulk_Delete::includes() Include the required files
     * @uses Bulk_Delete::load_textdomain() Load text domain for translation
     * @uses Bulk_Delete::setup_actions() Setup the hooks and actions
     * @see BULK_DELETE()
     * @return The one true BULK_DELETE
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Bulk_Delete ) ) {
            self::$instance = new Bulk_Delete;
            self::$instance->setup_paths();
            self::$instance->includes();
            self::$instance->load_textdomain();
            self::$instance->setup_actions();
        }
        return self::$instance;
    }

    /**
     * Throw error on object clone
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @since  5.0
     * @access protected
     * @return void
     */
    public function __clone() {
        // Cloning instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bulk-delete' ), '5.0' );
    }

    /**
     * Disable unserializing of the class
     *
     * @since  5.0
     * @access protected
     * @return void
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bulk-delete' ), '5.0' );
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since  5.0
     * @return void
     */
    private function setup_paths() {
        // Plugin Folder Path
        self::$PLUGIN_DIR = plugin_dir_path( __FILE__ );

        // Plugin Folder URL
        self::$PLUGIN_URL = plugin_dir_url( __FILE__ );

        // Plugin Root File
        self::$PLUGIN_FILE = __FILE__;
    }

    /**
     * Include required files
     *
     * @access private
     * @since  5.0
     * @return void
     */
    private function includes() {
        require_once self::$PLUGIN_DIR . '/include/class-bulk-delete-posts.php';
        require_once self::$PLUGIN_DIR . '/include/class-bulk-delete-pages.php';
        require_once self::$PLUGIN_DIR . '/include/class-bulk-delete-users.php';
        require_once self::$PLUGIN_DIR . '/include/class-bulk-delete-system-info.php';
        require_once self::$PLUGIN_DIR . '/include/class-bulk-delete-util.php';
        require_once self::$PLUGIN_DIR . '/include/class-bd-license.php';
        require_once self::$PLUGIN_DIR . '/include/class-bd-license-handler.php';
        require_once self::$PLUGIN_DIR . '/include/class-bd-edd-api-wrapper.php';
        require_once self::$PLUGIN_DIR . '/include/class-bd-settings.php';
        require_once self::$PLUGIN_DIR . '/include/admin-ui.php';
        require_once self::$PLUGIN_DIR . '/include/class-bulk-delete-help-screen.php';
    }

    /**
     * Loads the plugin language files
     *
     * @since  5.0
     */
    public function load_textdomain() {
        // Load localization domain
        $this->translations = dirname( plugin_basename( self::$PLUGIN_FILE ) ) . '/languages/';
        load_plugin_textdomain( 'bulk-delete', false, $this->translations );
    }

    /**
     * Loads the plugin's actions and hooks
     *
     * @access private
     * @since  5.0
     * @return void
     */
    private function setup_actions() {
        add_action( 'admin_menu', array( &$this, 'add_menu' ) );
        add_action( 'admin_init', array( &$this, 'request_handler' ) );
    }

    /**
     * Add navigation menu
     */
	function add_menu() {
        add_menu_page( __( 'Bulk Delete', 'bulk-delete' ) , __( 'Bulk Delete', 'bulk-delete' ), 'manage_options', self::POSTS_PAGE_SLUG, array( &$this, 'display_posts_page' ), 'dashicons-trash', '26.9966' );

        $this->posts_page = add_submenu_page( self::POSTS_PAGE_SLUG , __( 'Bulk Delete Posts'       , 'bulk-delete' ) , __( 'Bulk Delete Posts' , 'bulk-delete' ) , 'delete_posts'     , self::POSTS_PAGE_SLUG , array( &$this                    , 'display_posts_page' ) );
        $this->pages_page = add_submenu_page( self::POSTS_PAGE_SLUG , __( 'Bulk Delete Pages'       , 'bulk-delete' ) , __( 'Bulk Delete Pages' , 'bulk-delete' ) , 'delete_pages'     , self::PAGES_PAGE_SLUG , array( &$this                    , 'display_pages_page' ) );
        $this->users_page = add_submenu_page( self::POSTS_PAGE_SLUG , __( 'Bulk Delete Users'       , 'bulk-delete' ) , __( 'Bulk Delete Users' , 'bulk-delete' ) , 'delete_users'     , self::USERS_PAGE_SLUG , array( &$this                    , 'display_users_page' ) );
        $this->cron_page  = add_submenu_page( self::POSTS_PAGE_SLUG , __( 'Bulk Delete Schedules'   , 'bulk-delete' ) , __( 'Schedules'         , 'bulk-delete' ) , 'delete_posts'     , self::CRON_PAGE_SLUG  , array( &$this                    , 'display_cron_page' ) );
        $this->addon_page = add_submenu_page( self::POSTS_PAGE_SLUG , __( 'Addon Licenses'          , 'bulk-delete' ) , __( 'Addon Licenses'    , 'bulk-delete' ) , 'activate_plugins' , self::ADDON_PAGE_SLUG , array( 'BD_License'              , 'display_addon_page' ) );
        $this->info_page  = add_submenu_page( self::POSTS_PAGE_SLUG , __( 'Bulk Delete System Info' , 'bulk-delete' ) , __( 'System Info'       , 'bulk-delete' ) , 'manage_options'   , self::INFO_PAGE_SLUG  , array( 'Bulk_Delete_System_Info' , 'display_system_info' ) );

        // enqueue JavaScript
        add_action( 'admin_print_scripts-' . $this->posts_page, array( &$this, 'add_script') );
        add_action( 'admin_print_scripts-' . $this->pages_page, array( &$this, 'add_script') );
        add_action( 'admin_print_scripts-' . $this->users_page, array( &$this, 'add_script') );

        // delete posts page
		add_action( "load-{$this->posts_page}", array( &$this, 'add_delete_posts_settings_panel' ) );
        add_action( "add_meta_boxes_{$this->posts_page}", array( &$this, 'add_delete_posts_meta_boxes' ) );

        // delete pages page
		add_action( "load-{$this->pages_page}", array( &$this, 'add_delete_pages_settings_panel' ) );
        add_action( "add_meta_boxes_{$this->pages_page}", array( &$this, 'add_delete_pages_meta_boxes' ) );

        // delete users page
        add_action( "load-{$this->users_page}", array( &$this, 'add_delete_users_settings_panel' ) );
        add_action( "add_meta_boxes_{$this->users_page}", array( &$this, 'add_delete_users_meta_boxes' ) );
	}

    /**
     * Add settings Panel for delete posts page
     */
    function add_delete_posts_settings_panel() {

        /**
         * Add contextual help for admin screens
         *
         * @since 5.1
         */
        do_action( 'bd_add_contextual_help', $this->posts_page );

        /* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
        do_action('add_meta_boxes_' . $this->posts_page, null);
        do_action('add_meta_boxes', $this->posts_page, null);

        /* Enqueue WordPress' script for handling the meta boxes */
        wp_enqueue_script('postbox');
    }

    /**
     * Register meta boxes for delete posts page
     */
    function add_delete_posts_meta_boxes() {
        add_meta_box( self::BOX_POST_STATUS     , __( 'By Post Status'       , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_status_box'          , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_CATEGORY        , __( 'By Category'          , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_category_box'        , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_TAG             , __( 'By Tag'               , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_tag_box'             , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_TAX             , __( 'By Custom Taxonomy'   , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_taxonomy_box'        , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_POST_TYPE       , __( 'By Custom Post Types' , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_post_type_box'       , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_URL             , __( 'By URL'               , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_url_box'             , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_POST_REVISION   , __( 'By Post Revision'     , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_posts_by_revision_box'               , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_CUSTOM_FIELD    , __( 'By Custom Field'      , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_custom_field_box'    , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_TITLE           , __( 'By Title'             , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_title_box'           , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_DUPLICATE_TITLE , __( 'By Duplicate Title'   , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_duplicate_title_box' , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_POST_BY_ROLE    , __( 'By User Role'         , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_by_user_role_box'       , $this->posts_page , 'advanced' );
        add_meta_box( self::BOX_POST_FROM_TRASH , __( 'Posts in Trash'       , 'bulk-delete' ) , 'Bulk_Delete_Posts::render_delete_posts_from_trash'             , $this->posts_page , 'advanced' );
    }

    /**
     * Setup settings panel for delete pages page
     *
     * @since 5.0
     */
    function add_delete_pages_settings_panel() {

        /**
         * Add contextual help for admin screens
         *
         * @since 5.1
         */
        do_action( 'bd_add_contextual_help', $this->pages_page );

        /* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
        do_action('add_meta_boxes_' . $this->pages_page, null);
        do_action('add_meta_boxes', $this->pages_page, null);

        /* Enqueue WordPress' script for handling the meta boxes */
        wp_enqueue_script('postbox');
    }

    /**
     * Register meta boxes for delete pages page
     *
     * @since 5.0
     */
    function add_delete_pages_meta_boxes() {
        add_meta_box( self::BOX_PAGE_STATUS     , __( 'By Page status' , 'bulk-delete' ) , 'Bulk_Delete_Pages::render_delete_pages_by_status_box' , $this->pages_page , 'advanced' );
        add_meta_box( self::BOX_PAGE_FROM_TRASH , __( 'Pages in Trash' , 'bulk-delete' ) , 'Bulk_Delete_Pages::render_delete_pages_from_trash'    , $this->pages_page , 'advanced' );
    }

    /**
     * Add settings Panel for delete users page
     */
    function add_delete_users_settings_panel() {

        /**
         * Add contextual help for admin screens
         *
         * @since 5.1
         */
        do_action( 'bd_add_contextual_help', $this->users_page );

        /* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
        do_action('add_meta_boxes_' . $this->users_page, null);
        do_action('add_meta_boxes', $this->users_page, null);

        /* Enqueue WordPress' script for handling the meta boxes */
        wp_enqueue_script('postbox');
    }

    /**
     * Register meta boxes for delete users page
     */
    function add_delete_users_meta_boxes() {
        add_meta_box( self::BOX_USERS, __( 'By User Role', 'bulk-delete' ), 'Bulk_Delete_Users::render_delete_users_by_role_box', $this->users_page, 'advanced' );
    }

    /**
     * Enqueue JavaScript
     */
    function add_script() {
        global $wp_scripts;

        // uses code from http://trentrichardson.com/examples/timepicker/
        wp_enqueue_script( 'jquery-ui-timepicker', plugins_url( '/js/jquery-ui-timepicker.js', __FILE__ ), array( 'jquery-ui-slider', 'jquery-ui-datepicker' ), '1.4', true );
        wp_enqueue_script( self::JS_HANDLE, plugins_url('/js/bulk-delete.js', __FILE__), array('jquery-ui-timepicker'), self::VERSION, TRUE);

        $ui = $wp_scripts->query('jquery-ui-core');

        $url = "http://ajax.aspnetcdn.com/ajax/jquery.ui/{$ui->ver}/themes/smoothness/jquery-ui.css";
        wp_enqueue_style('jquery-ui-smoothness', $url, false, $ui->ver);
        wp_enqueue_style('jquery-ui-timepicker', plugins_url('/style/jquery-ui-timepicker.css', __FILE__), array(), '1.1.1');

        // JavaScript messages
        $msg = array(
            'deletewarning'      => __('Are you sure you want to delete all the selected posts', 'bulk-delete'),
            'deletewarningusers' => __( 'Are you sure you want to delete all the selected users', 'bulk-delete' )
        );

        $error = array(
            'selectone'    => __( 'Please select posts from at least one option', 'bulk-delete' ),
            'enterurl'     => __( 'Please enter at least one page url', 'bulk-delete' ),
            'enter_cf_key' => __( 'Please enter some value for custom field key', 'bulk-delete' ),
            'enter_title'  => __( 'Please enter some value for title', 'bulk-delete' )
        );

        $translation_array = array( 'msg' => $msg, 'error' => $error );
        wp_localize_script( self::JS_HANDLE, self::JS_VARIABLE, $translation_array );
    }

    /**
     * Show the delete posts page
     */
    function display_posts_page() {
?>
<div class="wrap">
    <h2><?php _e('Bulk Delete Posts', 'bulk-delete');?></h2>
    <?php settings_errors(); ?>

    <form method = "post">
<?php
        // nonce for bulk delete
        wp_nonce_field( 'sm-bulk-delete-posts', 'sm-bulk-delete-posts-nonce' );

        /* Used to save closed meta boxes and their order */
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
?>
    <div id = "poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="post-body-content">
                <div class="updated" >
                    <p><strong><?php _e("WARNING: Posts deleted once cannot be retrieved back. Use with caution.", 'bulk-delete'); ?></strong></p>
                </div>
            </div><!-- #post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <iframe frameBorder="0" height = "1300" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option( 'admin_color' ); ?>&version=<?php echo self::VERSION; ?>"></iframe>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes( '', 'advanced', null ); ?>
            </div> <!-- #postbox-container-2 -->

        </div> <!-- #post-body -->
    </div><!-- #poststuff -->
    </form>
</div><!-- .wrap -->

<?php
        /**
         * Runs just before displaying the footer text in the "Bulk Delete Posts" admin page.
         *
         * This action is primarily for adding extra content in the footer of "Bulk Delete Posts" admin page.
         *
         * @since 5.0
         */
        do_action( 'bd_admin_footer_posts_page' );
    }

    /**
     * Display the delete pages page
     *
     * @since 5.0
     */
    function display_pages_page() {
?>
<div class="wrap">
    <h2><?php _e( 'Bulk Delete Pages', 'bulk-delete' );?></h2>
    <?php settings_errors(); ?>

    <form method = "post">
<?php
        // nonce for bulk delete
        wp_nonce_field( 'sm-bulk-delete-pages', 'sm-bulk-delete-pages-nonce' );

        /* Used to save closed meta boxes and their order */
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
?>
    <div id = "poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="post-body-content">
                <div class = "updated">
                    <p><strong><?php _e( 'WARNING: Pages deleted once cannot be retrieved back. Use with caution.', 'bulk-delete' ); ?></strong></p>
                </div>
            </div><!-- #post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <iframe frameBorder="0" height = "1300" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option( 'admin_color' ); ?>&version=<?php echo self::VERSION; ?>"></iframe>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes( '', 'advanced', null ); ?>
            </div> <!-- #postbox-container-2 -->

        </div> <!-- #post-body -->
    </div><!-- #poststuff -->
    </form>
</div><!-- .wrap -->

<?php
        /**
         * Runs just before displaying the footer text in the "Bulk Delete Pages" admin page.
         *
         * This action is primarily for adding extra content in the footer of "Bulk Delete Pages" admin page.
         *
         * @since 5.0
         */
        do_action( 'bd_admin_footer_pages_page' );
    }

    /**
     * Display bulk delete users page
     */
    function display_users_page() {
?>
<div class="wrap">
    <h2><?php _e('Bulk Delete Users', 'bulk-delete');?></h2>
    <?php settings_errors(); ?>

    <form method = "post">
<?php
        // nonce for bulk delete
        wp_nonce_field( 'sm-bulk-delete-users', 'sm-bulk-delete-users-nonce' );

        /* Used to save closed meta boxes and their order */
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
?>
    <div id = "poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="post-body-content">
                <div class = "updated">
                    <p><strong><?php _e("WARNING: Users deleted once cannot be retrieved back. Use with caution.", 'bulk-delete'); ?></strong></p>
                </div>
            </div><!-- #post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <iframe frameBorder="0" height = "1300" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option( 'admin_color' ); ?>&version=<?php echo self::VERSION; ?>"></iframe>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes( '', 'advanced', null ); ?>
            </div> <!-- #postbox-container-2 -->

        </div> <!-- #post-body -->
    </div><!-- #poststuff -->
    </form>
</div><!-- .wrap -->

<?php
        /**
         * Runs just before displaying the footer text in the "Bulk Delete Users" admin page.
         *
         * This action is primarily for adding extra content in the footer of "Bulk Delete Users" admin page.
         *
         * @since 5.0
         */
        do_action( 'bd_admin_footer_users_page' );
    }

    /**
     * Display the schedule page
     */
    function display_cron_page() {

        if(!class_exists('WP_List_Table')){
            require_once( ABSPATH . WPINC . '/class-wp-list-table.php' );
        }

        if ( !class_exists( 'Cron_List_Table' ) ) {
            require_once self::$PLUGIN_DIR . '/include/class-cron-list-table.php';
        }

        //Prepare Table of elements
        $cron_list_table = new Cron_List_Table();
        $cron_list_table->prepare_items();
?>
    <div class="wrap">
        <h2><?php _e('Bulk Delete Schedules', 'bulk-delete');?></h2>
        <?php settings_errors(); ?>
<?php
        //Table of elements
        $cron_list_table->display();
?>
    </div>
<?php
        /**
         * Runs just before displaying the footer text in the "Schedules" admin page.
         *
         * This action is primarily for adding extra content in the footer of "Schedules" admin page.
         *
         * @since 5.0
         */
        do_action( 'bd_admin_footer_cron_page' );
    }

    /**
     * Handle both POST and GET requests
     *
     * This method automatically triggers all the actions
     */
    function request_handler() {

        if ( isset( $_POST['bd_action'] ) ) {
            if ( 'delete_pages_' === substr( $_POST['bd_action'], 0, strlen('delete_pages_') ) &&
                !check_admin_referer( 'sm-bulk-delete-pages', 'sm-bulk-delete-pages-nonce' ) ) {
                return FALSE;
            }

            if ( 'delete_posts_' === substr( $_POST['bd_action'], 0, strlen('delete_posts_') ) &&
                !check_admin_referer( 'sm-bulk-delete-posts', 'sm-bulk-delete-posts-nonce' ) ) {
                return FALSE;
            }

            if ( 'delete_users_' === substr( $_POST['bd_action'], 0, strlen('delete_users_') ) &&
                !check_admin_referer( 'sm-bulk-delete-users', 'sm-bulk-delete-users-nonce' ) ) {
                return FALSE;
            }

            do_action( 'bd_' . $_POST['bd_action'], $_POST );
        }

        if ( isset( $_GET['bd_action'] ) ) {
            do_action( 'bd_' . $_GET['bd_action'], $_GET );
        }

    }
}

endif; // End if class_exists check

/**
 * The main function responsible for returning the one true Bulk_Delete
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $bulk_delete = BULK_DELETE(); ?>
 *
 * @since 5.0
 * @return object The one true Bulk_Delete Instance
 */
function BULK_DELETE() {
	return Bulk_Delete::instance();
}

// Get BULK_DELETE Running
BULK_DELETE();
?>
