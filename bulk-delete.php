<?php
/**
Plugin Name: Bulk Delete
Plugin Script: bulk-delete.php
Plugin URI: http://sudarmuthu.com/wordpress/bulk-delete
Description: Bulk delete posts from selected categories, tags, custom taxonomies or by post type like drafts, scheduled posts, revisions etc.
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
Version: 3.4
License: GPL
Author: Sudar
Author URI: http://sudarmuthu.com/
Text Domain: bulk-delete
Domain Path: languages/

=== RELEASE NOTES ===
2009-02-02 - v0.1 - first version
2009-02-03 - v0.2 - Second release - Fixed issues with pagging
2009-04-05 - v0.3 - Third release - Prevented drafts from deleted when only posts are selected
2009-07-05 - v0.4 - Fourth release - Added option to delete by date.
2009-07-21 - v0.5 - Fifth release - Added option to delete all pending posts.
2009-07-22 - v0.6 - Sixth release - Added option to delete all scheduled posts.
2010-02-21 - v0.7 - Added an option to delete posts directly or send them to trash and support for translation.
2010-03-17 - v0.8 - Added support for private posts.
2010-06-19 - v1.0 - Proper handling of limits.
2011-01-22 - v1.1 - Added support to delete posts by custom taxonomies
2011-02-06 - v1.2 - Added some optimization to handle huge number of posts in underpowered servers
2011-05-11 - v1.3 - Added German translations
2011-08-25 - v1.4 - Added Turkish translations
2011-11-13 - v1.5 - Added Spanish translations
2011-11-28 - v1.6 - Added Italian translations
2012-01-12 - v1.7 - Added Bulgarian translations
2012-01-31 - v1.8 - Added roles and capabilities for menu
2012-03-16 - v1.9 - Added support for deleting by permalink. Credit Martin Capodici
                  - Fixed issues with translations
                  - Added Rusian translations
2012-04-01 - v2.0 (10 hours) - Fixed a major issue in how dates were handled.
                  - Major UI revamp
                  - Added debug information and support urls
2012-04-07 - v2.1 (1 hour) - Fixed CSS issues in IE.
                  - Added Lithuanian translations
2012-07-11 - v2.2 - (Dev time: 0.5 hour)
                  - Added Hindi translations
                  - Added checks to see if elements are present in the array before accessing them.
2012-10-28 - v2.2.1 - (Dev time: 0.5 hour)
                  - Added Serbian translations
2012-12-20 - v2.2.2 - (Dev time: 0.5 hour)
                  - Removed unused wpdb->prepare() function calls
2013-04-27 - v3.0 - (Dev time: 10 hours)
                  - Added support for pro addons
                  - Added GUI to see cron jobs
2013-04-28 - v3.1 - (Dev time: 5 hours)
                  - Added separate delete by sections for pages, drafts and urls
                  - Added the option to delete by date for drafts, revisions, future posts etc
                  - Added the option to delete by date for pages
2013-05-04 - v3.2 - (Dev time: 20 hours)
                  - Added support for scheduling auto delete of pages
                  - Added support for scheduling auto delete of drafts
                  - Fixed issue in deleting post revisions
                  - Move post revisions to a separate section
                  - Better handling of post count to improve performance
                  - Moved pages to a separate section
                  - Added ability to delete pages in different status
                  - Added the option to schedule auto delete of tags by date
                  - Fixed a bug which was not allowing categories to be deleted based on date
2013-05-11 - v3.3 - (Dev time: 10 hours)
                  - Enhanced the deletion of posts using custom taxonomies
                  - Added the ability to schedule auto delete of taxonomies by date
                  - Cleaned up all messages that are shown to the user
                  - Added on screen help tab
2013-05-22 - v3.4 - (Dev time: 20 hours)
                  - Incorporated Screen API to select/deselect different sections of the page
                  - Load only sections that are selected by the user
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
 * Bulk Delete Main class
 */
class Bulk_Delete {
    
    const VERSION               = '3.4';
    const JS_HANDLE             = 'bulk-delete';
    const JS_VARIABLE           = 'BULK_DELETE';

    // Cron hooks
    const CRON_HOOK_CATS        = 'do-bulk-delete-cats';
    const CRON_HOOK_PAGES       = 'do-bulk-delete-pages';
    const CRON_HOOK_POST_STATUS = 'do-bulk-delete-post-status';
    const CRON_HOOK_TAGS        = 'do-bulk-delete-tags';
    const CRON_HOOK_TAXONOMY    = 'do-bulk-delete-taxonomy';

