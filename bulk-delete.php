<?php
/**
Plugin Name: Bulk Delete
Plugin Script: bulk-delete.php
Plugin URI: http://sudarmuthu.com/wordpress/bulk-delete
Description: Bulk delete users and posts from selected categories, tags, post types, custom taxonomies or by post status like drafts, scheduled posts, revisions etc.
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
Version: 4.1
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

if ( !class_exists( 'Bulk_Delete_Users' ) ) {
    require_once dirname( __FILE__ ) . '/include/class-bulk-delete-users.php';
}

if ( !class_exists( 'Bulk_Delete_Posts' ) ) {
    require_once dirname( __FILE__ ) . '/include/class-bulk-delete-posts.php';
}

if ( !class_exists( 'Bulk_Delete_Util' ) ) {
    require_once dirname( __FILE__ ) . '/include/class-bulk-delete-util.php';
}

if ( !function_exists( 'array_get' ) ) {
    require_once dirname( __FILE__ ) . '/include/util.php';
}

/**
 * Bulk Delete Main class
 */
class Bulk_Delete {
    
    const VERSION               = '4.1';

    // page slugs
    const USERS_PAGE_SLUG       = 'bulk-delete-users';

    // JS constants
    const JS_HANDLE             = 'bulk-delete';
    const JS_VARIABLE           = 'BULK_DELETE';

    // Cron hooks
    const CRON_HOOK_CATS        = 'do-bulk-delete-cats';
    const CRON_HOOK_PAGES       = 'do-bulk-delete-pages';
    const CRON_HOOK_POST_STATUS = 'do-bulk-delete-post-status';
    const CRON_HOOK_TAGS        = 'do-bulk-delete-tags';
    const CRON_HOOK_TAXONOMY    = 'do-bulk-delete-taxonomy';
    const CRON_HOOK_POST_TYPES  = 'do-bulk-delete-post-types';
    const CRON_HOOK_CUSTOM_FIELD= 'do-bulk-delete-custom-field';

    const CRON_HOOK_USER_ROLE   = 'do-bulk-delete-users-by-role';

    // meta boxes for delete posts
    const BOX_POST_STATUS       = 'bd_by_post_status';
    const BOX_CATEGORY          = 'bd_by_category';
    const BOX_TAG               = 'bd_by_tag';
    const BOX_TAX               = 'bd_by_tax';
    const BOX_POST_TYPE         = 'bd_by_post_type';
    const BOX_PAGE              = 'bd_by_page';
    const BOX_URL               = 'bd_by_url';
    const BOX_POST_REVISION     = 'bd_by_post_revision';
    const BOX_CUSTOM_FIELD      = 'bd_by_custom_field';
    const BOX_DEBUG             = 'bd_debug';

    // meta boxes for delete users
    const BOX_USERS             = 'bdu_by_users';

    /**
     * Default constructor
     */
    public function __construct() {
        // Load localization domain
        $this->translations = dirname(plugin_basename(__FILE__)) . '/languages/' ;
        load_plugin_textdomain( 'bulk-delete', false, $this->translations);

        // Register hooks
        add_action('admin_menu', array(&$this, 'add_menu'));
        add_action('admin_init', array(&$this, 'request_handler'));

        // Add more links in the plugin listing page
        add_filter( 'plugin_action_links', array( &$this, 'filter_plugin_actions' ), 10, 2 );
        add_filter( 'plugin_row_meta', array( &$this, 'add_plugin_links' ), 10, 2 );  
    }

    /**
     * Add navigation menu
     */
	function add_menu() {

        $this->admin_page = add_submenu_page( 'tools.php', __("Bulk Delete Posts", 'bulk-delete'), __("Bulk Delete Posts", 'bulk-delete'), 'delete_posts', basename(__FILE__), array(&$this, 'display_posts_page'));
        $this->users_page = add_submenu_page( 'tools.php', __("Bulk Delete Users", 'bulk-delete'), __("Bulk Delete Users", 'bulk-delete'), 'delete_users', self::USERS_PAGE_SLUG, array( &$this, 'display_users_page' ));
        $this->cron_page  = add_submenu_page( 'tools.php', __("Bulk Delete Schedules", 'bulk-delete'), __("Bulk Delete Schedules", 'bulk-delete'), 'delete_posts', 'bulk-delete-cron', array(&$this, 'display_cron_page'));

        // enqueue JavaScript
        add_action( 'admin_print_scripts-' . $this->admin_page, array( &$this, 'add_script') );
        add_action( 'admin_print_scripts-' . $this->users_page, array( &$this, 'add_script') );

        // delete posts page
		add_action( "load-{$this->admin_page}", array( &$this, 'add_delete_posts_settings_panel' ) );
        add_action( "add_meta_boxes_{$this->admin_page}", array( &$this, 'add_delete_posts_meta_boxes' ) );

        // delete users page
        add_action( "load-{$this->users_page}", array( &$this, 'add_delete_users_settings_panel' ) );
        add_action( "add_meta_boxes_{$this->users_page}", array( &$this, 'add_delete_users_meta_boxes' ) );
	}

