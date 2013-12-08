<?php
/**
 * Utility class for deleting posts
 *
 * @package Bulk Delete
 * @author Sudar
 */
class Bulk_Delete_Posts {

    /**
     * Render post status box
     */
    public static function render_by_post_status_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_POST_STATUS) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
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
    public static function render_by_category_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_CATEGORY) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
            return;
        }

        $types =  get_post_types( array(
            'public'   => true,
            '_builtin' => false
            ), 'names'
        );

        array_unshift( $types, 'post' );
?>
        <!-- Category Start-->
        <h4><?php _e( 'Select the post type whose category posts you want to delete', 'bulk-delete' ); ?></h4>
        <fieldset class="options">
        <table class="optiontable">
<?php
        foreach ( $types as $type ) {
?>
            <tr>
                <td scope="row" >
                <input name="smbd_cat_post_type" value = "<?php echo $type; ?>" type = "radio"  class = "smbd_cat_post_type" <?php checked( $type, 'post' ); ?>>
                </td>
                <td>
                    <label for="smbd_cat_post_type"><?php echo $type; ?> </label>
                </td>
            </tr>
<?php
        }
?>
        </table>

        <h4><?php _e( 'Select the categories whose post you want to delete', 'bulk-delete' ); ?></h4>
        <p><?php _e( 'Note: The post count below for each category is the total number of posts in that category, irrespective of post type', 'bulk-delete' ); ?></p>
<?php
        $categories =  get_categories( array(
            'hide_empty' => false
            )
        );
?>
        <table class="optiontable">
<?php
        foreach ( $categories as $category ) {
?>
            <tr>
                <td scope="row" >
                    <input name="smbd_cats[]" value = "<?php echo $category->cat_ID; ?>" type = "checkbox" >
                </td>
                <td>
                    <label for="smbd_cats"><?php echo $category->cat_name; ?> (<?php echo $category->count . " "; _e( 'Posts', 'bulk-delete' ); ?>)</label>
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
        </table>

        <table class="optiontable">
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

    /**
     * Render by tag box
     */
    public static function render_by_tag_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_TAG) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
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
    public static function render_by_tax_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_TAX) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
            return;
        }

        $types =  get_post_types( array(
            'public'   => true,
            '_builtin' => false
            ), 'names'
        );

        array_unshift( $types, 'post' );

        $taxs =  get_taxonomies(array(
            'public'   => true,
            '_builtin' => false
            ), 'objects'
        );

        $terms_array = array();
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
        <h4><?php _e( 'Select the post type whose taxonomy posts you want to delete', 'bulk-delete' ); ?></h4>

        <fieldset class="options">
            <table class="optiontable">
<?php
            foreach ( $types as $type ) {
?>
            <tr>
                <td scope="row" >
                <input name="smbd_tax_post_type" value = "<?php echo $type; ?>" type = "radio"  class = "smbd_tax_post_type" <?php checked( $type, 'post' ); ?>>
                </td>
                <td>
                    <label for="smbd_tax_post_type"><?php echo $type; ?> </label>
                </td>
            </tr>
<?php
            }
?>
            </table>

            <h4><?php _e("Select the taxonomies whose post you want to delete", 'bulk-delete') ?></h4>

            <table class="optiontable">
<?php
            foreach ($terms_array as $tax => $terms) {
?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_taxs" value = "<?php echo $tax; ?>" type = "radio"  class = "custom-tax">
                    </td>
                    <td>
                        <label for="smbd_taxs"><?php echo $taxs[$tax]->labels->name; ?> </label>
                    </td>
                </tr>
<?php
            }
?>
            </table>

            <h4><?php _e("The selected taxonomy has the following terms. Select the terms whose post you want to delete", 'bulk-delete') ?></h4>
            <p><?php _e( 'Note: The post count below for each term is the total number of posts in that term, irrespective of post type', 'bulk-delete' ); ?></p>