    // meta boxes
    const BOX_POST_STATUS       = 'bd_by_post_status';
    const BOX_CATEGORY          = 'bd_by_category';
    const BOX_TAG               = 'bd_by_tag';
    const BOX_TAX               = 'bd_by_tax';
    const BOX_PAGE              = 'bd_by_page';
    const BOX_URL               = 'bd_by_url';
    const BOX_POST_REVISION     = 'bd_by_post_revision';
    const BOX_DEBUG             = 'bd_debug';

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
	    //Add a submenu to Manage
        $this->admin_page = add_options_page(__("Bulk Delete", 'bulk-delete'), __("Bulk Delete", 'bulk-delete'), 'delete_posts', basename(__FILE__), array(&$this, 'display_setting_page'));
        $this->cron_page = add_options_page(__("Bulk Delete Schedules", 'bulk-delete'), __("Bulk Delete Schedules", 'bulk-delete'), 'delete_posts', 'bulk-delete-cron', array(&$this, 'display_cron_page'));

		add_action( "load-{$this->admin_page}", array(&$this,'create_settings_panel' ) );
        add_action('admin_print_scripts-' . $this->admin_page, array(&$this, 'add_script'));

        add_action( "add_meta_boxes_{$this->admin_page}", array( &$this, 'add_meta_boxes' ));
	}

    /**
     * Register meta boxes
     */
    function add_meta_boxes() {
        add_meta_box( self::BOX_POST_STATUS, __( 'By Post Status', 'bulk-delete' ), array( &$this, 'render_by_post_status_box' ), $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_CATEGORY, __( 'By Category', 'bulk-delete' ), array( &$this, 'render_by_category_box' ), $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_TAG, __( 'By Tag', 'bulk-delete' ), array( &$this, 'render_by_tag_box' ), $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_TAX, __( 'By Custom Taxonomy', 'bulk-delete' ), array( &$this, 'render_by_tax_box' ), $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_PAGE, __( 'By Page', 'bulk-delete' ), array( &$this, 'render_by_page_box' ), $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_URL, __( 'By URL', 'bulk-delete' ), array( &$this, 'render_by_url_box' ), $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_POST_REVISION, __( 'By Post Revision', 'bulk-delete' ), array( &$this, 'render_by_post_revision_box' ), $this->admin_page, 'advanced' );
        add_meta_box( self::BOX_DEBUG, __( 'Debug Information', 'bulk-delete' ), array( &$this, 'render_debug_box' ), $this->admin_page, 'advanced', 'low' );
    }

    /**
     * Add settings Panel
     */ 
	function create_settings_panel() {
 
		/** 
		 * Create the WP_Screen object using page handle
		 */
		$this->admin_screen = WP_Screen::get($this->admin_page);
 
		/**
		 * Content specified inline
		 */
		$this->admin_screen->add_help_tab(
			array(
				'title'    => __('About Plugin', 'bulk-delete'),
				'id'       => 'about_tab',
				'content'  => '<p>' . __('This plugin allows you to delete posts in bulk from selected categories, tags, custom taxonomies or by post status like drafts, pending posts, scheduled posts etc.', 'bulk-delete') . '</p>',
				'callback' => false
			)
		);
 
        // Add help sidebar
		$this->admin_screen->set_help_sidebar(
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
     * Enqueue JavaScript
     */
    function add_script() {
        global $wp_scripts;

        // uses code from http://trentrichardson.com/examples/timepicker/
        wp_enqueue_script( 'jquery-ui-timepicker', plugins_url('/js/jquery-ui-timepicker.js', __FILE__), array('jquery-ui-slider', 'jquery-ui-datepicker'), '1.1.1', true);
        wp_enqueue_script( self::JS_HANDLE, plugins_url('/js/bulk-delete.js', __FILE__), array('jquery-ui-timepicker'), self::VERSION, TRUE);

        $ui = $wp_scripts->query('jquery-ui-core');

        $url = "http://ajax.aspnetcdn.com/ajax/jquery.ui/{$ui->ver}/themes/smoothness/jquery-ui.css";
        wp_enqueue_style('jquery-ui-smoothness', $url, false, $ui->ver);
        wp_enqueue_style('jquery-ui-timepicker', plugins_url('/style/jquery-ui-timepicker.css', __FILE__), array(), '1.1.1');

        // JavaScript messages
        $msg = array(
            'deletewarning' => __('Are you sure you want to delete all the selected posts', 'bulk-delete')
        );

        $error = array(
            'selectone' => __('Please select posts from at least one option', 'bulk-delete'),
            'enterurl' => __('Please enter at least one page url', 'bulk-delete')
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
            $settings_link = '<a href="options-general.php?page=bulk-delete.php">' . __('Manage', 'bulk-delete') . '</a>';
            array_unshift( $links, $settings_link ); // before other links
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
     * Show the Admin page
     */
    function display_setting_page() {
?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php _e('Bulk Delete', 'bulk-delete');?></h2>

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
                <div style="background:#ff0;text-align:center;color: red;padding:1px">
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
     * Render post status box
     */
    function render_by_post_status_box() {

        if ( $this->is_hidden(self::BOX_POST_STATUS) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'options-general.php?page=bulk-delete.php' );
            return;
        }

        $posts_count = wp_count_posts();
        $drafts      = $posts_count->draft;
        $future      = $posts_count->future;
        $pending     = $posts_count->pending;
        $private     = $posts_count->private;
?>
        <h4><?php _e("Select the posts which you want to delete", 'bulk-delete'); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td scope="row" >
                    <input name="smbd_drafts" id ="smbd_drafts" value = "drafts" type = "checkbox" />
                    <label for="smbd_drafts"><?php _e("All Draft Posts", 'bulk-delete'); ?> (<?php echo $drafts . " "; _e("Drafts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_pending" id ="smbd_pending" value = "pending" type = "checkbox" />
                    <label for="smbd_pending"><?php _e("All Pending posts", 'bulk-delete'); ?> (<?php echo $pending . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_future" id ="smbd_future" value = "future" type = "checkbox" />
                    <label for="smbd_future"><?php _e("All scheduled posts", 'bulk-delete'); ?> (<?php echo $future . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_private" id ="smbd_private" value = "private" type = "checkbox" />
                    <label for="smbd_private"><?php _e("All private posts", 'bulk-delete'); ?> (<?php echo $private . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_post_status_restrict" id="smbd_post_status_restrict" value = "true" type = "checkbox">
                    <?php _e("Only restrict to posts which are ", 'bulk-delete');?>
                    <select name="smbd_post_status_op" id="smbd_post_status_op" disabled>
                        <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                        <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                    </select>
                    <input type ="textbox" name="smbd_post_status_days" id="smbd_post_status_days" disabled value ="0" maxlength="4" size="4" /><?php _e("days", 'bulk-delete');?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_post_status_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                    <input name="smbd_post_status_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_post_status_limit" id="smbd_post_status_limit" value = "true" type = "checkbox" >
                    <?php _e("Only delete first ", 'bulk-delete');?>
                    <input type ="textbox" name="smbd_post_status_limit_to" id="smbd_post_status_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                    <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_post_status_cron" value = "false" type = "radio" checked="checked" /> <?php _e('Delete now', 'bulk-delete'); ?>
                    <input name="smbd_post_status_cron" value = "true" type = "radio" id = "smbd_post_status_cron" disabled > <?php _e('Schedule', 'bulk-delete'); ?>
                    <input name="smbd_post_status_cron_start" id = "smbd_post_status_cron_start" value = "now" type = "text" disabled><?php _e('repeat ', 'bulk-delete');?>
                    <select name = "smbd_post_status_cron_freq" id = "smbd_post_status_cron_freq" disabled>
                        <option value = "-1"><?php _e("Don't repeat", 'bulk-delete'); ?></option>
<?php
        $schedules = wp_get_schedules();
        foreach($schedules as $key => $value) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php                        
        }
?>
                    </select>
                    <span class = "bd-post-status-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/buy-bulk-delete-post-status-addon">Buy now</a></span>
                </td>
            </tr>
        </table>
        </fieldset>

        <p>
            <button type="submit" name="smbd_action" value = "bulk-delete-post-status" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
        </p>
<?php        
    }

    /**
     * Render By category box
     */
    function render_by_category_box() {

        if ( $this->is_hidden(self::BOX_CATEGORY) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'options-general.php?page=bulk-delete.php' );
            return;
        }

?>
        <!-- Category Start-->
        <h4><?php _e("Select the categories whose post you want to delete", 'bulk-delete'); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
<?php
        $categories =  get_categories(array('hide_empty' => false));
        foreach ($categories as $category) {
?>
            <tr>
                <td scope="row" >
                    <input name="smbd_cats[]" value = "<?php echo $category->cat_ID; ?>" type = "checkbox" />
                </td>
                <td>
                    <label for="smbd_cats"><?php echo $category->cat_name; ?> (<?php echo $category->count . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>
<?php
        }
?>
            <tr>
                <td scope="row" >
                    <input name="smbd_cats_all" id ="smbd_cats_all" value = "-1" type = "checkbox" >
                </td>
                <td>
                    <label for="smbd_cats_all"><?php _e("All Categories", 'bulk-delete') ?></label>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_cats_restrict" id="smbd_cats_restrict" value = "true" type = "checkbox" >
                </td>
                <td>
                    <?php _e("Only restrict to posts which are ", 'bulk-delete');?>
                    <select name="smbd_cats_op" id="smbd_cats_op" disabled>
                        <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                        <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                    </select>
                    <input type ="textbox" name="smbd_cats_days" id="smbd_cats_days" disabled value ="0" maxlength="4" size="4" /><?php _e("days", 'bulk-delete');?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_cats_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                    <input name="smbd_cats_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_cats_private" value = "false" type = "radio" checked="checked" /> <?php _e('Public posts', 'bulk-delete'); ?>
                    <input name="smbd_cats_private" value = "true" type = "radio" /> <?php _e('Private Posts', 'bulk-delete'); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_cats_limit" id="smbd_cats_limit" value = "true" type = "checkbox">
                </td>
                <td>
                    <?php _e("Only delete first ", 'bulk-delete');?>
                    <input type ="textbox" name="smbd_cats_limit_to" id="smbd_cats_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                    <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_cats_cron" value = "false" type = "radio" checked="checked" /> <?php _e('Delete now', 'bulk-delete'); ?>
                    <input name="smbd_cats_cron" value = "true" type = "radio" id = "smbd_cats_cron" disabled > <?php _e('Schedule', 'bulk-delete'); ?>
                    <input name="smbd_cats_cron_start" id = "smbd_cats_cron_start" value = "now" type = "text" disabled><?php _e('repeat ', 'bulk-delete');?>
                    <select name = "smbd_cats_cron_freq" id = "smbd_cats_cron_freq" disabled>
                        <option value = "-1"><?php _e("Don't repeat", 'bulk-delete'); ?></option>
<?php
        $schedules = wp_get_schedules();
        foreach($schedules as $key => $value) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php                        
        }
?>
                    </select>
                    <span class = "bd-cats-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/buy-bulk-delete-category-addon">Buy now</a></span>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <?php _e("Enter time in Y-m-d H:i:s format or enter now to use current time", 'bulk-delete');?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="smbd_action" value = "bulk-delete-cats" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
        </p>
        <!-- Category end-->
<?php
    }

    function render_by_tag_box() {

        if ( $this->is_hidden(self::BOX_TAG) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'options-general.php?page=bulk-delete.php' );
            return;
        }

        $tags =  get_tags();
        if (count($tags) > 0) {
?>
            <h4><?php _e("Select the tags whose post you want to delete", 'bulk-delete') ?></h4>

            <!-- Tags start-->
            <fieldset class="options">
            <table class="optiontable">
<?php
            foreach ($tags as $tag) {
?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_tags[]" value = "<?php echo $tag->term_id; ?>" type = "checkbox" />
                    </td>
                    <td>
                        <label for="smbd_tags"><?php echo $tag->name; ?> (<?php echo $tag->count . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                    </td>
                </tr>
<?php
            }
?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_tags_all" id ="smbd_tags_all" value = "-1" type = "checkbox" >
                    </td>
                    <td>
                        <label for="smbd_tags_all"><?php _e("All Tags", 'bulk-delete') ?></label>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_tags_restrict" id ="smbd_tags_restrict" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e("Only restrict to posts which are ", 'bulk-delete');?>
                        <select name="smbd_tags_op" id="smbd_tags_op" disabled>
                            <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                            <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                        </select>
                        <input type ="textbox" name="smbd_tags_days" id ="smbd_tags_days" value ="0"  maxlength="4" size="4" disabled /><?php _e("days", 'bulk-delete');?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_tags_force_delete" value = "false" type = "radio" checked="checked" > <?php _e('Move to Trash', 'bulk-delete'); ?>
                        <input name="smbd_tags_force_delete" value = "true" type = "radio" > <?php _e('Delete permanently', 'bulk-delete'); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_tags_private" value = "false" type = "radio" checked="checked" /> <?php _e('Public posts', 'bulk-delete'); ?>
                        <input name="smbd_tags_private" value = "true" type = "radio" /> <?php _e('Private Posts', 'bulk-delete'); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_tags_limit" id="smbd_tags_limit" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e("Only delete first ", 'bulk-delete');?>
                        <input type ="textbox" name="smbd_tags_limit_to" id="smbd_tags_limit_to" disabled value ="0" maxlength="4" size="4" ><?php _e("posts.", 'bulk-delete');?>
                        <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                    </td>
                </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_tags_cron" value = "false" type = "radio" checked="checked" > <?php _e('Delete now', 'bulk-delete'); ?>
                    <input name="smbd_tags_cron" value = "true" type = "radio" id = "smbd_tags_cron" disabled > <?php _e('Schedule', 'bulk-delete'); ?>
                    <input name="smbd_tags_cron_start" id = "smbd_tags_cron_start" value = "now" type = "text" disabled><?php _e('repeat ', 'bulk-delete');?>
                    <select name = "smbd_tags_cron_freq" id = "smbd_tags_cron_freq" disabled>
                        <option value = "-1"><?php _e("Don't repeat", 'bulk-delete'); ?></option>
<?php
        $schedules = wp_get_schedules();
        foreach($schedules as $key => $value) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php                        
        }
?>
                    </select>
                    <span class = "bd-tags-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/buy-bulk-delete-tags-addon">Buy now</a></span>
                </td>
            </tr>

            </table>
            </fieldset>
            <p class="submit">
                <button type="submit" name="smbd_action" value = "bulk-delete-tags" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
            </p>
            <!-- Tags end-->
<?php
        } else {
?>
            <h4><?php _e("You don't have any posts assigned to tags in this blog.", 'bulk-delete') ?></h4>
<?php
        }
    }

    /**
     * Render delete by custom taxonomy box
     */
    function render_by_tax_box() {

        if ( $this->is_hidden(self::BOX_TAX) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'options-general.php?page=bulk-delete.php' );
            return;
        }

        $terms_array = array();

        $taxs =  get_taxonomies(array(
            'public'   => true,
            '_builtin' => false
            ), 'objects'
        );

        if (count($taxs) > 0) {
            foreach ($taxs as $tax) {
                $terms = get_terms($tax->name);
                if (count($terms) > 0) {
                    $terms_array[$tax->name] = $terms;
                }
            }
        }

        if ( count( $terms_array ) > 0 ) {
?>
            <!-- Custom tax Start-->
            <h4><?php _e("Select the taxonomies whose post you want to delete", 'bulk-delete') ?></h4>

            <fieldset class="options">
            <table class="optiontable">
<?php
            foreach ($terms_array as $tax => $terms) {
?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_taxs" value = "<?php echo $tax; ?>" type = "radio"  class = "custom-tax">
                    </td>
                    <td>
                        <label for="smbd_taxs"><?php echo $tax; ?> </label>
                    </td>
                </tr>
<?php
            }
?>
            </table>

            <h4><?php _e("The selected taxonomy has the following terms. Select the terms whose post you want to delete", 'bulk-delete') ?></h4>
<?php
            foreach ($terms_array as $tax => $terms) {
?>
            <table class="optiontable terms_<?php echo $tax;?> terms">
<?php
                foreach ($terms as $term) {
?>
                    <tr>
                        <td scope="row" >
                            <input name="smbd_tax_terms[]" value = "<?php echo $term->name; ?>" type = "checkbox" class = "terms" >
                        </td>
                        <td>
                            <label for="smbd_tax_terms"><?php echo $term->name; ?> (<?php echo $term->count . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                        </td>
                    </tr>
<?php
                }
?>
            </table>
<?php
            }
?>
            </table>
            <table class="optiontable">
                <tr>
                    <td colspan="2">
                        <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_taxs_restrict" id ="smbd_taxs_restrict" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e("Only restrict to posts which are ", 'bulk-delete');?>
                        <select name="smbd_taxs_op" id="smbd_taxs_op" disabled>
                            <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                            <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                        </select>
                        <input type ="textbox" name="smbd_taxs_days" id ="smbd_taxs_days" value ="0"  maxlength="4" size="4" disabled /><?php _e("days", 'bulk-delete');?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_taxs_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                        <input name="smbd_taxs_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_taxs_private" value = "false" type = "radio" checked="checked" /> <?php _e('Public posts', 'bulk-delete'); ?>
                        <input name="smbd_taxs_private" value = "true" type = "radio" /> <?php _e('Private Posts', 'bulk-delete'); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_taxs_limit" id="smbd_taxs_limit" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e("Only delete first ", 'bulk-delete');?>
                        <input type ="textbox" name="smbd_taxs_limit_to" id="smbd_taxs_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                        <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                    </td>
                </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_taxs_cron" value = "false" type = "radio" checked="checked" > <?php _e('Delete now', 'bulk-delete'); ?>
                    <input name="smbd_taxs_cron" value = "true" type = "radio" id = "smbd_taxs_cron" disabled > <?php _e('Schedule', 'bulk-delete'); ?>
                    <input name="smbd_taxs_cron_start" id = "smbd_taxs_cron_start" value = "now" type = "text" disabled><?php _e('repeat ', 'bulk-delete');?>
                    <select name = "smbd_taxs_cron_freq" id = "smbd_taxs_cron_freq" disabled>
                        <option value = "-1"><?php _e("Don't repeat", 'bulk-delete'); ?></option>
<?php
        $schedules = wp_get_schedules();
        foreach($schedules as $key => $value) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php                        
        }
?>
                    </select>
                    <span class = "bd-taxs-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/buy-bulk-delete-taxonomy-addon">Buy now</a></span>
                </td>
            </tr>

            </table>
            </fieldset>
            <p class="submit">
                <button type="submit" name="smbd_action" value = "bulk-delete-taxs" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
            </p>
            <!-- Custom tax end-->
<?php
        } else {
?>
            <h4><?php _e("You don't have any posts assigned to custom taxonomies in this blog.", 'bulk-delete') ?></h4>
<?php
        }
    }

    /**
     * Render delete by pages box
     */
    function render_by_page_box() {

        if ( $this->is_hidden(self::BOX_PAGE) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'options-general.php?page=bulk-delete.php' );
            return;
        }

        $pages_count  = wp_count_posts( 'page' );
        $pages        = $pages_count->publish;
        $page_drafts  = $pages_count->draft;
        $page_future  = $pages_count->future;
        $page_pending = $pages_count->pending;
        $page_private = $pages_count->private;
?>
        <!-- Pages start-->
        <h4><?php _e("Select the pages which you want to delete", 'bulk-delete'); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td>
                    <input name="smbd_published_pages" value = "published_pages" type = "checkbox" />
                    <label for="smbd_published_pages"><?php _e("All Published Pages", 'bulk-delete'); ?> (<?php echo $pages . " "; _e("Pages", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_draft_pages" value = "draft_pages" type = "checkbox" />
                    <label for="smbd_draft_pages"><?php _e("All Draft Pages", 'bulk-delete'); ?> (<?php echo $page_drafts . " "; _e("Pages", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_future_pages" value = "scheduled_pages" type = "checkbox" />
                    <label for="smbd_future_pages"><?php _e("All Scheduled Pages", 'bulk-delete'); ?> (<?php echo $page_future . " "; _e("Pages", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_pending_pages" value = "pending_pages" type = "checkbox" />
                    <label for="smbd_pending_pages"><?php _e("All Pending Pages", 'bulk-delete'); ?> (<?php echo $page_pending . " "; _e("Pages", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_private_pages" value = "private_pages" type = "checkbox" />
                    <label for="smbd_private_pages"><?php _e("All Private Pages", 'bulk-delete'); ?> (<?php echo $page_private . " "; _e("Pages", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_pages_restrict" id="smbd_pages_restrict" value = "true" type = "checkbox">
                    <?php _e("Only restrict to pages which are ", 'bulk-delete');?>
                    <select name="smbd_pages_op" id="smbd_pages_op" disabled>
                        <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                        <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                    </select>
                    <input type ="textbox" name="smbd_pages_days" id="smbd_pages_days" disabled value ="0" maxlength="4" size="4" /><?php _e("days", 'bulk-delete');?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_pages_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                    <input name="smbd_pages_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_pages_limit" id="smbd_pages_limit" value = "true" type = "checkbox">
                    <?php _e("Only delete first ", 'bulk-delete');?>
                    <input type ="textbox" name="smbd_pages_limit_to" id="smbd_pages_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                    <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_pages_cron" value = "false" type = "radio" checked="checked" /> <?php _e('Delete now', 'bulk-delete'); ?>
                    <input name="smbd_pages_cron" value = "true" type = "radio" id = "smbd_pages_cron" disabled > <?php _e('Schedule', 'bulk-delete'); ?>
                    <input name="smbd_pages_cron_start" id = "smbd_pages_cron_start" value = "now" type = "text" disabled><?php _e('repeat ', 'bulk-delete');?>
                    <select name = "smbd_pages_cron_freq" id = "smbd_pages_cron_freq" disabled>
                        <option value = "-1"><?php _e("Don't repeat", 'bulk-delete'); ?></option>
<?php
        $schedules = wp_get_schedules();
        foreach($schedules as $key => $value) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php                        
        }
?>
                    </select>
                    <span class = "bd-pages-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/buy-bulk-delete-pages-addon">Buy now</a></span>
                </td>
            </tr>
        </table>
        </fieldset>

        <p>
            <button type="submit" name="smbd_action" value = "bulk-delete-page" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
        </p>
        <!-- Pages end-->
<?php
    }

    /**
     * Render delete by url box
     */
    function render_by_url_box() {

        if ( $this->is_hidden(self::BOX_URL) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'options-general.php?page=bulk-delete.php' );
            return;
        }

?>
        <!-- URLs start-->
        <h4><?php _e("Delete these specific pages", 'bulk-delete'); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td scope="row"> 
                    <label for="smdb_specific_pages"><?php _e("Enter one post url (not post ids) per line", 'bulk-delete'); ?></label>
                    <br/>
                    <textarea style="width: 450px; height: 80px;" id="smdb_specific_pages_urls" name="smdb_specific_pages_urls" rows="5" columns="80" ></textarea>
                </td>
            </tr>
            
            <tr>
                <td>
                    <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_specific_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                    <input name="smbd_specific_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="smbd_action" value = "bulk-delete-specific" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
        </p>
        <!-- URLs end-->
<?php
    }

    /**
     * Render delete by post revisions box
     */
    function render_by_post_revision_box() {

        if ( $this->is_hidden(self::BOX_POST_REVISION) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'options-general.php?page=bulk-delete.php' );
            return;
        }

        global $wpdb;

        $revisions = $wpdb->get_var("select count(*) from $wpdb->posts where post_type = 'revision'");
?>
        <!-- Post Revisions start-->
        <h4><?php _e("Select the posts which you want to delete", 'bulk-delete'); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td>
                    <input name="smbd_revisions" id ="smbd_revisions" value = "revisions" type = "checkbox" />
                    <label for="smbd_revisions"><?php _e("All Revisions", 'bulk-delete'); ?> (<?php echo $revisions . " "; _e("Revisions", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="smbd_action" value = "bulk-delete-revisions" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
        </p>
        <!-- Post Revisions end-->
<?php
    }

    /**
     * Render debug box
     */
    function render_debug_box() {
?>
        <!-- Debug box start-->
        <p>
            <?php _e('If you are seeing a blank page after clicking the Bulk Delete button, then ', 'bulk-delete'); ?><a href = "http://sudarmuthu.com/wordpress/bulk-delete#faq"><?php _e('check out this FAQ', 'bulk-delete');?></a>. 
            <?php _e('You also need need the following debug information.', 'bulk-delete'); ?>
        </p>
        <table cellspacing="10">
            <tr>
                <th align = "right"><?php _e('PHP Version ', 'bulk-delete'); ?></th>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <th align = "right"><?php _e('Plugin Version ', 'bulk-delete'); ?></th>
                <td><?php echo self::VERSION; ?></td>
            </tr>
            <tr>
                <th align = "right"><?php _e('Available memory size ', 'bulk-delete');?></th>
                <td><?php echo ini_get( 'memory_limit' ); ?></td>
            </tr>
            <tr>
                <th align = "right"><?php _e('Script time out ', 'bulk-delete');?></th>
                <td><?php echo ini_get( 'max_execution_time' ); ?></td>
            </tr>
            <tr>
                <th align = "right"><?php _e('Script input time ', 'bulk-delete'); ?></th>
                <td><?php echo ini_get( 'max_input_time' ); ?></td>
            </tr>
        </table>
        <!-- Debug box end-->
<?php
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
        $cron_list_table->prepare_items($this->get_cron_schedules());
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
        if (isset($_GET['smbd_action'])) {

            switch($_GET['smbd_action']) {

                case 'delete-cron':
                    //TODO: Check for nonce and referer
                    $cron_id = absint($_GET['cron_id']);
                    $cron_items = $this->get_cron_schedules();
                    wp_unschedule_event($cron_items[$cron_id]['timestamp'], $cron_items[$cron_id]['type'], $cron_items[$cron_id]['args']);

                    $this->msg = __('The selected scheduled job was successfully deleted ', 'bulk-delete');
                    // hook the admin notices action
                    add_action( 'admin_notices', array(&$this, 'deleted_notice'), 9 );

                    break;
            }
        }

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
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=bulk-delete-cron');
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
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=bulk-delete-cron');
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
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=bulk-delete-cron');
                    } else {
                        $deleted_count = self::delete_taxs($delete_options);
                        $this->msg = sprintf( _n('Deleted %d post from the selected custom taxonomies', 'Deleted %d posts from the selected custom taxonomies' , $deleted_count, 'bulk-delete' ), $deleted_count);
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
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=bulk-delete-cron');
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
                            sprintf( __('See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete'), get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=bulk-delete-cron');
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
            }

            // hook the admin notices action
            add_action( 'admin_notices', array(&$this, 'deleted_notice'), 9 );
        }
    }

    /**
     * Show deleted notice messages
     */
    function deleted_notice() {
        if ($this->msg != '') {
            echo "<div class = 'updated'><p>" . $this->msg . "</p></div>";
        }

        // cleanup
        $this->msg = '';
        remove_action( 'admin_notices', array( &$this, 'deleted_notice' ));
    }

    /**
     * @brief Check whether the box is hidden or not
     *
     * @param $box
     *
     * @return 
     */
    private function is_hidden( $box ) {
        $hidden_boxes = $this->get_hidden_boxes();
        return ( is_array( $hidden_boxes ) && in_array( $box, $hidden_boxes ) );
    }

    /**
     * Get the list of hidden boxes
     *
     * @return the array of hidden meta boxes
     */
    private function get_hidden_boxes() {
        $current_user = wp_get_current_user();
        return get_user_meta( $current_user->ID, 'metaboxhidden_settings_page_bulk-delete', TRUE );
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

    /**
     * Get the list of cron schedules
     *
     * @return array - The list of cron schedules
     */
    private function get_cron_schedules() {

        $cron_items = array();
		$cron = _get_cron_array();
		$date_format = _x( 'M j, Y @ G:i', 'Cron table date format', 'bulk-delete' );
        $i = 0;

		foreach ( $cron as $timestamp => $cronhooks ) {
			foreach ( (array) $cronhooks as $hook => $events ) {
                if (substr($hook, 0, 15) == 'do-bulk-delete-') {
                    $cron_item = array();

                    foreach ( (array) $events as $key => $event ) {
                        $cron_item['timestamp'] = $timestamp;
                        $cron_item['due'] = date_i18n( $date_format, $timestamp + ( get_option('gmt_offset') * 60 * 60 ) );
                        $cron_item['schedule'] = $event['schedule'];
                        $cron_item['type'] = $hook;
                        $cron_item['args'] = $event['args'];
                        $cron_item['id'] = $i;
                    }

                    $cron_items[$i] = $cron_item;
                    $i++;
                }
            }
        }
        return $cron_items;
    }
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'Bulk_Delete' ); function Bulk_Delete() { global $Bulk_Delete; $Bulk_Delete = new Bulk_Delete(); }

/**
 * Check whether a key is present. If present returns the value, else returns the default value
 *
 * @param <array> $array - Array whose key has to be checked
 * @param <string> $key - key that has to be checked
 * @param <string> $default - the default value that has to be used, if the key is not found (optional)
 *
 * @return <mixed> If present returns the value, else returns the default value
 * @author Sudar
 */
if (!function_exists('array_get')) {
    function array_get($array, $key, $default = NULL) {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
?>
