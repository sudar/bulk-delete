<?php
/*
Plugin Name: Bulk Delete
Plugin Script: bulk-delete.php
Plugin URI: http://sudarmuthu.com/wordpress/bulk-delete
Description: Bulk delete posts from selected categories, tags, custom taxonomies or by post type like drafts, scheduled posts, revisions etc.
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
Version: 3.1
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
    
    const VERSION = '3.1';
    const JS_HANDLE = 'bulk-delete';
    const JS_VARIABLE = 'BULK_DELETE';

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
        add_filter('plugin_action_links', array(&$this, 'filter_plugin_actions'), 10, 2 );
        add_filter( 'plugin_row_meta', array( &$this, 'add_plugin_links' ), 10, 2 );  
    }

    /**
     * Add navigation menu
     */
	function add_menu() {
	    //Add a submenu to Manage
        $this->admin_page = add_options_page(__("Bulk Delete", 'bulk-delete'), __("Bulk Delete", 'bulk-delete'), 'delete_posts', basename(__FILE__), array(&$this, 'display_setting_page'));
        $this->cron_page = add_options_page(__("Bulk Delete Schedules", 'bulk-delete'), __("Bulk Delete Schedules", 'bulk-delete'), 'delete_posts', 'bulk-delete-cron', array(&$this, 'display_cron_page'));

        add_action('admin_print_scripts-' . $this->admin_page, array(&$this, 'add_script'));
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
            'deletewarning' => __('Are you sure you want to delete all the selected posts', 'bulk-delete'),
            'selectone' => __('Please select at least one option', 'bulk-delete')
        );
        $translation_array = array( 'msg' => $msg );
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
            array( '<a href="http://sudarmuthu.com/out/bulk-delete-addons" target="_blank">' . __('Buy Addons', 'bulk-delete') . '</a>' )
        );
        return $links;
    }

    /**
     * Show the Admin page
     */
    function display_setting_page() {
        global $wpdb;
?>
	<div class="updated fade" style="background:#ff0;text-align:center;color: red;"><p><strong><?php _e("WARNING: Posts deleted once cannot be retrieved back. Use with caution.", 'bulk-delete'); ?></strong></p></div>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e('Bulk Delete', 'bulk-delete');?></h2>

        <div id = "poststuff" style = "float:left; width:75%">

        <div class = "postbox">
            <div class = "handlediv"> <br> </div>
            <h3 class = "hndle"><span><?php _e("By Type", 'bulk-delete'); ?></span></h3>
        <div class = "inside">
        <h4><?php _e("Select the posts which you want to delete", 'bulk-delete'); ?></h4>

        <form name="smbd_form" id = "smbd_misc_form" action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post" onsubmit="return bd_validateForm(this);">

<?php
        $wp_query = new WP_Query;
        $drafts = $wpdb->get_var("select count(*) from $wpdb->posts where post_status = 'draft'");
        $revisions = $wpdb->get_var("select count(*) from $wpdb->posts where post_type = 'revision'");
        $pending = $wpdb->get_var("select count(*) from $wpdb->posts where post_status = 'pending'");
        $future = $wpdb->get_var("select count(*) from $wpdb->posts where post_status = 'future'");
        $private = $wpdb->get_var("select count(*) from $wpdb->posts where post_status = 'private'");
?>
        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td scope="row" >
                    <input name="smbd_drafts" id ="smbd_drafts" value = "drafts" type = "checkbox" />
                    <label for="smbd_drafts"><?php _e("All Drafts", 'bulk-delete'); ?> (<?php echo $drafts . " "; _e("Drafts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_revisions" id ="smbd_revisions" value = "revisions" type = "checkbox" />
                    <label for="smbd_revisions"><?php _e("All Revisions", 'bulk-delete'); ?> (<?php echo $revisions . " "; _e("Revisions", 'bulk-delete'); ?>)</label>
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
                    <input name="smbd_special_restrict" id="smbd_special_restrict" value = "true" type = "checkbox"  onclick="toggle_date_restrict('special');" />
                    <?php _e("Only restrict to posts which are ", 'bulk-delete');?>
                    <select name="smbd_special_op" id="smbd_special_op" disabled>
                        <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                        <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                    </select>
                    <input type ="textbox" name="smbd_special_days" id="smbd_special_days" disabled value ="0" maxlength="4" size="4" /><?php _e("days", 'bulk-delete');?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_special_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                    <input name="smbd_special_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_special_limit" id="smbd_special_limit" value = "true" type = "checkbox"  onclick="toggle_limit_restrict('special');" />
                    <?php _e("Only delete first ", 'bulk-delete');?>
                    <input type ="textbox" name="smbd_special_limit_to" id="smbd_special_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                    <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p class="submit">
            <input type="submit" name="submit" class="button-primary" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
        </p>

        <?php wp_nonce_field('bulk-delete-posts'); ?>

        <input type="hidden" name="smbd_action" value="bulk-delete-special" />
        </form>
        </div>
        </div>


        <div class = "postbox">
            <div class = "handlediv"> <br> </div>
            <h3 class = "hndle"><span><?php _e("By Pages", 'bulk-delete'); ?></span></h3>
        <div class = "inside">
        <h4><?php _e("Select the pages which you want to delete", 'bulk-delete'); ?></h4>

        <form name="smbd_form" id = "smbd_page_form" action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post"
        onsubmit="return bd_validateForm(this);">

<?php
        $wp_query = new WP_Query;
        $pages = $wpdb->get_var("select count(*) from $wpdb->posts where post_type = 'page' AND post_status = 'publish' ");
?>
        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td>
                    <input name="smbd_pages" value = "pages" type = "checkbox" />
                    <label for="smbd_pages"><?php _e("All Pages", 'bulk-delete'); ?> (<?php echo $pages . " "; _e("Pages", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_page_restrict" id="smbd_page_restrict" value = "true" type = "checkbox"  onclick="toggle_date_restrict('page');" />
                    <?php _e("Only restrict to pages which are ", 'bulk-delete');?>
                    <select name="smbd_page_op" id="smbd_page_op" disabled>
                        <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                        <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                    </select>
                    <input type ="textbox" name="smbd_page_days" id="smbd_page_days" disabled value ="0" maxlength="4" size="4" /><?php _e("days", 'bulk-delete');?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_page_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                    <input name="smbd_page_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_page_limit" id="smbd_page_limit" value = "true" type = "checkbox"  onclick="toggle_limit_restrict('page');" />
                    <?php _e("Only delete first ", 'bulk-delete');?>
                    <input type ="textbox" name="smbd_page_limit_to" id="smbd_page_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                    <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p class="submit">
            <input type="submit" name="submit" class="button-primary" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
        </p>

        <?php wp_nonce_field('bulk-delete-posts'); ?>

        <input type="hidden" name="smbd_action" value="bulk-delete-page" />
        </form>
        </div>
        </div>


        <div class = "postbox">
            <div class = "handlediv"> <br> </div>
            <h3 class = "hndle"><span><?php _e("By Urls", 'bulk-delete'); ?></span></h3>
        <div class = "inside">
        <h4><?php _e("Delete these specific pages", 'bulk-delete'); ?></h4>

        <form name="smbd_form" id = "smbd_specific_form" action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post" >

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

        <p class="submit">
            <input type="submit" name="submit" class="button-primary" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
        </p>

        <?php wp_nonce_field('bulk-delete-posts'); ?>

        <input type="hidden" name="smbd_action" value="bulk-delete-specific" >
        </form>
        </div>
        </div>


        <div class = "postbox">
            <div class = "handlediv">
                <br>
            </div>
                <h3 class = "hndle"><span><?php _e("By Category", 'bulk-delete'); ?></span></h3>
            <div class = "inside">
        <h4><?php _e("Select the categories whose post you want to delete", 'bulk-delete'); ?></h4>

        <form name="smbd_form" id = "smbd_cat_form"
        action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post" onsubmit="return bd_validateForm(this);">

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
                    <input name="smbd_cats_all" id ="smbd_cats_all" value = "-1" type = "checkbox" onclick="bd_checkAll(document.getElementById('smbd_cat_form'));" />
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
                    <input name="smbd_cats_restrict" id="smbd_cats_restrict" value = "true" type = "checkbox"  onclick="toggle_date_restrict('cats');" />
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
                    <input name="smbd_cats_limit" id="smbd_cats_limit" value = "true" type = "checkbox"  onclick="toggle_limit_restrict('cats');" />
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
                    <span class = "bd-cats-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/bulk-delete-category-addon">Buy now</a></span>
                </td>
            </tr>
            <tr>
                <td scope="row" colspan="2">
                    <?php _e("Enter time in Y-m-d H:i:s format or enter now to use current time", 'bulk-delete');?>
                </td>
            </tr>

        </table>
        </fieldset>
        <p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
        </p>

<?php wp_nonce_field('bulk-delete-posts'); ?>

		<input type="hidden" name="smbd_action" value="bulk-delete-cats" />
		</form>
        </div>
        </div>
<?php
        $tags =  get_tags();
        if (count($tags) > 0) {
?>
        <div class = "postbox">
            <div class = "handlediv">
                <br>
            </div>

            <h3 class = "hndle"><span><?php _e("By Tags", 'bulk-delete'); ?></span></h3>

            <div class = "inside">
            <h4><?php _e("Select the tags whose post you want to delete", 'bulk-delete') ?></h4>

            <form name="smbd_form" id = "smbd_tag_form"
            action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post"
            onsubmit="return bd_validateForm(this);">

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
                        <input name="smbd_tags_all" id ="smbd_tags_all" value = "-1" type = "checkbox" onclick="bd_checkAll(document.getElementById('smbd_tag_form'));" />
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
                        <input name="smbd_tags_restrict" id ="smbd_tags_restrict" value = "true" type = "checkbox" onclick="toggle_date_restrict('tags');" />
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
                        <input name="smbd_tags_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                        <input name="smbd_tags_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
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
                        <input name="smbd_tags_limit" id="smbd_tags_limit" value = "true" type = "checkbox"  onclick="toggle_limit_restrict('tags');" />
                    </td>
                    <td>
                        <?php _e("Only delete first ", 'bulk-delete');?>
                        <input type ="textbox" name="smbd_tags_limit_to" id="smbd_tags_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                        <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                    </td>
                </tr>

            </table>
            </fieldset>
            <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
            </p>

    <?php wp_nonce_field('bulk-delete-posts'); ?>

            <input type="hidden" name="smbd_action" value="bulk-delete-tags" />
            </form>
            </div>
            </div>
<?php
        }
?>

<?php
        $customTaxs =  get_taxonomies();
        if (count($customTaxs) > 0) {
?>
        <div class = "postbox">
            <div class = "handlediv">
                <br>
            </div>
            <h3 class = "hndle"><span><?php _e("By Taxonomies", 'bulk-delete'); ?></span></h3>
            <div class = "inside">
            <h4><?php _e("Select the taxonomies whose post you want to delete", 'bulk-delete') ?></h4>

            <form name="smbd_form" id = "smbd_tax_form"
            action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post"
            onsubmit="return bd_validateForm(this);">

            <fieldset class="options">
            <table class="optiontable">
    <?php
            foreach ($customTaxs as $taxs) {

                $posts = smbd_get_tax_post($taxs);
    ?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_taxs[]" value = "<?php echo $taxs; ?>" type = "checkbox" />
                    </td>
                    <td>
                        <label for="smbd_taxs"><?php echo $taxs; ?> (<?php echo count($posts) . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                    </td>
                </tr>
    <?php
            }
    ?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_taxs_all" id ="smbd_taxs_all" value = "-1" type = "checkbox" onclick="bd_checkAll(document.getElementById('smbd_tax_form'));" />
                    </td>
                    <td>
                        <label for="smbd_taxs_all"><?php _e("All Taxonomies", 'bulk-delete') ?></label>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_taxs_restrict" id ="smbd_taxs_restrict" value = "true" type = "checkbox" onclick="toggle_date_restrict('taxs');" />
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
                        <input name="smbd_taxs_limit" id="smbd_taxs_limit" value = "true" type = "checkbox"  onclick="toggle_limit_restrict('taxs');" />
                    </td>
                    <td>
                        <?php _e("Only delete first ", 'bulk-delete');?>
                        <input type ="textbox" name="smbd_taxs_limit_to" id="smbd_taxs_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                        <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                    </td>
                </tr>

            </table>
            </fieldset>
            <p class="submit">
                    <input type="submit" class="button-primary" name="submit" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
            </p>

    <?php wp_nonce_field('bulk-delete-posts'); ?>

            <input type="hidden" name="smbd_action" value="bulk-delete-taxs" />
            </form>
            </div>
            </div>
<?php
        }
?>
        <div class = "postbox">
            <div class = "handlediv">
                <br>
            </div>
            <h3 class = "hndle"><span><?php _e('Debug Information', 'bulk-delete'); ?></span></h3>
            <div class = "inside">
            <p><?php _e('If you are seeing a blank page after clicking the Bulk Delete button, then ', 'bulk-delete'); ?><a href = "http://sudarmuthu.com/wordpress/bulk-delete#faq-white-screen"><?php _e('check out this FAQ', 'bulk-delete');?></a>. 
                <?php _e('You also need need the following debug information.', 'bulk-delete'); ?></p>
                <table cellspacing="10">
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
            </div>
        </div>
    </div>
        
    <iframe frameBorder="0" height = "950" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option('admin_color'); ?>"></iframe>
    
    </div>
<?php

    // Display credits in Footer
    add_action( 'in_admin_footer', array(&$this, 'admin_footer' ));
    }

    /**
     * Adds Footer links. Based on http://striderweb.com/nerdaphernalia/2008/06/give-your-wordpress-plugin-credit/
     */
    function admin_footer() {
        $plugin_data = get_plugin_data( __FILE__ );
        printf('%1$s ' . __("plugin", 'bulk-delete') .' | ' . __("Version", 'bulk-delete') . ' %2$s | '. __('by', 'bulk-delete') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
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
    }

    /**
     * Request Handler
     */
    function request_handler() {
        global $wpdb;

        if (isset($_GET['smbd_action'])) {

            switch($_GET['smbd_action']) {

                case 'delete-cron':
                    //TODO: Check for nonce and referer
                    $cron_id = absint($_GET['cron_id']);
                    $cron_items = $this->get_cron_schedules();
                    wp_unschedule_event($cron_items[$cron_id]['timestamp'], $cron_items[$cron_id]['type'], $cron_items[$cron_id]['args']);

                    break;
            }
        }

        if (isset($_POST['smbd_action'])) {

            $wp_query = new WP_Query;
            check_admin_referer( 'bulk-delete-posts');

            switch($_POST['smbd_action']) {

                case "bulk-delete-cats":
                    // delete by cats

                    $delete_options = array();
                    $delete_options['selected_cats'] = array_get($_POST, 'smbd_cats');
                    $delete_options['restrict'] = array_get($_POST, 'smbd_cats_restrict', FALSE);
                    $delete_options['private'] = array_get($_POST, 'smbd_cats_private');
                    $delete_options['limit_to'] = absint(array_get($_POST, 'smbd_cats_limits_to', 0));
                    $delete_options['force_delete'] = array_get($_POST, 'smbd_cats_force_delete', 'false');

                    $delete_options['cats_op'] = array_get($_POST, 'smbd_cats_op');
                    $delete_options['cats_days'] = array_get($_POST, 'smbd_cats_days');
                    
                    if (array_get($_POST, 'smbd_cats_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_cats_cron_freq'];
                        $time = strtotime($_POST['smbd_cats_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, 'do-bulk-delete-cats', array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq , 'do-bulk-delete-cats', array($delete_options));
                        }
                    } else {
                        self::delete_cats($delete_options);
                    }
                    
                    break;

                case "bulk-delete-tags":
                    // delete by tags
                    $selected_tags = array_get($_POST, 'smbd_tags');
                    if (array_get($_POST, 'smbd_tags_restrict', 'false') == "true") {
                        add_filter ('posts_where', 'smbd_tags_by_days');
                    }

                    $private = array_get($_POST, 'smbd_tags_private', 'false');

                    if ($private == 'true') {
                        $options = array('tag__in'=>$selected_tags,'post_status'=>'private', 'post_type'=>'post');
                    } else {
                        $options = array('tag__in'=>$selected_tags,'post_status'=>'publish', 'post_type'=>'post');
                    }

                    $limit_to = absint(array_get($_POST, 'smbd_tags_limits_to', 0));

                    if ($limit_to > 0) {
                        $options['showposts'] = $limit_to;
                    } else {
                        $options['nopaging'] = 'true';
                    }

                    $force_delete = array_get($_POST, 'smbd_tags_force_delete');

                    if ($force_delete == 'true') {
                        $force_delete = true;
                    } else {
                        $force_delete = false;
                    }

                    $posts = $wp_query->query($options);
                    
                    foreach ($posts as $post) {
                        wp_delete_post($post->ID, $force_delete);
                    }
                    
                    break;

                case "bulk-delete-taxs":
                    // delete by taxs
                    $selected_taxs = array_get($_POST, 'smbd_taxs');

                    foreach ($selected_taxs as $selected_tax) {
                        $postids = smbd_get_tax_post($selected_tax);
                        
                        if (array_get($_POST, 'smbd_taxs_restrict', 'false') == "true") {
                            add_filter ('posts_where', 'smbd_taxs_by_days');
                        }

                        $private = array_get($_POST, 'smbd_taxs_private');

                        if ($private == 'true') {
                            $options = array('post__in'=>$postids,'post_status'=>'private', 'post_type'=>'post');
                        } else {
                            $options = array('post__in'=>$postids,'post_status'=>'publish', 'post_type'=>'post');
                        }

                        $limit_to = absint(array_get($_POST, 'smbd_taxs_limits_to', 0));

                        if ($limit_to > 0) {
                            $options['showposts'] = $limit_to;
                        } else {
                            $options['nopaging'] = 'true';
                        }

                        $force_delete = array_get($_POST, 'smbd_taxs_force_delete');

                        if ($force_delete == 'true') {
                            $force_delete = true;
                        } else {
                            $force_delete = false;
                        }

                        $posts = $wp_query->query($options);
                        foreach ($posts as $post) {
                            wp_delete_post($post->ID, $force_delete);
                        }
                    }
                    
                    break;

                case "bulk-delete-special":
                    // Delete special types like drafts, reviesion etc
                    
                    $delete_options = array();
                    $delete_options['restrict'] = array_get($_POST, 'smbd_special_restrict', FALSE);
                    $delete_options['limit_to'] = absint(array_get($_POST, 'smbd_special_limits_to', 0));
                    $delete_options['force_delete'] = array_get($_POST, 'smbd_special_force_delete', 'false');

                    $delete_options['special_op'] = array_get($_POST, 'smbd_special_op');
                    $delete_options['special_days'] = array_get($_POST, 'smbd_special_days');

                    $delete_options['drafts'] = array_get($_POST, 'smbd_drafts');
                    $delete_options['revisions'] = array_get($_POST, 'smbd_revisions');
                    $delete_options['pending'] = array_get($_POST, 'smbd_pending');
                    $delete_options['future'] = array_get($_POST, 'smbd_future');
                    $delete_options['private'] = array_get($_POST, 'smbd_private');
                    
                    if (array_get($_POST, 'smbd_special_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_special_cron_freq'];
                        $time = strtotime($_POST['smbd_special_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, 'do-bulk-delete-special', array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq , 'do-bulk-delete-special', array($delete_options));
                        }
                    } else {
                        self::delete_special($delete_options);
                    }
                    
                    break;

                case "bulk-delete-page":
                    // Delete pages
                    
                    $delete_options = array();
                    $delete_options['restrict'] = array_get($_POST, 'smbd_page_restrict', FALSE);
                    $delete_options['limit_to'] = absint(array_get($_POST, 'smbd_page_limits_to', 0));
                    $delete_options['force_delete'] = array_get($_POST, 'smbd_page_force_delete', 'false');

                    $delete_options['page_op'] = array_get($_POST, 'smbd_page_op');
                    $delete_options['page_days'] = array_get($_POST, 'smbd_page_days');

                    $delete_options['pages'] = array_get($_POST, 'smbd_pages');

                    if (array_get($_POST, 'smbd_page_cron', 'false') == 'true') {
                        $freq = $_POST['smbd_page_cron_freq'];
                        $time = strtotime($_POST['smbd_page_cron_start']) - ( get_option('gmt_offset') * 60 * 60 );

                        if ($freq == -1) {
                            wp_schedule_single_event($time, 'do-bulk-delete-page', array($delete_options));
                        } else {
                            wp_schedule_event($time, $freq , 'do-bulk-delete-page', array($delete_options));
                        }
                    } else {
                        self::delete_pages($delete_options);
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
        echo "<div class = 'updated'><p>" . __("All the selected posts have been successfully deleted.", 'bulk-delete') . "</p></div>";
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
            $options['cats_op'] = $delete_options['cats_op'];
            $options['cats_days'] = $delete_options['cats_days'];

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
    }

    /**
     * Delete special type of posts - drafts, revisions etc.
     */
    static function delete_special($delete_options) {
        global $wp_query;

        $options = array();

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
            $options['op'] = $delete_options['special_op'];
            $options['days'] = $delete_options['special_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        // Drafts
        if ("drafts" == $delete_options['drafts']) {
            $options['post_status'] = 'draft';
            $drafts = $wp_query->query($options);

            foreach ($drafts as $draft) {
                wp_delete_post($draft->ID, $force_delete);
            }
        }

        // Revisions
        if ("revisions" == $delete_options['revisions']) {
            $revisions = $wpdb->get_results("select ID from $wpdb->posts where post_type = 'revision'");

            foreach ($revisions as $revision) {
                wp_delete_post($revision->ID, $force_delete);
            }
        }

        // Pending Posts
        if ("pending" == $delete_options['pending']) {
            $pendings = $wpdb->get_results("select ID from $wpdb->posts where post_status = 'pending'");

            foreach ($pendings as $pending) {
                wp_delete_post($pending->ID, $force_delete);
            }
        }

        // Future Posts
        if ("future" == $delete_options['future']) {
            $futures = $wpdb->get_results("select ID from $wpdb->posts where post_status = 'future'");

            foreach ($futures as $future) {
                wp_delete_post($future->ID, $force_delete);
            }
        }

        // Private Posts
        if ("private" == $delete_options['private']) {
            $privates = $wpdb->get_results("select ID from $wpdb->posts where post_status = 'private'");

            foreach ($privates as $private) {
                wp_delete_post($private->ID, $force_delete);
            }
        }
    }

    /**
     * Delete pages
     */
    static function delete_pages($delete_options) {
        global $wp_query;

        $options = array();

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
            $options['op'] = $delete_options['page_op'];
            $options['days'] = $delete_options['page_days'];

            if (!class_exists('Bulk_Delete_By_Days')) {
                require_once dirname(__FILE__) . '/include/class-bulk-delete-by-days.php';
            }
            $bulk_Delete_By_Days = new Bulk_Delete_By_Days;
        }

        // Pages
        if ("pages" == $delete_options['pages']) {
            $options['post_type'] = 'page';
            $pages = $wp_query->query($options);

            foreach ($pages as $page) {
                wp_delete_post($page->ID, $force_delete);
            }
        }
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

/**
 * function to filter posts by days
 * @param <type> $where
 * @return <type>
 */
if (!function_exists('smbd_tags_by_days ')) {
    function smbd_tags_by_days ($where = '') {
        $tags_op = array_get($_POST, 'smbd_tags_op');
        $tags_days = array_get($_POST, 'smbd_tags_days');
        
        remove_filter('posts_where', 'smbd_tags_by_days');

        $where .= " AND post_date $tags_op '" . date('y-m-d', strtotime("-$tags_days days")) . "'";
        return $where;
    }
}

/**
 * function to filter custom taxonomy posts by days
 * @param <type> $where
 * @return <type>
 */
if (!function_exists('smbd_taxs_by_days ')) {
    function smbd_taxs_by_days ($where = '') {
        $taxs_op = array_get($_POST, 'smbd_taxs_op');
        $taxs_days = array_get($_POST, 'smbd_taxs_days');

        remove_filter('posts_where', 'smbd_taxs_by_days');

        $where .= " AND post_date $taxs_op '" . date('y-m-d', strtotime("-$taxs_days days")) . "'";
        return $where;
    }
}

/**
 * Return the posts for a taxonomy
 *
 * @param <type> $tax
 * @return <type>
 */
if (!function_exists('smbd_get_tax_post')) {
    function smbd_get_tax_post($tax) {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT object_id FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id IN (SELECT term_taxonomy_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = '%s')", $tax);
        $post_ids_result = $wpdb->get_results($query);

        $postids = array();
        foreach ($post_ids_result as $post_id_result) {
            $postids[] = $post_id_result->object_id;
        }

        return $postids;
    }
}
?>