    /**
     * Add settings Panel for delete posts page
     */ 
	function add_delete_posts_settings_panel() {
 
		/** 
		 * Create the WP_Screen object using page handle
		 */
		$this->delete_posts_screen = WP_Screen::get($this->admin_page);
 
		/**
		 * Content specified inline
		 */
		$this->delete_posts_screen->add_help_tab(
			array(
				'title'    => __('About Plugin', 'bulk-delete'),
				'id'       => 'about_tab',
				'content'  => '<p>' . __('This plugin allows you to delete posts in bulk from selected categories, tags, custom taxonomies or by post status like drafts, pending posts, scheduled posts etc.', 'bulk-delete') . '</p>',
				'callback' => false
			)
		);
 
        // Add help sidebar
		$this->delete_posts_screen->set_help_sidebar(
            '<p><strong>' . __('More information', 'bulk-delete') . '</strong></p>' .
            '<p><a href = "http://sudarmuthu.com/wordpress/bulk-delete">' . __('Plugin Homepage/support', 'bulk-delete') . '</a></p>' .
            '<p><a href = "http://sudarmuthu.com/wordpress/bulk-delete/pro-addons">' . __("Buy pro addons", 'bulk-delete') . '</a></p>' .
            '<p><a href = "http://sudarmuthu.com/blog">' . __("Plugin author's blog", 'bulk-delete') . '</a></p>' .
            '<p><a href = "http://sudarmuthu.com/wordpress/">' . __("Other Plugin's by Author", 'bulk-delete') . '</a></p>'
        );

        /* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
        do_action('add_meta_boxes_' . $this->admin_page, null);
        do_action('add_meta_boxes', $this->admin_page, null);
    
        /* Enqueue WordPress' script for handling the meta boxes */
        wp_enqueue_script('postbox');
	}