<?php
            foreach ($terms_array as $tax => $terms) {
?>
            <table class="optiontable terms_<?php echo $tax;?> terms">
<?php
                foreach ($terms as $term) {
?>
                    <tr>
                        <td scope="row" >
                            <input name="smbd_tax_terms[]" value = "<?php echo $term->slug; ?>" type = "checkbox" class = "terms" >
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
     * Render delete by custom post type box
     */
    public static function render_by_post_type_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_POST_TYPE) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
            return;
        }

        $types_array = array();

        $types =  get_post_types( array(
            'public'   => true,
            '_builtin' => false
            ), 'names'
        );

        if ( count( $types ) > 0 ) {
            foreach ($types as $type) {
                $post_count = wp_count_posts( $type );
                if ( $post_count->publish > 0 ) {
                    $types_array["$type-publish"] = $post_count->publish;
                }
                if ( $post_count->future > 0 ) {
                    $types_array["$type-future"] = $post_count->future;
                }
                if ( $post_count->pending > 0 ) {
                    $types_array["$type-pending"] = $post_count->pending;
                }
                if ( $post_count->draft > 0 ) {
                    $types_array["$type-draft"] = $post_count->draft;
                }
                if ( $post_count->private > 0 ) {
                    $types_array["$type-private"] = $post_count->private;
                }
            }
        }

        if ( count( $types_array ) > 0 ) {
?>
            <!-- Custom post type Start-->
            <h4><?php _e( "Select the custom post type whose post you want to delete", 'bulk-delete' ) ?></h4>

            <fieldset class="options">
            <table class="optiontable">
<?php
            foreach ( $types_array as $type => $count ) {
?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_types[]" value = "<?php echo $type; ?>" type = "checkbox">
                    </td>
                    <td>
                    <label for="smbd_types"><?php echo Bulk_Delete_Util::display_post_type_status( $type ), ' (', $count, ')'; ?></label>
                    </td>
                </tr>
<?php
            }
?>
                <tr>
                    <td colspan="2">
                        <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_types_restrict" id ="smbd_types_restrict" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e("Only restrict to posts which are ", 'bulk-delete');?>
                        <select name="smbd_types_op" id="smbd_types_op" disabled>
                            <option value ="<"><?php _e("older than", 'bulk-delete');?></option>
                            <option value =">"><?php _e("posted within last", 'bulk-delete');?></option>
                        </select>
                        <input type ="textbox" name="smbd_types_days" id ="smbd_types_days" value ="0"  maxlength="4" size="4" disabled /><?php _e("days", 'bulk-delete');?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_types_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e('Move to Trash', 'bulk-delete'); ?>
                        <input name="smbd_types_force_delete" value = "true" type = "radio" /> <?php _e('Delete permanently', 'bulk-delete'); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_types_limit" id="smbd_types_limit" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e("Only delete first ", 'bulk-delete');?>
                        <input type ="textbox" name="smbd_types_limit_to" id="smbd_types_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("posts.", 'bulk-delete');?>
                        <?php _e("Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete') ?>
                    </td>
                </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_types_cron" value = "false" type = "radio" checked="checked" > <?php _e('Delete now', 'bulk-delete'); ?>
                    <input name="smbd_types_cron" value = "true" type = "radio" id = "smbd_types_cron" disabled > <?php _e('Schedule', 'bulk-delete'); ?>
                    <input name="smbd_types_cron_start" id = "smbd_types_cron_start" value = "now" type = "text" disabled><?php _e('repeat ', 'bulk-delete');?>
                    <select name = "smbd_types_cron_freq" id = "smbd_types_cron_freq" disabled>
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
                    <span class = "bd-types-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/buy-bulk-delete-post-type-addon">Buy now</a></span>
                </td>
            </tr>

            </table>
            </fieldset>
            <p class="submit">
                <button type="submit" name="smbd_action" value = "bulk-delete-post-types" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
            </p>
            <!-- Custom post type end-->
<?php
        } else {
?>
            <h4><?php _e("You don't have any posts assigned to custom post types in this blog.", 'bulk-delete') ?></h4>
<?php
        }
    }

    /**
     * Render delete by pages box
     */
    public static function render_by_page_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_PAGE) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
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
    public static function render_by_url_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_URL) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
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
    public static function render_by_post_revision_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden(Bulk_Delete::BOX_POST_REVISION) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
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
     * Render delete posts by custom field box
     */
    public static function render_by_custom_field_box() {

        if ( Bulk_Delete_Util::is_posts_box_hidden( Bulk_Delete::BOX_CUSTOM_FIELD ) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=bulk-delete.php' );
            return;
        }

        if ( !class_exists( 'Bulk_Delete_Custom_Field' ) ) {
?>
        <!-- Custom Field box start-->
        <p>
            <span class = "bd-post-status-pro" style = "color:red">
                <?php _e( 'You need "Bulk Delete by Custom Field" Addon, to delete post by custom field.', 'bulk-delete'); ?>
                <a href = "http://sudarmuthu.com/wordpress/bulk-delete/pro-addons#bulk-delete-by-custom-field">Buy now</a>
            </span>
        </p>
        <!-- Custom Field box end-->
<?php
        } else {
            Bulk_Delete_Custom_Field::render_by_custom_field_box();
        }
    }

    /**
     * Render debug box
     */
    public static function render_debug_box() {
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
                <td><?php echo Bulk_Delete::VERSION; ?></td>
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
}
?>
