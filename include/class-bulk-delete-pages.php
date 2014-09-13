<?php
/**
 * Utility class for deleting pages
 *
 * @package Bulk_Delete
 * @package Page
 * @author  Sudar
 * @since   5.0
 */
class Bulk_Delete_Pages {

    /**
     * Render delete pages by page status box
     *
     * @access public
     * @static
     * @since  5.0
     */
    public static function render_delete_pages_by_status_box() {

        if ( Bulk_Delete_Util::is_pages_box_hidden( Bulk_Delete::BOX_PAGE_STATUS ) ) {
            printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::PAGES_PAGE_SLUG );
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
        <h4><?php _e( 'Select the pages which you want to delete', 'bulk-delete' ); ?></h4>

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
                    <input type ="textbox" name="smbd_pages_limit_to" id="smbd_pages_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e("pages.", 'bulk-delete');?>
                    <?php _e("Use this option if there are more than 1000 pages and the script timesout.", 'bulk-delete') ?>
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
                    <span class = "bd-pages-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://bulkwp.com/addons/scheduler-for-deleting-pages-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp">Buy now</a></span>
                </td>
            </tr>
        </table>
        </fieldset>

        <p>
            <button type="submit" name="bd_action" value = "delete_pages_by_status" class="button-primary"><?php _e( 'Bulk Delete ', 'bulk-delete' ) ?>&raquo;</button>
        </p>
        <!-- Pages end-->
<?php
    }

    /**
     * Request handler for deleting pages by status
     *
     * @since 5.0
     */
    public static function do_delete_pages_by_status() {
        $delete_options = array();
        $delete_options['restrict']     = array_get( $_POST, 'smbd_pages_restrict', FALSE );
        $delete_options['limit_to']     = absint( array_get( $_POST, 'smbd_pages_limit_to', 0 ) );
        $delete_options['force_delete'] = array_get( $_POST, 'smbd_pages_force_delete', 'false' );

        $delete_options['page_op']      = array_get( $_POST, 'smbd_pages_op' );
        $delete_options['page_days']    = array_get( $_POST, 'smbd_pages_days' );

        $delete_options['publish']      = array_get( $_POST, 'smbd_published_pages' );
        $delete_options['drafts']       = array_get( $_POST, 'smbd_draft_pages' );
        $delete_options['pending']      = array_get( $_POST, 'smbd_pending_pages' );
        $delete_options['future']       = array_get( $_POST, 'smbd_future_pages' );
        $delete_options['private']      = array_get( $_POST, 'smbd_private_pages' );

        if ( array_get( $_POST, 'smbd_pages_cron', 'false' ) == 'true' ) {
            $freq = $_POST['smbd_pages_cron_freq'];
            $time = strtotime( $_POST['smbd_pages_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

            if ( $freq == -1 ) {
                wp_schedule_single_event( $time, Bulk_Delete::CRON_HOOK_PAGES_STATUS, array( $delete_options ) );
            } else {
                wp_schedule_event( $time, $freq , Bulk_Delete::CRON_HOOK_PAGES_STATUS, array( $delete_options ) );
            }
            $msg = __( 'The selected pages are scheduled for deletion.', 'bulk-delete' ) . ' ' .
                sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/admin.php?page=' . Bulk_Delete::CRON_PAGE_SLUG );
        } else {
            $deleted_count = self::delete_pages_by_status( $delete_options );
            $msg = sprintf( _n( 'Deleted %d page', 'Deleted %d pages' , $deleted_count, 'bulk-delete' ), $deleted_count );
        }

        add_settings_error(
            Bulk_Delete::PAGES_PAGE_SLUG,
            'deleted-cron',
            $msg,
            'updated'
        );
    }

    /**
     * Bulk Delete pages
     *
     * @since 5.0
     */
    public static function delete_pages_by_status( $delete_options ) {
        global $wp_query;

        $options     = array();
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

        // Pending pages
        if ("pending_pages" == $delete_options['pending']) {
            $post_status[] = 'pending';
        }

        // Future pages
        if ("future_pages" == $delete_options['future']) {
            $post_status[] = 'future';
        }

        // Private pages
        if ("private_pages" == $delete_options['private']) {
            $post_status[] = 'private';
        }

        $options['post_type'] = 'page';
        $options['post_status'] = $post_status;

        if ($delete_options['restrict'] == "true") {
            $options['op'] = $delete_options['page_op'];
            $options['days'] = $delete_options['page_days'];

            if ( !class_exists( 'Bulk_Delete_By_Days' ) ) {
                require_once Bulk_Delete::$PLUGIN_DIR . '/include/class-bulk-delete-by-days.php';
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
     * Render delete pages from trash box
     *
     * @since 5.1
     * @static
     */
    public static function render_delete_pages_from_trash() {
        if ( Bulk_Delete_Util::is_pages_box_hidden( Bulk_Delete::BOX_PAGE_FROM_TRASH ) ) {
            printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::PAGES_PAGE_SLUG );
            return;
        }

        if ( !class_exists( 'Bulk_Delete_From_Trash' ) ) {
?>
        <!-- pages In Trash box start-->
        <p>
            <span class = "bd-pages-trash-pro" style = "color:red">
                <?php _e( 'You need "Bulk Delete From Trash" Addon, to delete pages in Trash.', 'bulk-delete' ); ?>
                <a href = "http://bulkwp.com/addons/bulk-delete-from-trash/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-th">Buy now</a>
            </span>
        </p>
        <!-- pages In Trash box end-->
<?php
        } else {
            /**
             * Render delete pages from trash box
             *
             * @since 5.4
             */
            do_action( 'bd_render_delete_pages_from_trash' );
        }
    }

    /**
     * Filter JS Array and add validation hooks
     *
     * @since 5.4
     * @static
     * @param  array $js_array JavaScript Array
     * @return array           Modified JavaScript Array
     */
    public static function filter_js_array( $js_array ) {
        $js_array['dt_iterators'][] = '_pages';
        return $js_array;
    }
}

add_action( 'bd_delete_pages_by_status', array( 'Bulk_Delete_Pages', 'do_delete_pages_by_status' ) );
add_filter( 'bd_javascript_array', array( 'Bulk_Delete_Pages' , 'filter_js_array' ) );
?>
