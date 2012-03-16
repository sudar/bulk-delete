<?php
/*
Plugin Name: Bulk Delete
Plugin Script: bulk-delete.php
Plugin URI: http://sudarmuthu.com/wordpress/bulk-delete
Description: Bulk delete posts from selected categories or tags. Use it with caution.
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
Version: 1.9
License: GPL
Author: Sudar
Author URI: http://sudarmuthu.com/
Text Domain: bulk-delete

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
 * Request Handler
 */

if (!function_exists('smbd_request_handler')) {
    function smbd_request_handler() {
        global $wpdb;

        // Load localization domain
        load_plugin_textdomain( 'bulk-delete', false, dirname(plugin_basename(__FILE__)) . '/languages' );

        if (isset($_POST['smbd_action'])) {

            $wp_query = new WP_Query;
            check_admin_referer( 'bulk-delete-posts');

            switch($_POST['smbd_action']) {

                case "bulk-delete-cats":
                    // delete by cats
                    $selected_cats = $_POST['smbd_cats'];
                    if ($_POST['smbd_cats_restrict'] == "true") {

                        add_option('cats_op', $_POST['smbd_cats_op']);
                        add_option('cats_days', $_POST['smbd_cats_days']);
                        
                        add_filter ('posts_where', 'cats_by_days');
                    }

                    $private = $_POST['smbd_cats_private'];

                    if ($private == 'true') {
                        $options = array('category__in'=>$selected_cats,'post_status'=>'private', 'post_type'=>'post');
                    } else {
                        $options = array('category__in'=>$selected_cats,'post_status'=>'publish', 'post_type'=>'post');
                    }

                    $limit_to = absint($_POST['smbd_cats_limits_to']);

                    if ($limit_to > 0) {
                        $options['showposts'] = $limit_to;
                    } else {
                        $options['nopaging'] = 'true';
                    }

                    $force_delete = $_POST['smbd_cats_force_delete'];

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

                case "bulk-delete-tags":
                    // delete by tags
                    $selected_tags = $_POST['smbd_tags'];
                    if ($_POST['smbd_tags_restrict'] == "true") {

                        add_option('tags_op', $_POST['smbd_tags_op']);
                        add_option('tags_days', $_POST['smbd_tags_days']);

                        add_filter ('posts_where', 'tags_by_days');
                    }

                    $private = $_POST['smbd_tags_private'];

                    if ($private == 'true') {
                        $options = array('tag__in'=>$selected_tags,'post_status'=>'private', 'post_type'=>'post');
                    } else {
                        $options = array('tag__in'=>$selected_tags,'post_status'=>'publish', 'post_type'=>'post');
                    }

                    $limit_to = absint($_POST['smbd_tags_limits_to']);

                    if ($limit_to > 0) {
                        $options['showposts'] = $limit_to;
                    } else {
                        $options['nopaging'] = 'true';
                    }

                    $force_delete = $_POST['smbd_tags_force_delete'];

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
                    $selected_taxs = $_POST['smbd_taxs'];

                    foreach ($selected_taxs as $selected_tax) {
                        $postids = get_tax_post($selected_tax);
                        
                        if ($_POST['smbd_taxs_restrict'] == "true") {

                            add_option('taxs_op', $_POST['smbd_taxs_op']);
                            add_option('taxs_days', $_POST['smbd_taxs_days']);

                            add_filter ('posts_where', 'taxs_by_days');
                        }

                        $private = $_POST['smbd_taxs_private'];

                        if ($private == 'true') {
                            $options = array('post__in'=>$postids,'post_status'=>'private', 'post_type'=>'post');
                        } else {
                            $options = array('post__in'=>$postids,'post_status'=>'publish', 'post_type'=>'post');
                        }

                        $limit_to = absint($_POST['smbd_taxs_limits_to']);

                        if ($limit_to > 0) {
                            $options['showposts'] = $limit_to;
                        } else {
                            $options['nopaging'] = 'true';
                        }

                        $force_delete = $_POST['smbd_taxs_force_delete'];

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
                    $options = array();

                    $limit_to = absint($_POST['smbd_special_limit_to']);

                    if ($limit_to > 0) {
                        $options['showposts'] = $limit_to;
                    } else {
                        $options['nopaging'] = 'true';
                    }

                    $force_delete = $_POST['smbd_special_force_delete'];
                    if ($force_delete == 'true') {
                        $force_delete = true;
                    } else {
                        $force_delete = false;
                    }

                    // Drafts
                    if ("drafs" == $_POST['smbd_drafs']) {
                        $options['post_status'] = 'draft';
                        $drafts = $wp_query->query($options);

                        foreach ($drafts as $draft) {
                            wp_delete_post($draft->ID, $force_delete);
                        }
                    }

                    // Revisions
                    if ("revisions" == $_POST['smbd_revisions']) {
                        $revisions = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_type = 'revision'"));

                        foreach ($revisions as $revision) {
                            wp_delete_post($revision->ID, $force_delete);
                        }
                    }

                    // Pending Posts
                    if ("pending" == $_POST['smbd_pending']) {
                        $pendings = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_status = 'pending'"));

                        foreach ($pendings as $pending) {
                            wp_delete_post($pending->ID, $force_delete);
                        }
                    }

                    // Future Posts
                    if ("future" == $_POST['smbd_future']) {
                        $futures = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_status = 'future'"));

                        foreach ($futures as $future) {
                            wp_delete_post($future->ID, $force_delete);
                        }
                    }

                    // Private Posts
                    if ("private" == $_POST['smbd_private']) {
                        $privates = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_status = 'private'"));

                        foreach ($privates as $private) {
                            wp_delete_post($private->ID, $force_delete);
                        }
                    }

                    // Pages
                    if ("pages" == $_POST['smbd_pages']) {
                        $options['post_type'] = 'page';
                        $pages = $wp_query->query($options);

                        foreach ($pages as $page) {
                            wp_delete_post($page->ID, $force_delete);
                        }
                    }
                    
                    // Specific Pages
                    if ("specificpages" == $_POST['smdb_specific_pages']) {
                        $urls = preg_split( '/\r\n|\r|\n/', $_POST['smdb_specific_pages_urls'] );
                        foreach ($urls as $url) {
                            $checkedurl = $url;
                            if (substr($checkedurl ,0,1) == '/') {
                                $checkedurl = get_site_url() . $checkedurl ;
                            }
                            $postid = url_to_postid( $checkedurl );
                            wp_delete_post($postid, $force_delete);
                        }
                    }
                    
                    break;
            }

            // hook the admin notices action
            add_action( 'admin_notices', 'smbd_deleted_notice', 9 );
        }
    }
}

/**
 * Show deleted notice messages
 */
function smbd_deleted_notice() {
    echo "<div class = 'updated'><p>" . __("All the selected posts have been sucessfully deleted.", 'bulk-delete') . "</p></div>";
}

/**
 * Show the Admin page
 */
if (!function_exists('smbd_displayOptions')) {
    function smbd_displayOptions() {
        global $wpdb;
?>
	<div class="updated fade" style="background:#ff0;text-align:center;color: red;"><p><strong><?php _e("WARNING: Posts deleted once cannot be retrieved back. Use with caution.", 'bulk-delete'); ?></strong></p></div>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Bulk Delete</h2>

            <h3><?php _e("Select the posts which you want to delete", 'bulk-delete'); ?></h3>

        <form name="smbd_form" id = "smbd_misc_form"
        action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post"
        onsubmit="return bd_validateForm(this);">

<?php
        $wp_query = new WP_Query;
        $drafts = $wpdb->get_var($wpdb->prepare("select count(*) from $wpdb->posts where post_status = 'draft'"));
        $revisions = $wpdb->get_var($wpdb->prepare("select count(*) from $wpdb->posts where post_type = 'revision'"));
        $pending = $wpdb->get_var($wpdb->prepare("select count(*) from $wpdb->posts where post_status = 'pending'"));
        $future = $wpdb->get_var($wpdb->prepare("select count(*) from $wpdb->posts where post_status = 'future'"));
        $private = $wpdb->get_var($wpdb->prepare("select count(*) from $wpdb->posts where post_status = 'private'"));
        $pages = $wpdb->get_var($wpdb->prepare("select count(*) from $wpdb->posts where post_type = 'page'"));
?>
        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td scope="row" >
                    <input name="smbd_drafs" id ="smbd_drafs" value = "drafs" type = "checkbox" />
                    <label for="smbd_drafs"><?php echo _e("All Drafts", 'bulk-delete'); ?> (<?php echo $drafts . " "; _e("Drafts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_revisions" id ="smbd_revisions" value = "revisions" type = "checkbox" />
                    <label for="smbd_revisions"><?php echo _e("All Revisions", 'bulk-delete'); ?> (<?php echo $revisions . " "; _e("Revisions", 'bulk-delete'); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_pending" id ="smbd_pending" value = "pending" type = "checkbox" />
                    <label for="smbd_pending"><?php echo _e("All Pending posts", 'bulk-delete'); ?> (<?php echo $pending . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_future" id ="smbd_future" value = "future" type = "checkbox" />
                    <label for="smbd_future"><?php echo _e("All scheduled posts", 'bulk-delete'); ?> (<?php echo $future . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_private" id ="smbd_private" value = "private" type = "checkbox" />
                    <label for="smbd_private"><?php echo _e("All private posts", 'bulk-delete'); ?> (<?php echo $private . " "; _e("Posts", 'bulk-delete'); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_pages" value = "pages" type = "checkbox" />
                    <label for="smbd_pages"><?php echo _e("All Pages", 'bulk-delete'); ?> (<?php echo $pages . " "; _e("Pages", 'bulk-delete'); ?>)</label>
                </td>
            </tr>

            <tr>
                <td scope="row"> 
                    <input name="smdb_specific_pages" id="smdb_specific_pages" value = "specificpages" type = "checkbox"  />                    
                    <label for="smdb_specific_pages"><?php echo _e("Delete these specific pages", 'bulk-delete'); ?></label>
                    <br/>
                    <textarea style="width: 450px; height: 80px;" id="smdb_specific_pages_urls" name="smdb_specific_pages_urls" rows="5" columns="80" ></textarea>
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
                <input type="submit" name="submit" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
        </p>

        <?php wp_nonce_field('bulk-delete-posts'); ?>

        <input type="hidden" name="smbd_action" value="bulk-delete-special" />
        </form>

        <h3><?php _e("By Category", 'bulk-delete'); ?></h3>
        <h4><?php _e("Select the categories whose post you want to delete", 'bulk-delete'); ?></h4>

        <form name="smbd_form" id = "smbd_cat_form"
        action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post"
        onsubmit="return bd_validateForm(this);">

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
                <td colspan="2"></td>
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

        </table>
        </fieldset>
        <p class="submit">
				<input type="submit" name="submit" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
        </p>

<?php wp_nonce_field('bulk-delete-posts'); ?>

		<input type="hidden" name="smbd_action" value="bulk-delete-cats" />
		</form>
<?php
        $tags =  get_tags();
        if (count($tags) > 0) {
?>
            <h3><?php _e("By Tags", 'bulk-delete'); ?></h3>
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
                    <td colspan="2"></td>
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
                    <input type="submit" name="submit" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
            </p>

    <?php wp_nonce_field('bulk-delete-posts'); ?>

            <input type="hidden" name="smbd_action" value="bulk-delete-tags" />
            </form>
<?php
        }
?>

<?php
        $customTaxs =  get_taxonomies();
        if (count($customTaxs) > 0) {
?>
            <h3><?php _e("By Taxonomies", 'bulk-delete'); ?></h3>
            <h4><?php _e("Select the taxonomies whose post you want to delete", 'bulk-delete') ?></h4>

            <form name="smbd_form" id = "smbd_tax_form"
            action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post"
            onsubmit="return bd_validateForm(this);">

            <fieldset class="options">
            <table class="optiontable">
    <?php
            foreach ($customTaxs as $taxs) {

                $posts = get_tax_post($taxs);
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
                    <td colspan="2"></td>
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
                    <input type="submit" name="submit" value="<?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;">
            </p>

    <?php wp_nonce_field('bulk-delete-posts'); ?>

            <input type="hidden" name="smbd_action" value="bulk-delete-taxs" />
            </form>
<?php
        }
?>
            <h3><?php _e('Support', 'bulk-delete'); ?></h3>
            <p><?php _e('If you have any questions/comments/feedback about the Plugin then post a comment in the <a target="_blank" href = "http://sudarmuthu.com/wordpress/bulk-delete">Plugins homepage</a>.','bulk-delete'); ?></p>
            <p><?php _e('If you like the Plugin, then consider doing one of the following.', 'bulk-delete'); ?></p>
            <ul style="list-style:disc inside">
                <li><?php _e('Write a blog post about the Plugin.', 'bulk-delete'); ?></li>
                <li><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://sudarmuthu.com/wordpress/bulk-delete" data-text="Bulk Delete WordPress Plugin" data-count="none" data-via="sudarmuthu">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script><?php _e(' about it.', 'bulk-delete'); ?></li>
                <li><?php _e('Give a <a href = "http://wordpress.org/extend/plugins/bulk-delete/" target="_blank">good rating</a>.', 'bulk-delete'); ?></li>
                <li><?php _e('Say <a href = "http://sudarmuthu.com/if-you-wanna-thank-me" target="_blank">thank you</a>.', 'bulk-delete'); ?></li>
            </ul>

        <p><em><?php _e("If you are looking to move posts in bulk, instead of deleting then try out my ", 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/wordpress/bulk-move"><?php _e("Bulk Move Plugin", 'bulk-delete');?></a>.</em></p>
    </div>
<?php

    // Display credits in Footer
    add_action( 'in_admin_footer', 'smbd_admin_footer' );
    }
}

/**
 * Print JavaScript
 */
function smbd_print_scripts() {
?>
<script type="text/javascript">

    /**
     * Check All Checkboxes
     */
    function bd_checkAll(form) {
        for (i = 0, n = form.elements.length; i < n; i++) {
            if(form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick',2))) {
                if(form.elements[i].checked == true)
                    form.elements[i].checked = false;
                else
                    form.elements[i].checked = true;
            }
        }
    }

    function toggle_date_restrict(el) {
        if (jQuery("#smbd_" + el + "_restrict").is(":checked")) {
            jQuery("#smbd_" + el + "_op").removeAttr('disabled');
            jQuery("#smbd_" + el + "_days").removeAttr('disabled');
        } else {
            jQuery("#smbd_" + el + "_op").attr('disabled', 'true');
            jQuery("#smbd_" + el + "_days").attr('disabled', 'true');
        }
    }
    
    function toggle_limit_restrict(el) {
        if (jQuery("#smbd_" + el + "_limit").is(":checked")) {
            jQuery("#smbd_" + el + "_limit_to").removeAttr('disabled');
        } else {
            jQuery("#smbd_" + el + "_limit_to").attr('disabled', 'true');
        }
    }
    /**
     * Validate Form
     */
    function bd_validateForm(form) {
        var valid = false;
        for (i = 0, n = form.elements.length; i < n; i++) {
            if(form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick',2))) {
                if(form.elements[i].checked == true) {
                    valid = true;
                    break;
                }
            }
        }

        if (valid) {
            return confirm("<?php _e('Are you sure you want to delete all the selected posts', 'bulk-delete'); ?>");
        } else {
            alert ("<?php _e('Please select at least one', 'bulk-delete'); ?>");
            return false;
        }
    }
</script>
<?php
}

/**
 * function to filter posts by days
 * @param <type> $where
 * @return <type>
 */
function cats_by_days ($where = '') {
    $cats_op = get_option('cats_op');
    $cats_days = get_option('cats_days');
    remove_filter('posts_where', 'cats_by_days');

    $where .= " AND post_date $cats_op '" . date('y-m-d', strtotime("-$cats_days days")) . "'";
    return $where;
}

/**
 * function to filter posts by days
 * @param <type> $where
 * @return <type>
 */
function tags_by_days ($where = '') {
    $tags_op = get_option('tags_op');
    $tags_days = get_option('tags_days');
    
    remove_filter('posts_where', 'tags_by_days');
    $where .= " AND post_date $tags_op '" . date('y-m-d', strtotime("-$tags_days days")) . "'";
    return $where;
}

/**
 * function to filter custom taxonomy posts by days
 * @param <type> $where
 * @return <type>
 */
function taxs_by_days ($where = '') {
    $taxs_op = get_option('taxs_op');
    $taxs_days = get_option('taxs_days');

    remove_filter('posts_where', 'taxs_by_days');
    $where .= " AND post_date $taxs_op '" . date('y-m-d', strtotime("-$taxs_days days")) . "'";
    return $where;
}

/**
 * Return the posts for a taxonomy
 *
 * @param <type> $tax
 * @return <type>
 */
function get_tax_post($tax) {
    global $wpdb;
    
    $query = $wpdb->prepare("select object_id from {$wpdb->prefix}term_relationships where term_taxonomy_id in (select term_taxonomy_id from {$wpdb->prefix}term_taxonomy where taxonomy = '$tax')");
    $post_ids_result = $wpdb->get_results($query);

    $postids = array();
    foreach ($post_ids_result as $post_id_result) {
        $postids[] = $post_id_result->object_id;
    }

    return $postids;
}
/**
 * Add navigation menu
 */
if(!function_exists('smbd_add_menu')) {
	function smbd_add_menu() {
	    //Add a submenu to Manage
        $page = add_options_page("Bulk Delete", "Bulk Delete", 'manage_options', basename(__FILE__), "smbd_displayOptions");
        add_action('admin_print_scripts-' . $page, 'smbd_print_scripts');
	}
}

/**
 * Adds the settings link in the Plugin page. Based on http://striderweb.com/nerdaphernalia/2008/06/wp-use-action-links/
 * @staticvar <type> $this_plugin
 * @param <type> $links
 * @param <type> $file
 */
function smbd_filter_plugin_actions($links, $file) {
    static $this_plugin;
    if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

    if( $file == $this_plugin ) {
        $settings_link = '<a href="options-general.php?page=bulk-delete.php">' . _('Manage') . '</a>';
        array_unshift( $links, $settings_link ); // before other links
    }
    return $links;
}

/**
 * Adds Footer links. Based on http://striderweb.com/nerdaphernalia/2008/06/give-your-wordpress-plugin-credit/
 */
function smbd_admin_footer() {
    $plugin_data = get_plugin_data( __FILE__ );
    printf('%1$s ' . __("plugin", 'bulk-delete') .' | ' . __("Version", 'bulk-delete') . ' %2$s | '. __('by', 'bulk-delete') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
}

add_filter( 'plugin_action_links', 'smbd_filter_plugin_actions', 10, 2 );
add_action('admin_menu', 'smbd_add_menu');
add_action('init', 'smbd_request_handler');
?>
