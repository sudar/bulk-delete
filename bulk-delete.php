<?php
/*
Plugin Name: Bulk Delete
Plugin Script: bulk-delete.php
Plugin URI: http://sudarmuthu.com/wordpress/bulk-delete
Description: Bulk delete posts from selected categories or tags. Use it with caution.
Version: 0.6
License: GPL
Author: Sudar
Author URI: http://sudarmuthu.com/

=== RELEASE NOTES ===
2009-02-02 - v0.1 - first version
2009-02-03 - v0.2 - Second release - Fixed issues with pagging
2009-04-05 - v0.3 - Third release - Prevented drafts from deleted when only posts are selected
2009-07-05 - v0.4 - Fourth release - Added option to delete by date.
2009-07-21 - v0.5 - Fifth release - Added option to delete all pending posts.
2009-07-22 - v0.6 - Sixth release - Added option to delete all scheduled posts.

*/


/**
 * Request Handler
 */

if (!function_exists('smbd_request_handler')) {
    function smbd_request_handler() {
        global $wpdb;

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
                    $posts = $wp_query->query(array('category__in'=>$selected_cats,'post_status'=>'publish', 'post_type'=>'post', 'nopaging'=>'true'));
                    foreach ($posts as $post) {
                        wp_delete_post($post->ID);
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
                    $posts = $wp_query->query(array('tag__in'=>$selected_tags, 'post_status'=>'publish', 'post_type'=>'post', 'nopaging'=>'true'));

                    foreach ($posts as $post) {
                        wp_delete_post($post->ID);
                    }
                    
                    break;

                case "bulk-delete-special":
                    // Drafts
                    if ("drafs" == $_POST['smbd_drafs']) {
                        $drafts = $wp_query->query(array('post_status'=>'draft', 'nopaging'=>'true'));

                        foreach ($drafts as $draft) {
                            wp_delete_post($draft->ID);
                        }
                    }

                    // Revisions
                    if ("revisions" == $_POST['smbd_revisions']) {
                        $revisions = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where post_type = 'revision'"));

                        foreach ($revisions as $revision) {
                            wp_delete_post($revision->ID);
                        }
                    }

                    // Pending Posts
                    if ("pending" == $_POST['smbd_pending']) {
                        $pendings = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where post_status = 'pending'"));

                        foreach ($pendings as $pending) {
                            wp_delete_post($pending->ID);
                        }
                    }

                    // Future Posts
                    if ("future" == $_POST['smbd_future']) {
                        $futures = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where post_status = 'future'"));

                        foreach ($futures as $future) {
                            wp_delete_post($future->ID);
                        }
                    }

                    // Pages
                    if ("pages" == $_POST['smbd_pages']) {
                        $pages = $wp_query->query(array('post_type'=>'page', 'nopaging'=>'true'));

                        foreach ($pages as $page) {
                            wp_delete_post($page->ID);
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
    echo "<div class = 'updated'><p>" . __("All the selected posts have been sucessfully deleted.") ."</p></div>";
}

/**
 * Show the Admin page
 */
if (!function_exists('smbd_displayOptions')) {
    function smbd_displayOptions() {
        global $wpdb;
?>
	<div class="updated fade" style="background:#ff0;text-align:center;color: red;"><p><strong><?php _e("WARNING: Posts deleted once cannot be retrieved back. Use with caution."); ?></strong></p></div>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Bulk Delete</h2>

            <h3><?php _e("Select the posts which you want to delete"); ?></h3>

        <form name="smbd_form" id = "smbd_misc_form"
        action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=bulk-delete.php" method="post"
        onsubmit="return bd_validateForm(this);">

<?php
        $wp_query = new WP_Query;
        $drafts = $wp_query->query(array('post_status'=>'draft'));
        $revisions = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where post_type = 'revision'"));
        $pending = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where post_status = 'pending'"));
        $future = $wpdb->get_results($wpdb->prepare("select * from $wpdb->posts where post_status = 'future'"));
        $pages = $wp_query->query(array('post_type'=>'page'));
?>
        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td scope="row" >
                    <input name="smbd_drafs" id ="smbd_drafs" value = "drafs" type = "checkbox" />
                    <label for="smbd_drafs"><?php echo _e("All Drafts"); ?> (<?php echo count($drafts) . " "; _e("Drafts"); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_revisions" id ="smbd_revisions" value = "revisions" type = "checkbox" />
                    <label for="smbd_revisions"><?php echo _e("All Revisions"); ?> (<?php echo count($revisions) . " "; _e("Revisons"); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_pending" id ="smbd_pending" value = "pending" type = "checkbox" />
                    <label for="smbd_pending"><?php echo _e("All Pending posts"); ?> (<?php echo count($pending) . " "; _e("Posts"); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_future" id ="smbd_future" value = "future" type = "checkbox" />
                    <label for="smbd_future"><?php echo _e("All scheduled posts"); ?> (<?php echo count($future) . " "; _e("Posts"); ?>)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="smbd_pages" value = "pages" type = "checkbox" />
                    <label for="smbd_pages"><?php echo _e("All Pages"); ?> (<?php echo count($pages) . " "; _e("Pages"); ?>)</label>
                </td>
            </tr>

        </table>
        </fieldset>
        <p class="submit">
                <input type="submit" name="submit" value="<?php _e("Bulk Delete ") ?>&raquo;">
        </p>

        <?php wp_nonce_field('bulk-delete-posts'); ?>

        <input type="hidden" name="smbd_action" value="bulk-delete-special" />
        </form>

        <h3><?php _e("By Category"); ?></h3>
        <h4><?php _e("Select the categories whose post you want to delete") ?></h4>

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
                    <label for="smbd_cats"><?php echo $category->cat_name; ?> (<?php echo $category->count . " "; _e("Posts"); ?>)</label>
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
                    <label for="smbd_cats_all"><?php _e("All Categories") ?></label>
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
                    <?php _e("Only restrict to posts which are ");?>
                    <select name="smbd_cats_op" id="smbd_cats_op" disabled>
                        <option value ="<"><?php _e("older than");?></option>
                        <option value =">"><?php _e("posted within last");?></option>
                    </select>
                    <input type ="textbox" name="smbd_cats_days" id="smbd_cats_days" disabled value ="0" maxlength="4" size="4" /><?php _e("days");?>
                </td>
            </tr>

        </table>
        </fieldset>
        <p class="submit">
				<input type="submit" name="submit" value="<?php _e("Bulk Delete ") ?>&raquo;">
        </p>

<?php wp_nonce_field('bulk-delete-posts'); ?>

		<input type="hidden" name="smbd_action" value="bulk-delete-cats" />
		</form>
<?php
        $tags =  get_tags();
        if (count($tags) > 0) {
?>
            <h3><?php _e("By Tags"); ?></h3>
            <h4><?php _e("Select the tags whose post you want to delete") ?></h4>

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
                        <label for="smbd_tags"><?php echo $tag->name; ?> (<?php echo $tag->count . " "; _e("Posts"); ?>)</label>
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
                        <label for="smbd_tags_all"><?php _e("All Tags") ?></label>
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
                        <?php _e("Only restrict to posts which are ");?>
                        <select name="smbd_tags_op" id="smbd_tags_op" disabled>
                            <option value ="<"><?php _e("older than");?></option>
                            <option value =">"><?php _e("posted within last");?></option>
                        </select>
                        <input type ="textbox" name="smbd_tags_days" id ="smbd_tags_days" value ="0"  maxlength="4" size="4" disabled /><?php _e("days");?>
                    </td>
                </tr>

            </table>
            </fieldset>
            <p class="submit">
                    <input type="submit" name="submit" value="<?php _e("Bulk Delete ") ?>&raquo;">
            </p>

    <?php wp_nonce_field('bulk-delete-posts'); ?>

            <input type="hidden" name="smbd_action" value="bulk-delete-tags" />
            </form>
<?php
        }
?>
        <p><em><?php _e("If you are looking to move posts in bulk, instead of deleting then try out my "); ?> <a href = "http://sudarmuthu.com/wordpress/bulk-move"><?php _e("Bulk Move Plugin");?></a>.</em></p>
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
            return confirm("<?php _e('Are you sure you want to delete all the selected posts'); ?>");
        } else {
            alert ("<?php _e('Please select atleast one'); ?>");
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
 * Add navigation menu
 */
if(!function_exists('smbd_add_menu')) {
	function smbd_add_menu() {
	    //Add a submenu to Manage
        add_options_page("Bulk Delete", "Bulk Delete", 8, basename(__FILE__), "smbd_displayOptions");
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
    printf('%1$s ' . __("plugin") .' | ' . __("Version") . ' %2$s | '. __('by') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
}

add_filter( 'plugin_action_links', 'smbd_filter_plugin_actions', 10, 2 );

add_action('admin_menu', 'smbd_add_menu');
add_action('init', 'smbd_request_handler');
add_action('admin_head', 'smbd_print_scripts');
?>