    /**
     * Register meta boxes for delete posts page
     */
    function add_delete_posts_meta_boxes() {
        add_meta_box( self::BOX_POST_STATUS, __( 'By Post Status', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_post_status_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_CATEGORY, __( 'By Category', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_category_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_TAG, __( 'By Tag', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_tag_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_TAX, __( 'By Custom Taxonomy', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_tax_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_POST_TYPE, __( 'By Custom Post Types', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_post_type_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_PAGE, __( 'By Page', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_page_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_URL, __( 'By URL', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_url_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_POST_REVISION, __( 'By Post Revision', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_post_revision_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_CUSTOM_FIELD, __( 'By Custom Field', 'bulk-delete' ), 'Bulk_Delete_Posts::render_by_custom_field_box', $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_DEBUG, __( 'Debug Information', 'bulk-delete' ), 'Bulk_Delete_Posts::render_debug_box', $this->admin_page, 'advanced', 'low' );
    }

    /**
     * Add settings Panel for delete users page
     */ 
	function add_delete_users_settings_panel() {
 
		/** 
		 * Create the WP_Screen object using page handle
		 */
		$this->delete_users_screen = WP_Screen::get( $this->users_page );
 
		/**
		 * Content specified inline
		 */
		$this->delete_users_screen->add_help_tab(
			array(
				'title'    => __('About Plugin', 'bulk-delete'),
				'id'       => 'about_tab',
				'content'  => '<p>' . __('This plugin allows you to delete posts in bulk from selected categories, tags, custom taxonomies or by post status like drafts, pending posts, scheduled posts etc.', 'bulk-delete') . '</p>',
				'callback' => false
			)
		);
 
        // Add help sidebar
		$this->delete_users_screen->set_help_sidebar(
            '<p><strong>' . __('More information', 'bulk-delete') . '</strong></p>' .
            '<p><a href = "http://sudarmuthu.com/wordpress/bulk-delete">' . __('Plugin Homepage/support', 'bulk-delete') . '</a></p>' .
            '<p><a href = "http://sudarmuthu.com/wordpress/bulk-delete/pro-addons">' . __("Buy pro addons", 'bulk-delete') . '</a></p>' .
            '<p><a href = "http://sudarmuthu.com/blog">' . __("Plugin author's blog", 'bulk-delete') . '</a></p>' .
            '<p><a href = "http://sudarmuthu.com/wordpress/">' . __("Other Plugin's by Author", 'bulk-delete') . '</a></p>'
        );

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
        add_meta_box( self::BOX_USERS, __( 'By User Role', 'bulk-delete' ), 'Bulk_Delete_Users::render_delete_users_box', $this->users_page, 'advanced' );
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
            'enter_cf_key' => __( 'Please enter some value for custom field key', 'bulk-delete' )
        );

        $translation_array = array( 'msg' => $msg, 'error' => $error );
        wp_localize_script( self::JS_HANDLE, self::JS_VARIABLE, $translation_array );
    }

    /**
     * Adds the settings link in the Plugin page. Based on http://striderweb.com/nerdaphernalia/2008/06/wp-use-action-links/
     * @staticvar <type> $this_plugin
     * @param <type> $links
     * @param <type> $file
     */
    function filter_plugin_actions($links, $file) {
        static $this_plugin;
        if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

        if( $file == $this_plugin ) {

            $delete_users_link = '<a href="tools.php?page=' . self::USERS_PAGE_SLUG . '">' . __('Bulk Delete Users', 'bulk-delete') . '</a>';
            array_unshift( $links, $delete_users_link ); // before other links

            $delete_posts_link = '<a href="tools.php?page=bulk-delete.php">' . __('Bulk Delete Posts', 'bulk-delete') . '</a>';
            array_unshift( $links, $delete_posts_link ); // before other links
        }
        return $links;
    }

    /**
     * Adds additional links in the Plugin listing. Based on http://zourbuth.com/archives/751/creating-additional-wordpress-plugin-links-row-meta/
     */
    function add_plugin_links($links, $file) {
        $plugin = plugin_basename(__FILE__);

        if ($file == $plugin) // only for this plugin
            return array_merge( $links, 
            array( '<a href="http://sudarmuthu.com/wordpress/bulk-delete/pro-addons" target="_blank">' . __('Buy Addons', 'bulk-delete') . '</a>' )
        );
        return $links;
    }

    /**
     * Show the delete posts page
     */
    function display_posts_page() {
?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php _e('Bulk Delete Posts', 'bulk-delete');?></h2>

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
                <iframe frameBorder="0" height = "1000" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option('admin_color'); ?>&version=<?php echo self::VERSION; ?>"></iframe>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <?php //do_meta_boxes( '', 'normal', null ); ?>
                <?php do_meta_boxes( '', 'advanced', null ); ?>
            </div> <!-- #postbox-container-2 -->

        </div> <!-- #post-body -->
    </div><!-- #poststuff -->
    </form>
</div><!-- .wrap -->

<?php
        // Display credits in Footer
        add_action( 'in_admin_footer', array(&$this, 'admin_footer' ));
    }

    /**
     * Adds Footer links.
     */
    function admin_footer() {
        $plugin_data = get_plugin_data( __FILE__ );
        printf('%1$s ' . __("plugin", 'bulk-delete') .' | ' . __("Version", 'bulk-delete') . ' %2$s | '. __('by', 'bulk-delete') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
    }

    /**
     * Display bulk delete users page
     */
    function display_users_page() {
?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php _e('Bulk Delete Users', 'bulk-delete');?></h2>

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
                <iframe frameBorder="0" height = "1000" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option('admin_color'); ?>&version=<?php echo self::VERSION; ?>"></iframe>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes( '', 'advanced', null ); ?>
            </div> <!-- #postbox-container-2 -->

        </div> <!-- #post-body -->
    </div><!-- #poststuff -->
    </form>
</div><!-- .wrap -->

<?php
        // Display credits in Footer
        add_action( 'in_admin_footer', array(&$this, 'admin_footer' ));
    }

    /**
     * Display the schedule page
     */
    function display_cron_page() {
        
        if(!class_exists('WP_List_Table')){
            require_once( ABSPATH . WPINC . '/class-wp-list-table.php' );
        }

        if (!class_exists('Cron_List_Table')) {
            require_once dirname(__FILE__) . '/include/class-cron-list-table.php';
        }

        //Prepare Table of elements
        $cron_list_table = new Cron_List_Table();
        $cron_list_table->prepare_items( Bulk_Delete_Util::get_cron_schedules() );
?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e('Bulk Delete Schedules', 'bulk-delete');?></h2>
<?php        
        //Table of elements
        $cron_list_table->display();
?>
    </div>
<?php
        // Display credits in Footer
        add_action( 'in_admin_footer', array(&$this, 'admin_footer' ));
    }

    /**
     * Request Handler. Handles both POST and GET requests
     */
    function request_handler() {

        // delete schedules
        if ( isset( $_GET['smbd_action'] ) && check_admin_referer( 'sm-bulk-delete-cron', 'sm-bulk-delete-cron-nonce' ) ) {

            switch($_GET['smbd_action']) {

                case 'delete-cron':
                    $cron_id = absint($_GET['cron_id']);
                    $cron_items = Bulk_Delete_Util::get_cron_schedules();
                    wp_unschedule_event($cron_items[$cron_id]['timestamp'], $cron_items[$cron_id]['type'], $cron_items[$cron_id]['args']);

                    $this->msg = __('The selected scheduled job was successfully deleted ', 'bulk-delete');

                    break;
            }
        }

        // delete users
        if ( isset( $_POST['smbdu_action'] ) && check_admin_referer( 'sm-bulk-delete-users', 'sm-bulk-delete-users-nonce' ) ) {

            switch( $_POST['smbdu_action'] ) {

                case "bulk-delete-users-by-role":
                    // delete by user role

                    $delete_options = array();
                    $delete_options['selected_roles']   = array_get( $_POST, 'smbdu_roles' );
                    $delete_options['no_posts']         = array_get( $_POST, 'smbdu_role_no_posts', FALSE );

                    $delete_options['login_restrict']   = array_get( $_POST, 'smbdu_login_restrict', FALSE );
                    $delete_options['login_days']       = array_get( $_POST, 'smbdu_login_days' );
                    $delete_options['limit_to']         = array_get( $_POST, 'smbdu_role_limit' );

                    if (array_get( $_POST, 'smbdu_userrole_cron', 'false' ) == 'true' ) {
                        $freq = $_POST['smbdu_userrole_cron_freq'];
                        $time = strtotime( $_POST['smbdu_userrole_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

                        if ( $freq == -1 ) {
                            wp_schedule_single_event( $time, self::CRON_HOOK_USER_ROLE, array( $delete_options ) );
                        } else {
                            wp_schedule_event( $time, $freq , self::CRON_HOOK_USER_ROLE, array( $delete_options ) );
                        }

                        $this->msg = __( 'Users from the selected userrole are scheduled for deletion.', 'bulk-delete' ) . ' ' . 
                            sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/tools.php?page=bulk-delete-cron' );
                    } else {
                        $deleted_count = Bulk_Delete_Users::delete_users_by_role( $delete_options );
                        $this->msg = sprintf( _n('Deleted %d user from the selected roles', 'Deleted %d users from the selected role' , $deleted_count, 'bulk-delete' ), $deleted_count );
                    }

                    break;
            }
        }

        // delete posts
        if ( isset( $_POST['smbd_action'] ) && check_admin_referer( 'sm-bulk-delete-posts', 'sm-bulk-delete-posts-nonce' ) ) {

            switch($_POST['smbd_action']) {

                case "bulk-delete-cats":
                    // delete by cats

                    $delete_options = array();
                    $delete_options['selected_cats'] = array_get($_POST, 'smbd_cats');
                    $delete_options['restrict']      = array_get($_POST, 'smbd_cats_restrict', FALSE);
                    $delete_options['private']       = array_get($_POST, 'smbd_cats_private');
                    $delete_options['limit_to']      = absint(array_get($_POST, 'smbd_cats_limit_to', 0));
                    $delete_options['force_delete']  = array_get($_POST, 'smbd_cats_force_delete', 'false');

                    $delete_options['cats_op']       = array_get($_POST, 'smbd_cats_op');
                    $delete_options['cats_days']     = array_get($_POST, 'smbd_cats_days');
                    
                    if (array_get($_POST, 'smbd_cats_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_cats_cron_freq'];
                        $time = strtotime($_POST['smbd_cats_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, self::CRON_HOOK_CATS, array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq , self::CRON_HOOK_CATS, array($delete_options));
                        }

                        $this->msg = __('Posts from the selected categories are scheduled for deletion.', 'bulk-delete') . ' ' . 
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/tools.php?page=bulk-delete-cron');
                    } else {
                        $deleted_count = self::delete_cats($delete_options);
                        $this->msg = sprintf( _n('Deleted %d post from the selected categories', 'Deleted %d posts from the selected categories' , $deleted_count, 'bulk-delete' ), $deleted_count);
                    }
                    
                    break;

                case "bulk-delete-tags":
                    // delete by tags
                    
                    $delete_options = array();
                    $delete_options['selected_tags'] = array_get($_POST, 'smbd_tags');
                    $delete_options['restrict']      = array_get($_POST, 'smbd_tags_restrict', FALSE);
                    $delete_options['private']       = array_get($_POST, 'smbd_tags_private');
                    $delete_options['limit_to']      = absint(array_get($_POST, 'smbd_tags_limit_to', 0));
                    $delete_options['force_delete']  = array_get($_POST, 'smbd_tags_force_delete', 'false');

                    $delete_options['tags_op']       = array_get($_POST, 'smbd_tags_op');
                    $delete_options['tags_days']     = array_get($_POST, 'smbd_tags_days');
                    
                    if (array_get($_POST, 'smbd_tags_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_tags_cron_freq'];
                        $time = strtotime($_POST['smbd_tags_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, self::CRON_HOOK_TAGS, array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq, self::CRON_HOOK_TAGS, array($delete_options));
                        }
                        $this->msg = __('Posts from the selected tags are scheduled for deletion.', 'bulk-delete') . ' ' . 
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/tools.php?page=bulk-delete-cron');
                    } else {
                        $deleted_count = self::delete_tags($delete_options);
                        $this->msg = sprintf( _n('Deleted %d post from the selected tags', 'Deleted %d posts from the selected tags' , $deleted_count, 'bulk-delete' ), $deleted_count);
                    }

                    break;

                case "bulk-delete-taxs":
                    // delete by taxs
                    
                    $delete_options = array();
                    $delete_options['selected_taxs']      = array_get($_POST, 'smbd_taxs');
                    $delete_options['selected_tax_terms'] = array_get($_POST, 'smbd_tax_terms');
                    $delete_options['restrict']           = array_get($_POST, 'smbd_taxs_restrict', FALSE);
                    $delete_options['private']            = array_get($_POST, 'smbd_taxs_private');
                    $delete_options['limit_to']           = absint(array_get($_POST, 'smbd_taxs_limit_to', 0));
                    $delete_options['force_delete']       = array_get($_POST, 'smbd_taxs_force_delete', 'false');

                    $delete_options['taxs_op']            = array_get($_POST, 'smbd_taxs_op');
                    $delete_options['taxs_days']          = array_get($_POST, 'smbd_taxs_days');
                    
                    if (array_get($_POST, 'smbd_taxs_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_taxs_cron_freq'];
                        $time = strtotime($_POST['smbd_taxs_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, self::CRON_HOOK_TAXONOMY, array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq, self::CRON_HOOK_TAXONOMY, array($delete_options));
                        }
                        $this->msg = __('Posts from the selected custom taxonomies are scheduled for deletion.', 'bulk-delete') . ' ' . 
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/tools.php?page=bulk-delete-cron');
                    } else {
                        $deleted_count = self::delete_taxs($delete_options);
                        $this->msg = sprintf( _n('Deleted %d post from the selected custom taxonomies', 'Deleted %d posts from the selected custom taxonomies' , $deleted_count, 'bulk-delete' ), $deleted_count);
                    }
                    break;

                case "bulk-delete-post-types":
                    // delete by custom post type
                    
                    $delete_options                   = array();

                    $delete_options['selected_types'] = array_get( $_POST, 'smbd_types' );
                    $delete_options['restrict']       = array_get($_POST, 'smbd_types_restrict', FALSE);
                    $delete_options['private']        = array_get($_POST, 'smbd_types_private');
                    $delete_options['limit_to']       = absint(array_get($_POST, 'smbd_types_limit_to', 0));
                    $delete_options['force_delete']   = array_get($_POST, 'smbd_types_force_delete', 'false');

                    $delete_options['types_op']       = array_get($_POST, 'smbd_types_op');
                    $delete_options['types_days']     = array_get($_POST, 'smbd_types_days');
                    
                    if (array_get($_POST, 'smbd_types_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_types_cron_freq'];
                        $time = strtotime($_POST['smbd_types_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event( $time, self::CRON_HOOK_POST_TYPES, array( $delete_options ) );
                        } else {
                            wp_schedule_event( $time, $freq, self::CRON_HOOK_POST_TYPES, array( $delete_options ) );
                        }

                        $this->msg = __( 'Posts from the selected custom post type are scheduled for deletion.', 'bulk-delete') . ' ' . 
                            sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/tools.php?page=bulk-delete-cron' );
                    } else {
                        $deleted_count = self::delete_post_types( $delete_options );
                        $this->msg = sprintf( _n( 'Deleted %d post from the selected custom post type', 'Deleted %d posts from the selected custom post type' , $deleted_count, 'bulk-delete' ), $deleted_count );
                    }

                    break;

                case "bulk-delete-post-status":
                    // Delete by post status like drafts, pending posts etc
                    
                    $delete_options = array();
                    $delete_options['restrict']         = array_get($_POST, 'smbd_post_status_restrict', FALSE);
                    $delete_options['limit_to']         = absint(array_get($_POST, 'smbd_post_status_limit_to', 0));
                    $delete_options['force_delete']     = array_get($_POST, 'smbd_post_status_force_delete', 'false');

                    $delete_options['post_status_op']   = array_get($_POST, 'smbd_post_status_op');
                    $delete_options['post_status_days'] = array_get($_POST, 'smbd_post_status_days');

                    $delete_options['drafts']           = array_get($_POST, 'smbd_drafts');
                    $delete_options['pending']          = array_get($_POST, 'smbd_pending');
                    $delete_options['future']           = array_get($_POST, 'smbd_future');
                    $delete_options['private']          = array_get($_POST, 'smbd_private');
                    
                    if (array_get($_POST, 'smbd_post_status_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_post_status_cron_freq'];
                        $time = strtotime($_POST['smbd_post_status_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, self::CRON_HOOK_POST_STATUS, array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq, self::CRON_HOOK_POST_STATUS, array($delete_options));
                        }
                        $this->msg = __('Posts with the selected status are scheduled for deletion.', 'bulk-delete') . ' ' . 
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/tools.php?page=bulk-delete-cron');
                    } else {
                        $deleted_count = self::delete_post_status($delete_options);
                        $this->msg = sprintf( _n('Deleted %d post with the selected post status', 'Deleted %d posts with the selected post status' , $deleted_count, 'bulk-delete' ), $deleted_count);
                    }
                    
                    break;

                case "bulk-delete-page":
                    // Delete pages
                    
                    $delete_options = array();
                    $delete_options['restrict']     = array_get($_POST, 'smbd_pages_restrict', FALSE);
                    $delete_options['limit_to']     = absint(array_get($_POST, 'smbd_pages_limit_to', 0));
                    $delete_options['force_delete'] = array_get($_POST, 'smbd_pages_force_delete', 'false');

                    $delete_options['page_op']      = array_get($_POST, 'smbd_pages_op');
                    $delete_options['page_days']    = array_get($_POST, 'smbd_pages_days');

                    $delete_options['publish']      = array_get($_POST, 'smbd_published_pages');
                    $delete_options['drafts']       = array_get($_POST, 'smbd_draft_pages');
                    $delete_options['pending']      = array_get($_POST, 'smbd_pending_pages');
                    $delete_options['future']       = array_get($_POST, 'smbd_future_pages');
                    $delete_options['private']      = array_get($_POST, 'smbd_private_pages');

                    if (array_get($_POST, 'smbd_pages_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_pages_cron_freq'];
                        $time = strtotime($_POST['smbd_pages_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, self::CRON_HOOK_PAGES, array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq , self::CRON_HOOK_PAGES, array($delete_options));
                        }
                        $this->msg = __('The selected pages are scheduled for deletion.', 'bulk-delete') . ' ' . 
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/tools.php?page=bulk-delete-cron');
                    } else {
                        $deleted_count = self::delete_pages($delete_options);
                        $this->msg = sprintf( _n('Deleted %d page', 'Deleted %d pages' , $deleted_count, 'bulk-delete' ), $deleted_count);
                    }
                    
                    break;

                case "bulk-delete-specific":
                    // Delete pages
                    
                    $force_delete = array_get($_POST, 'smbd_specific_force_delete');
                    if ($force_delete == 'true') {
                        $force_delete = true;
                    } else {
                        $force_delete = false;
                    }
                    
                    $urls = preg_split( '/\r\n|\r|\n/', array_get($_POST, 'smdb_specific_pages_urls') );
                    foreach ($urls as $url) {
                        $checkedurl = $url;
                        if (substr($checkedurl ,0,1) == '/') {
                            $checkedurl = get_site_url() . $checkedurl ;
                        }
                        $postid = url_to_postid( $checkedurl );
                        wp_delete_post($postid, $force_delete);
                    }

                    $deleted_count = count( $url );
                    $this->msg = sprintf( _n( 'Deleted %d post with the specified urls', 'Deleted %d posts with the specified urls' , $deleted_count, 'bulk-delete' ), $deleted_count);
                    break;

                case "bulk-delete-revisions":
                    // Delete page revisions
                    
                    $delete_options['revisions'] = array_get($_POST, 'smbd_revisions');
                    $deleted_count = self::delete_revisions($delete_options);

                    $this->msg = sprintf( _n( 'Deleted %d post revision', 'Deleted %d post revisions' , $deleted_count, 'bulk-delete' ), $deleted_count);
                    break;

                case "bulk-delete-cf":
                    // delete by custom field

                    if ( class_exists( 'Bulk_Delete_Custom_Field' ) ) {
                        $delete_options = array();
                        $delete_options['cf_key']        = array_get($_POST, 'smbd_cf_key');
                        $delete_options['cf_field_op']   = array_get($_POST, 'smbd_cf_field_op');
                        $delete_options['cf_value']      = array_get($_POST, 'smbd_cf_value');
                        $delete_options['restrict']      = array_get($_POST, 'smbd_cf_restrict', FALSE);
                        $delete_options['private']       = array_get($_POST, 'smbd_cf_private');
                        $delete_options['limit_to']      = absint(array_get($_POST, 'smbd_cf_limit_to', 0));
                        $delete_options['force_delete']  = array_get($_POST, 'smbd_cf_force_delete', 'false');

                        $delete_options['cf_op']         = array_get($_POST, 'smbd_cf_op');
                        $delete_options['cf_days']       = array_get($_POST, 'smbd_cf_days');
                        
                        if (array_get($_POST, 'smbd_cf_cron', 'false') == 'true') {
                            $freq = $_POST['smbd_cf_cron_freq'];
                            $time = strtotime($_POST['smbd_cf_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                            if ($freq == -1) {
                                wp_schedule_single_event($time, self::CRON_HOOK_CUSTOM_FIELD, array($delete_options));
                            } else {
                                wp_schedule_event($time, $freq , self::CRON_HOOK_CUSTOM_FIELD, array($delete_options));
                            }

                            $this->msg = __( 'Posts matching the selected custom field setting are scheduled for deletion.', 'bulk-delete' ) . ' ' . 
                                sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( 'wpurl' ) . '/wp-admin/tools.php?page=bulk-delete-cron' );
                        } else {
                            $deleted_count = Bulk_Delete_Custom_Field::delete_custom_field( $delete_options );
                            $this->msg = sprintf( _n( 'Deleted %d post using the selected custom field condition', 'Deleted %d posts using the selected custom field condition' , $deleted_count, 'bulk-delete' ), $deleted_count );
                        }
                    } 
                    break;
            }
        }

        // hook the admin notices action
        add_action( 'admin_notices', array(&$this, 'deleted_notice'), 9 );
    }

    /**
     * Show deleted notice messages
     */
    function deleted_notice() {
        if ( isset( $this->msg ) && $this->msg != '' ) {
            echo "<div class = 'updated'><p>" . $this->msg . "</p></div>";
        }

        // cleanup
        $this->msg = '';
        remove_action( 'admin_notices', array( &$this, 'deleted_notice' ));
    }

    /**
     * Delete posts by category
     */
    static function delete_cats($delete_options) {

        $selected_cats = $delete_options['selected_cats'];

        $private = $delete_options['private'];

        if ($private == 'true') {
            $options = array('category__in'=>$selected_cats,'post_status'=>'private', 'post_type'=>'post');
        } else {
            $options = array('category__in'=>$selected_cats,'post_status'=>'publish', 'post_type'=>'post');
        }

        $limit_to = $delete_options['limit_to'];

        if ($limit_to > 0) {
            $options['showposts'] = $limit_to;
        } else {
            $options['nopaging'] = 'true';
        }

        $force_delete = $delete_options['force_delete'];

        if ($force_delete == 'true') {
            $force_delete = true;
        } else {
            $force_delete = false;
        }

        if ($delete_options['restrict'] == "true") {
            $options['op'] = $delete_options['cats_op'];
            $options['days'] = $delete_options['cats_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        $wp_query = new WP_Query();
        $posts = $wp_query->query($options);

        foreach ($posts as $post) {
            wp_delete_post($post->ID, $force_delete);
        }

        return count($posts);
    }

    /**
     * Delete posts by tags
     */
    static function delete_tags($delete_options) {

        $selected_tags = $delete_options['selected_tags'];
        $options = array('tag__in'=>$selected_tags, 'post_status'=>'publish', 'post_type'=>'post');

        $private = $delete_options['private'];

        if ($private == 'true') {
            $options['post_status']  = 'private';
        }

        $limit_to = $delete_options['limit_to'];

        if ($limit_to > 0) {
            $options['showposts'] = $limit_to;
        } else {
            $options['nopaging'] = 'true';
        }

        $force_delete = $delete_options['force_delete'];

        if ($force_delete == 'true') {
            $force_delete = true;
        } else {
            $force_delete = false;
        }

        if ($delete_options['restrict'] == "true") {
            $options['op'] = $delete_options['tags_op'];
            $options['days'] = $delete_options['tags_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        $wp_query = new WP_Query();
        $posts = $wp_query->query($options);

        foreach ($posts as $post) {
            wp_delete_post($post->ID, $force_delete);
        }

        return count($posts);
    }

    /**
     * Delete posts by custom taxnomomy
     */
    static function delete_taxs($delete_options) {

        $selected_taxs = $delete_options['selected_taxs'];
        $selected_tax_terms = $delete_options['selected_tax_terms'];

        $options = array(
            'post_status'=>'publish', 
            'post_type'  =>'post',
            'tax_query'  => array(
                array(
                    'taxonomy' => $selected_taxs,
                    'terms'    => $selected_tax_terms,
                    'field'    => 'slug'
                )
            )
        );

        $private = $delete_options['private'];

        if ($private == 'true') {
            $options['post_status']  = 'private';
        }

        $limit_to = $delete_options['limit_to'];

        if ($limit_to > 0) {
            $options['showposts'] = $limit_to;
        } else {
            $options['nopaging'] = 'true';
        }

        $force_delete = $delete_options['force_delete'];

        if ($force_delete == 'true') {
            $force_delete = true;
        } else {
            $force_delete = false;
        }

        if ($delete_options['restrict'] == "true") {
            $options['op'] = $delete_options['taxs_op'];
            $options['days'] = $delete_options['taxs_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        $wp_query = new WP_Query();
        $posts = $wp_query->query($options);

        foreach ($posts as $post) {
            wp_delete_post($post->ID, $force_delete);
        }

        return count( $posts );
    }

    /**
     * Delete posts by custom post type
     */
    static function delete_post_types( $delete_options ) {
        $selected_types = $delete_options['selected_types'];

        $options = array(
            'post_status' => 'publish', 
            'post_type'   => $selected_types
        );

        $private = $delete_options['private'];

        if ($private == 'true') {
            $options['post_status']  = 'private';
        }

        $limit_to = $delete_options['limit_to'];

        if ($limit_to > 0) {
            $options['showposts'] = $limit_to;
        } else {
            $options['nopaging'] = 'true';
        }

        $force_delete = $delete_options['force_delete'];

        if ($force_delete == 'true') {
            $force_delete = true;
        } else {
            $force_delete = false;
        }

        self::pre_query();

        if ($delete_options['restrict'] == "true") {
            $options['op'] = $delete_options['types_op'];
            $options['days'] = $delete_options['types_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        $wp_query = new WP_Query();
        $posts = $wp_query->query($options);

        self::post_query();

        foreach ($posts as $post) {
            // $force delete parameter to custom post types doesn't work
            if ( $force_delete ) {
                wp_delete_post( $post->ID );
            } else {
                wp_trash_post( $post->ID );
            }
        }

        return count( $posts );
    }

    /**
     * The event calendar Plugin changes query parameters which results in compatibility issues.
     * So we disable it before executing our query
     */
    static function pre_query() {
        if ( class_exists( 'TribeEventsQuery' ) ) {
            remove_filter( 'pre_get_posts', array( TribeEventsQuery, 'pre_get_posts' ), 0 );
        }
    }

    /**
     * The event calendar Plugin changes query parameters which results in compatibility issues.
     * So we disable it before executing our query and then enable it after our query
     */
    static function post_query() {
        if ( class_exists( 'TribeEventsQuery' ) ) {
            add_filter( 'pre_get_posts', array( TribeEventsQuery, 'pre_get_posts' ), 0 );
        }
    }

    /**
     * Delete posts by post status - drafts, pending posts, scheduled posts etc.
     */
    static function delete_post_status( $delete_options ) {
        global $wp_query;
        global $wpdb;

        $options = array();
        $post_status = array();

        $limit_to = $delete_options['limit_to'];

        if ($limit_to > 0) {
            $options['showposts'] = $limit_to;
        } else {
            $options['nopaging'] = 'true';
        }

        $force_delete = $delete_options['force_delete'];

        if ($force_delete == 'true') {
            $force_delete = true;
        } else {
            $force_delete = false;
        }

        // Drafts
        if ("drafts" == $delete_options['drafts']) {
            $post_status[] = 'draft';
        }

        // Pending Posts
        if ("pending" == $delete_options['pending']) {
            $post_status[] = 'pending';
        }

        // Future Posts
        if ("future" == $delete_options['future']) {
            $post_status[] = 'future';
        }

        // Private Posts
        if ("private" == $delete_options['private']) {
            $post_status[] = 'private';
        }

        if ($delete_options['restrict'] == "true") {
            $options['op'] = $delete_options['post_status_op'];
            $options['days'] = $delete_options['post_status_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        // now retrieve all posts and delete them
        $options['post_status'] = $post_status;

        // ignore sticky posts.
        // For some reason, sticky posts also gets deleted when deleting drafts through a schedule
        $options['post__not_in'] = get_option( 'sticky_posts' );

        $posts = $wp_query->query($options);

        foreach ($posts as $post) {
            wp_delete_post($post->ID, $force_delete);
        }

        return count( $posts );
    }

    /**
     * Bulk Delete pages
     */
    static function delete_pages( $delete_options ) {
        global $wp_query;

        $options = array();
        $post_status = array();

        $limit_to = $delete_options['limit_to'];

        if ($limit_to > 0) {
            $options['showposts'] = $limit_to;
        } else {
            $options['nopaging'] = 'true';
        }

        $force_delete = $delete_options['force_delete'];

        if ($force_delete == 'true') {
            $force_delete = true;
        } else {
            $force_delete = false;
        }

        // published pages
        if ("published_pages" == $delete_options['publish']) {
            $post_status[] = 'publish';
        }

        // Drafts
        if ("draft_pages" == $delete_options['drafts']) {
            $post_status[] = 'draft';
        }

        // Pending Posts
        if ("pending_pages" == $delete_options['pending']) {
            $post_status[] = 'pending';
        }

        // Future Posts
        if ("future_pages" == $delete_options['future']) {
            $post_status[] = 'future';
        }

        // Private Posts
        if ("private_pages" == $delete_options['private']) {
            $post_status[] = 'private';
        }

        $options['post_type'] = 'page';
        $options['post_status'] = $post_status;

        if ($delete_options['restrict'] == "true") {
            $options['op'] = $delete_options['page_op'];
            $options['days'] = $delete_options['page_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        $pages = $wp_query->query($options);
        foreach ($pages as $page) {
            wp_delete_post($page->ID, $force_delete);
        }

        return count( $pages );
    }

    /**
     * Delete all post revisions
     */
    static function delete_revisions( $delete_options ) {
        global $wpdb;

        // Revisions
        if ("revisions" == $delete_options['revisions']) {
            $revisions = $wpdb->get_results("select ID from $wpdb->posts where post_type = 'revision'");

            foreach ($revisions as $revision) {
                wp_delete_post( $revision->ID );
            }

            return count( $revisions );
        }

        return 0;
    }
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'Bulk_Delete' ); function Bulk_Delete() { global $Bulk_Delete; $Bulk_Delete = new Bulk_Delete(); }
?>
