<?php
/**
 * Utility class for deleting posts
 *
 * @author     Sudar
 * @package    BulkDelete
 */


class Bulk_Delete_Posts {

	/**
	 * Render post status box
	 */
	public static function render_delete_posts_by_status_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_POST_STATUS ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		$posts_count = wp_count_posts();
		$publish     = $posts_count->publish;
		$drafts      = $posts_count->draft;
		$future      = $posts_count->future;
		$pending     = $posts_count->pending;
		$private     = $posts_count->private;

		$sticky      = count( get_option( 'sticky_posts' ) );
?>
        <h4><?php _e( "Select the posts which you want to delete", 'bulk-delete' ); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td>
                    <input name="smbd_publish" id ="smbd_publish" value = "publish" type = "checkbox" >
                    <label for="smbd_publish"><?php _e( 'All publish posts', 'bulk-delete' ); ?> (<?php echo $publish . ' '; _e( 'Posts', 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

            <tr>
                <td scope="row" >
                    <input name="smbd_drafts" id ="smbd_drafts" value = "drafts" type = "checkbox" />
                    <label for="smbd_drafts"><?php _e( "All Draft Posts", 'bulk-delete' ); ?> (<?php echo $drafts . " "; _e( "Drafts", 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_pending" id ="smbd_pending" value = "pending" type = "checkbox" />
                    <label for="smbd_pending"><?php _e( "All Pending posts", 'bulk-delete' ); ?> (<?php echo $pending . " "; _e( "Posts", 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_future" id ="smbd_future" value = "future" type = "checkbox" />
                    <label for="smbd_future"><?php _e( "All scheduled posts", 'bulk-delete' ); ?> (<?php echo $future . " "; _e( "Posts", 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_private" id ="smbd_private" value = "private" type = "checkbox" />
                    <label for="smbd_private"><?php _e( "All private posts", 'bulk-delete' ); ?> (<?php echo $private . " "; _e( "Posts", 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_sticky" id ="smbd_sticky" value = "sticky" type = "checkbox" >
                    <label for="smbd_sticky"><?php _e( 'All Sticky posts', 'bulk-delete' ); ?> (<?php echo $sticky . ' '; _e( 'Posts', 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

            <tr>
                <td>
                    <h4><?php _e( "Choose your filtering options", 'bulk-delete' ); ?></h4>
                </td>
            </tr>

            <tr>
                <td>
                    <p><?php _e( 'Note: The date filter will not work for sticky posts', 'bulk-delete' ); ?></p>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_post_status_restrict" id="smbd_post_status_restrict" value = "true" type = "checkbox">
                    <?php _e( "Only restrict to posts which are ", 'bulk-delete' );?>
                    <select name="smbd_post_status_op" id="smbd_post_status_op" disabled>
                        <option value ="<"><?php _e( "older than", 'bulk-delete' );?></option>
                        <option value =">"><?php _e( "posted within last", 'bulk-delete' );?></option>
                    </select>
                    <input type ="textbox" name="smbd_post_status_days" id="smbd_post_status_days" disabled value ="0" maxlength="4" size="4" /><?php _e( "days", 'bulk-delete' );?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_post_status_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e( 'Move to Trash', 'bulk-delete' ); ?>
                    <input name="smbd_post_status_force_delete" value = "true" type = "radio" /> <?php _e( 'Delete permanently', 'bulk-delete' ); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_post_status_limit" id="smbd_post_status_limit" value = "true" type = "checkbox" >
                    <?php _e( "Only delete first ", 'bulk-delete' );?>
                    <input type ="textbox" name="smbd_post_status_limit_to" id="smbd_post_status_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e( "posts.", 'bulk-delete' );?>
                    <?php _e( "Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete' ) ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_post_status_cron" value = "false" type = "radio" checked="checked" /> <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbd_post_status_cron" value = "true" type = "radio" id = "smbd_post_status_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbd_post_status_cron_start" id = "smbd_post_status_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name = "smbd_post_status_cron_freq" id = "smbd_post_status_cron_freq" disabled>
                        <option value = "-1"><?php _e( "Don't repeat", 'bulk-delete' ); ?></option>
<?php
		$schedules = wp_get_schedules();
		foreach ( $schedules as $key => $value ) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php
		}
?>
                    </select>
                    <span class = "bd-post-status-pro" style = "color:red"><?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sps">Buy now</a></span>
                </td>
            </tr>
        </table>
        </fieldset>

        <p>
            <button type="submit" name="bd_action" value = "delete_posts_by_status" class="button-primary"><?php _e( 'Bulk Delete ', 'bulk-delete' ) ?>&raquo;</button>
        </p>
<?php
	}

	/**
	 * Delete posts by post status
	 *
	 * @since 5.0
	 * @static
	 */
	public static function do_delete_posts_by_status() {

		$delete_options = array();
		$delete_options['restrict']         = array_get( $_POST, 'smbd_post_status_restrict', false );
		$delete_options['limit_to']         = absint( array_get( $_POST, 'smbd_post_status_limit_to', 0 ) );
		$delete_options['force_delete']     = array_get( $_POST, 'smbd_post_status_force_delete', 'false' );

		$delete_options['post_status_op']   = array_get( $_POST, 'smbd_post_status_op' );
		$delete_options['post_status_days'] = array_get( $_POST, 'smbd_post_status_days' );

		$delete_options['publish']          = array_get( $_POST, 'smbd_publish' );
		$delete_options['drafts']           = array_get( $_POST, 'smbd_drafts' );
		$delete_options['pending']          = array_get( $_POST, 'smbd_pending' );
		$delete_options['future']           = array_get( $_POST, 'smbd_future' );
		$delete_options['private']          = array_get( $_POST, 'smbd_private' );
		$delete_options['sticky']           = array_get( $_POST, 'smbd_sticky' );

		if ( array_get( $_POST, 'smbd_post_status_cron', 'false' ) == 'true' ) {
			$freq = $_POST['smbd_post_status_cron_freq'];
			$time = strtotime( $_POST['smbd_post_status_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

			if ( $freq == -1 ) {
				wp_schedule_single_event( $time, Bulk_Delete::CRON_HOOK_POST_STATUS, array( $delete_options ) );
			} else {
				wp_schedule_event( $time, $freq, Bulk_Delete::CRON_HOOK_POST_STATUS, array( $delete_options ) );
			}
			$msg = __( 'Posts with the selected status are scheduled for deletion.', 'bulk-delete' ) . ' ' .
				sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/admin.php?page=' . Bulk_Delete::CRON_PAGE_SLUG );
		} else {
			$deleted_count = self::delete_posts_by_status( $delete_options );
			$msg = sprintf( _n( 'Deleted %d post with the selected post status', 'Deleted %d posts with the selected post status' , $deleted_count, 'bulk-delete' ), $deleted_count );
		}

		add_settings_error(
			Bulk_Delete::POSTS_PAGE_SLUG,
			'deleted-posts',
			$msg,
			'updated'
		);
	}

	/**
	 * Delete posts by post status - drafts, pending posts, scheduled posts etc.
	 *
	 * @since  5.0
	 * @static
	 *
	 * @param array   $delete_options Options for deleting posts
	 * @return int   $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_status( $delete_options ) {
		global $wpdb;

		$posts_deleted = 0;

		$force_delete = $delete_options['force_delete'];

		if ( $force_delete == 'true' ) {
			$force_delete = true;
		} else {
			$force_delete = false;
		}

		// Delete sticky posts
		if ( 'sticky' == $delete_options['sticky'] ) {
			$sticky_post_ids = get_option( 'sticky_posts' );

			foreach ( $sticky_post_ids as $sticky_post_id ) {
				wp_delete_post( $sticky_post_id, $force_delete );
			}

			$posts_deleted += count( $sticky_post_ids );
		}

		$options = array();
		$post_status = array();

		$limit_to = $delete_options['limit_to'];

		if ( $limit_to > 0 ) {
			$options['showposts'] = $limit_to;
		} else {
			$options['nopaging'] = 'true';
		}

		$force_delete = $delete_options['force_delete'];

		if ( $force_delete == 'true' ) {
			$force_delete = true;
		} else {
			$force_delete = false;
		}

		// Published posts
		if ( 'publish' == $delete_options['publish'] ) {
			$post_status[] = 'publish';
		}

		// Drafts
		if ( 'drafts' == $delete_options['drafts'] ) {
			$post_status[] = 'draft';

			// ignore sticky posts.
			// For some reason, sticky posts also gets deleted when deleting drafts through a schedule
			$options['post__not_in'] = get_option( 'sticky_posts' );
		}

		// Pending Posts
		if ( "pending" == $delete_options['pending'] ) {
			$post_status[] = 'pending';
		}

		// Future Posts
		if ( "future" == $delete_options['future'] ) {
			$post_status[] = 'future';
		}

		// Private Posts
		if ( "private" == $delete_options['private'] ) {
			$post_status[] = 'private';
		}

		if ( $delete_options['restrict'] == "true" ) {
			$options['op'] = $delete_options['post_status_op'];
			$options['days'] = $delete_options['post_status_days'];

			if ( ! class_exists( 'Bulk_Delete_By_Days' ) ) {
				require_once Bulk_Delete::$PLUGIN_DIR . '/include/util/class-bulk-delete-by-days.php';
			}
			new Bulk_Delete_By_Days;
		}

		// now retrieve all posts and delete them
		$options['post_status'] = $post_status;

		$post_ids = bd_query( $options );
		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, $force_delete );
		}

		$posts_deleted += count( $post_ids );
		return $posts_deleted;
	}

	/**
	 * Render Delete posts by category box
	 */
	public static function render_delete_posts_by_category_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_CATEGORY ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		$types = get_post_types( array(
				'_builtin' => false,
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
                    <label for="smbd_cats_all"><?php _e( "All Categories", 'bulk-delete' ) ?></label>
                </td>
            </tr>
        </table>

        <table class="optiontable">
            <tr>
                <td colspan="2">
                    <h4><?php _e( "Choose your filtering options", 'bulk-delete' ); ?></h4>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_cats_restrict" id="smbd_cats_restrict" value = "true" type = "checkbox" >
                </td>
                <td>
                    <?php _e( "Only restrict to posts which are ", 'bulk-delete' );?>
                    <select name="smbd_cats_op" id="smbd_cats_op" disabled>
                        <option value ="<"><?php _e( "older than", 'bulk-delete' );?></option>
                        <option value =">"><?php _e( "posted within last", 'bulk-delete' );?></option>
                    </select>
                    <input type ="textbox" name="smbd_cats_days" id="smbd_cats_days" disabled value ="0" maxlength="4" size="4" /><?php _e( "days", 'bulk-delete' );?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_cats_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e( 'Move to Trash', 'bulk-delete' ); ?>
                    <input name="smbd_cats_force_delete" value = "true" type = "radio" /> <?php _e( 'Delete permanently', 'bulk-delete' ); ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_cats_private" value = "false" type = "radio" checked="checked" /> <?php _e( 'Public posts', 'bulk-delete' ); ?>
                    <input name="smbd_cats_private" value = "true" type = "radio" /> <?php _e( 'Private Posts', 'bulk-delete' ); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_cats_limit" id="smbd_cats_limit" value = "true" type = "checkbox">
                </td>
                <td>
                    <?php _e( "Only delete first ", 'bulk-delete' );?>
                    <input type ="textbox" name="smbd_cats_limit_to" id="smbd_cats_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e( "posts.", 'bulk-delete' );?>
                    <?php _e( "Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete' ) ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_cats_cron" value = "false" type = "radio" checked="checked" /> <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbd_cats_cron" value = "true" type = "radio" id = "smbd_cats_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbd_cats_cron_start" id = "smbd_cats_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name = "smbd_cats_cron_freq" id = "smbd_cats_cron_freq" disabled>
                        <option value = "-1"><?php _e( "Don't repeat", 'bulk-delete' ); ?></option>
<?php
		$schedules = wp_get_schedules();
		foreach ( $schedules as $key => $value ) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php
		}
?>
                    </select>
                    <span class = "bd-cats-pro" style = "color:red"><?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-category/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sc">Buy now</a></span>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <?php _e( "Enter time in Y-m-d H:i:s format or enter now to use current time", 'bulk-delete' );?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="bd_action" value = "delete_posts_by_category" class="button-primary"><?php _e( 'Bulk Delete ', 'bulk-delete' ); ?>&raquo;</button>
        </p>
        <!-- Category end-->
<?php
	}

	/**
	 * Process delete posts by category
	 *
	 * @since 5.0
	 * @static
	 */
	public static function do_delete_posts_by_category() {

		$delete_options = array();
		$delete_options['post_type']     = array_get( $_POST, 'smbd_cat_post_type', 'post' );
		$delete_options['selected_cats'] = array_get( $_POST, 'smbd_cats' );
		$delete_options['restrict']      = array_get( $_POST, 'smbd_cats_restrict', false );
		$delete_options['private']       = array_get( $_POST, 'smbd_cats_private' );
		$delete_options['limit_to']      = absint( array_get( $_POST, 'smbd_cats_limit_to', 0 ) );
		$delete_options['force_delete']  = array_get( $_POST, 'smbd_cats_force_delete', 'false' );

		$delete_options['cats_op']       = array_get( $_POST, 'smbd_cats_op' );
		$delete_options['cats_days']     = array_get( $_POST, 'smbd_cats_days' );

		if ( array_get( $_POST, 'smbd_cats_cron', 'false' ) == 'true' ) {
			$freq = $_POST['smbd_cats_cron_freq'];
			$time = strtotime( $_POST['smbd_cats_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

			if ( $freq == -1 ) {
				wp_schedule_single_event( $time, Bulk_Delete::CRON_HOOK_CATEGORY, array( $delete_options ) );
			} else {
				wp_schedule_event( $time, $freq , Bulk_Delete::CRON_HOOK_CATEGORY, array( $delete_options ) );
			}

			$msg = __( 'Posts from the selected categories are scheduled for deletion.', 'bulk-delete' ) . ' ' .
				sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/admin.php?page=' . Bulk_Delete::CRON_PAGE_SLUG );
		} else {
			$deleted_count = self::delete_posts_by_category( $delete_options );
			$msg = sprintf( _n( 'Deleted %d post from the selected categories', 'Deleted %d posts from the selected categories' , $deleted_count, 'bulk-delete' ), $deleted_count );
		}

		add_settings_error(
			Bulk_Delete::POSTS_PAGE_SLUG,
			'deleted-posts',
			$msg,
			'updated'
		);
	}

	/**
	 * Delete posts by category
	 *
	 * @since 5.0
	 * @static
	 * @param array   $delete_options Options for deleting posts
	 * @return int   $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_category( $delete_options ) {

		// For compatibility reasons set default post type to 'post'
		$post_type     = array_get( $delete_options, 'post_type', 'post' );
		$selected_cats = $delete_options['selected_cats'];

		$options = array(
			'post_type'    => $post_type,
			'category__in' => $selected_cats,
			'post_status'  => 'publish'
		);

		$private = $delete_options['private'];

		if ( $private == 'true' ) {
			$options[ 'post_status' ] = 'private';
		}

		$limit_to = $delete_options['limit_to'];

		if ( $limit_to > 0 ) {
			$options['showposts'] = $limit_to;
		} else {
			$options['nopaging']  = 'true';
		}

		if ( $delete_options['restrict'] == "true" ) {
			$options['op'] = $delete_options['cats_op'];
			$options['days'] = $delete_options['cats_days'];

			if ( ! class_exists( 'Bulk_Delete_By_Days' ) ) {
				require_once Bulk_Delete::$PLUGIN_DIR . '/include/util/class-bulk-delete-by-days.php';
			}
			new Bulk_Delete_By_Days;
		}

		$post_ids = bd_query( $options );
		foreach ( $post_ids as $post_id ) {
			// $force delete parameter to custom post types doesn't work
			if ( 'true' == $delete_options['force_delete'] ) {
				wp_delete_post( $post_id, true );
			} else {
				wp_trash_post( $post_id );
			}
		}

		return count( $post_ids );
	}

	/**
	 * Render delete posts by tag box
	 */
	public static function render_delete_posts_by_tag_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_TAG ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		$tags =  get_tags();
		if ( count( $tags ) > 0 ) {
?>
            <h4><?php _e( "Select the tags whose post you want to delete", 'bulk-delete' ) ?></h4>

            <!-- Tags start-->
            <fieldset class="options">
            <table class="optiontable">
<?php
			foreach ( $tags as $tag ) {
?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_tags[]" value = "<?php echo $tag->term_id; ?>" type = "checkbox">
                    </td>
                    <td>
                        <label for="smbd_tags"><?php echo $tag->name; ?> (<?php echo $tag->count . " "; _e( "Posts", 'bulk-delete' ); ?>)</label>
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
                        <label for="smbd_tags_all"><?php _e( "All Tags", 'bulk-delete' ) ?></label>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <h4><?php _e( "Choose your filtering options", 'bulk-delete' ); ?></h4>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_tags_restrict" id ="smbd_tags_restrict" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e( "Only restrict to posts which are ", 'bulk-delete' );?>
                        <select name="smbd_tags_op" id="smbd_tags_op" disabled>
                            <option value ="<"><?php _e( "older than", 'bulk-delete' );?></option>
                            <option value =">"><?php _e( "posted within last", 'bulk-delete' );?></option>
                        </select>
                        <input type ="textbox" name="smbd_tags_days" id ="smbd_tags_days" value ="0"  maxlength="4" size="4" disabled /><?php _e( "days", 'bulk-delete' );?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_tags_force_delete" value = "false" type = "radio" checked="checked" > <?php _e( 'Move to Trash', 'bulk-delete' ); ?>
                        <input name="smbd_tags_force_delete" value = "true" type = "radio" > <?php _e( 'Delete permanently', 'bulk-delete' ); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_tags_private" value = "false" type = "radio" checked="checked" /> <?php _e( 'Public posts', 'bulk-delete' ); ?>
                        <input name="smbd_tags_private" value = "true" type = "radio" /> <?php _e( 'Private Posts', 'bulk-delete' ); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_tags_limit" id="smbd_tags_limit" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e( "Only delete first ", 'bulk-delete' );?>
                        <input type ="textbox" name="smbd_tags_limit_to" id="smbd_tags_limit_to" disabled value ="0" maxlength="4" size="4" ><?php _e( "posts.", 'bulk-delete' );?>
                        <?php _e( "Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete' ) ?>
                    </td>
                </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_tags_cron" value = "false" type = "radio" checked="checked" > <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbd_tags_cron" value = "true" type = "radio" id = "smbd_tags_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbd_tags_cron_start" id = "smbd_tags_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name = "smbd_tags_cron_freq" id = "smbd_tags_cron_freq" disabled>
                        <option value = "-1"><?php _e( "Don't repeat", 'bulk-delete' ); ?></option>
<?php
			$schedules = wp_get_schedules();
			foreach ( $schedules as $key => $value ) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php
			}
?>
                    </select>
                    <span class = "bd-tags-pro" style = "color:red"><?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-tag/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-st">Buy now</a></span>
                </td>
            </tr>

            </table>
            </fieldset>
            <p class="submit">
                <button type="submit" name="bd_action" value = "delete_posts_by_tag" class="button-primary"><?php _e( 'Bulk Delete ', 'bulk-delete' ); ?>&raquo;</button>
            </p>
            <!-- Tags end-->
<?php
		} else {
?>
            <h4><?php _e( "You don't have any posts assigned to tags in this blog.", 'bulk-delete' ) ?></h4>
<?php
		}
	}

	/**
	 * Process Delete Posts by tag request
	 *
	 * @static
	 * @since 5.0
	 */
	public static function do_delete_posts_by_tag() {

		$delete_options = array();
		$delete_options['selected_tags'] = array_get( $_POST, 'smbd_tags' );
		$delete_options['restrict']      = array_get( $_POST, 'smbd_tags_restrict', false );
		$delete_options['private']       = array_get( $_POST, 'smbd_tags_private' );
		$delete_options['limit_to']      = absint( array_get( $_POST, 'smbd_tags_limit_to', 0 ) );
		$delete_options['force_delete']  = array_get( $_POST, 'smbd_tags_force_delete', 'false' );

		$delete_options['tags_op']       = array_get( $_POST, 'smbd_tags_op' );
		$delete_options['tags_days']     = array_get( $_POST, 'smbd_tags_days' );

		if ( array_get( $_POST, 'smbd_tags_cron', 'false' ) == 'true' ) {
			$freq = $_POST['smbd_tags_cron_freq'];
			$time = strtotime( $_POST['smbd_tags_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

			if ( $freq == -1 ) {
				wp_schedule_single_event( $time, Bulk_Delete::CRON_HOOK_TAG, array( $delete_options ) );
			} else {
				wp_schedule_event( $time, $freq, Bulk_Delete::CRON_HOOK_TAG, array( $delete_options ) );
			}
			$msg = __( 'Posts from the selected tags are scheduled for deletion.', 'bulk-delete' ) . ' ' .
				sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/admin.php?page=' . Bulk_Delete::CRON_PAGE_SLUG );
		} else {
			$deleted_count = self::delete_posts_by_tag( $delete_options );
			$msg = sprintf( _n( 'Deleted %d post from the selected tags', 'Deleted %d posts from the selected tags' , $deleted_count, 'bulk-delete' ), $deleted_count );
		}

		add_settings_error(
			Bulk_Delete::POSTS_PAGE_SLUG,
			'deleted-posts',
			$msg,
			'updated'
		);
	}

	/**
	 * Delete posts by tag
	 *
	 * @since 5.0
	 * @static
	 * @param  array $delete_options Options for deleting posts
	 * @return int   $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_tag( $delete_options ) {
		$selected_tags = $delete_options['selected_tags'];
		$options = array(
			'tag__in'     => $selected_tags,
			'post_status' => 'publish',
			'post_type'   => 'post',
		);

		$private = $delete_options['private'];

		if ( $private == 'true' ) {
			$options['post_status'] = 'private';
		}

		$limit_to = $delete_options['limit_to'];

		if ( $limit_to > 0 ) {
			$options['showposts'] = $limit_to;
		} else {
			$options['nopaging'] = 'true';
		}

		$force_delete = $delete_options['force_delete'];

		if ( $force_delete == 'true' ) {
			$force_delete = true;
		} else {
			$force_delete = false;
		}

		if ( $delete_options['restrict'] == "true" ) {
			$options['op'] = $delete_options['tags_op'];
			$options['days'] = $delete_options['tags_days'];

			if ( ! class_exists( 'Bulk_Delete_By_Days' ) ) {
				require_once Bulk_Delete::$PLUGIN_DIR . '/include/util/class-bulk-delete-by-days.php';
			}
			new Bulk_Delete_By_Days;
		}

		$post_ids = bd_query( $options );
		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, $force_delete );
		}

		return count( $post_ids );
	}

	/**
	 * Render delete by custom taxonomy box
	 */
	public static function render_delete_posts_by_taxonomy_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_TAX ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		$types =  get_post_types( array(
				'_builtin' => false
			), 'names'
		);

		array_unshift( $types, 'post' );

		$taxs =  get_taxonomies( array(
				'public'   => true,
				'_builtin' => false
			), 'objects'
		);

		$terms_array = array();
		if ( count( $taxs ) > 0 ) {
			foreach ( $taxs as $tax ) {
				$terms = get_terms( $tax->name );
				if ( count( $terms ) > 0 ) {
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

            <h4><?php _e( "Select the taxonomies whose post you want to delete", 'bulk-delete' ) ?></h4>

            <table class="optiontable">
<?php
			foreach ( $terms_array as $tax => $terms ) {
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

            <h4><?php _e( "The selected taxonomy has the following terms. Select the terms whose post you want to delete", 'bulk-delete' ) ?></h4>
            <p><?php _e( 'Note: The post count below for each term is the total number of posts in that term, irrespective of post type', 'bulk-delete' ); ?></p>
<?php
			foreach ( $terms_array as $tax => $terms ) {
?>
            <table class="optiontable terms_<?php echo $tax;?> terms">
<?php
				foreach ( $terms as $term ) {
?>
                    <tr>
                        <td scope="row" >
                            <input name="smbd_tax_terms[]" value = "<?php echo $term->slug; ?>" type = "checkbox" class = "terms" >
                        </td>
                        <td>
                            <label for="smbd_tax_terms"><?php echo $term->name; ?> (<?php echo $term->count . " "; _e( "Posts", 'bulk-delete' ); ?>)</label>
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
                        <h4><?php _e( "Choose your filtering options", 'bulk-delete' ); ?></h4>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_taxs_restrict" id ="smbd_taxs_restrict" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e( "Only restrict to posts which are ", 'bulk-delete' );?>
                        <select name="smbd_taxs_op" id="smbd_taxs_op" disabled>
                            <option value ="<"><?php _e( "older than", 'bulk-delete' );?></option>
                            <option value =">"><?php _e( "posted within last", 'bulk-delete' );?></option>
                        </select>
                        <input type ="textbox" name="smbd_taxs_days" id ="smbd_taxs_days" value ="0"  maxlength="4" size="4" disabled /><?php _e( "days", 'bulk-delete' );?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_taxs_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e( 'Move to Trash', 'bulk-delete' ); ?>
                        <input name="smbd_taxs_force_delete" value = "true" type = "radio" /> <?php _e( 'Delete permanently', 'bulk-delete' ); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_taxs_private" value = "false" type = "radio" checked="checked" /> <?php _e( 'Public posts', 'bulk-delete' ); ?>
                        <input name="smbd_taxs_private" value = "true" type = "radio" /> <?php _e( 'Private Posts', 'bulk-delete' ); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_taxs_limit" id="smbd_taxs_limit" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e( "Only delete first ", 'bulk-delete' );?>
                        <input type ="textbox" name="smbd_taxs_limit_to" id="smbd_taxs_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e( "posts.", 'bulk-delete' );?>
                        <?php _e( "Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete' ) ?>
                    </td>
                </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_taxs_cron" value = "false" type = "radio" checked="checked" > <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbd_taxs_cron" value = "true" type = "radio" id = "smbd_taxs_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbd_taxs_cron_start" id = "smbd_taxs_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name = "smbd_taxs_cron_freq" id = "smbd_taxs_cron_freq" disabled>
                        <option value = "-1"><?php _e( "Don't repeat", 'bulk-delete' ); ?></option>
<?php
			$schedules = wp_get_schedules();
			foreach ( $schedules as $key => $value ) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php
			}
?>
                    </select>
                    <span class = "bd-taxs-pro" style = "color:red"><?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-taxonomy/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-stx">Buy now</a></span>
                </td>
            </tr>

            </table>
            </fieldset>
            <p class="submit">
                <button type="submit" name="bd_action" value = "delete_posts_by_taxonomy" class="button-primary"><?php _e( "Bulk Delete ", 'bulk-delete' ) ?>&raquo;</button>
            </p>
            <!-- Custom tax end-->
<?php
		} else {
?>
            <h4><?php _e( "You don't have any posts assigned to custom taxonomies in this blog.", 'bulk-delete' ) ?></h4>
<?php
		}
	}

	/**
	 * Process Delete posts by Taxonomy Request
	 *
	 * @static
	 * @since 5.0
	 */
	public static function do_delete_posts_by_taxonomy() {

		$delete_options = array();
		$delete_options['post_type']          = array_get( $_POST, 'smbd_tax_post_type', 'post' );
		$delete_options['selected_taxs']      = array_get( $_POST, 'smbd_taxs' );
		$delete_options['selected_tax_terms'] = array_get( $_POST, 'smbd_tax_terms' );
		$delete_options['restrict']           = array_get( $_POST, 'smbd_taxs_restrict', false );
		$delete_options['private']            = array_get( $_POST, 'smbd_taxs_private' );
		$delete_options['limit_to']           = absint( array_get( $_POST, 'smbd_taxs_limit_to', 0 ) );
		$delete_options['force_delete']       = array_get( $_POST, 'smbd_taxs_force_delete', 'false' );

		$delete_options['taxs_op']            = array_get( $_POST, 'smbd_taxs_op' );
		$delete_options['taxs_days']          = array_get( $_POST, 'smbd_taxs_days' );

		if ( array_get( $_POST, 'smbd_taxs_cron', 'false' ) == 'true' ) {
			$freq = $_POST['smbd_taxs_cron_freq'];
			$time = strtotime( $_POST['smbd_taxs_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

			if ( $freq == -1 ) {
				wp_schedule_single_event( $time, Bulk_Delete::CRON_HOOK_TAXONOMY, array( $delete_options ) );
			} else {
				wp_schedule_event( $time, $freq, Bulk_Delete::CRON_HOOK_TAXONOMY, array( $delete_options ) );
			}
			$msg = __( 'Posts from the selected custom taxonomies are scheduled for deletion.', 'bulk-delete' ) . ' ' .
				sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/admin.php?page=' . Bulk_Delete::CRON_PAGE_SLUG );
		} else {
			$deleted_count = self::delete_posts_by_taxonomy( $delete_options );
			$msg = sprintf( _n( 'Deleted %d post from the selected custom taxonomies', 'Deleted %d posts from the selected custom taxonomies' , $deleted_count, 'bulk-delete' ), $deleted_count );
		}

		add_settings_error(
			Bulk_Delete::POSTS_PAGE_SLUG,
			'deleted-posts',
			$msg,
			'updated'
		);
	}

	/**
	 * Delete posts by custom taxonomy
	 *
	 * @since 5.0
	 * @static
	 * @param array   $delete_options Options for deleting posts
	 * @return int   $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_taxonomy( $delete_options ) {
		// For compatibility reasons set default post type to 'post'
		$post_type          = array_get( $delete_options, 'post_type', 'post' );
		$selected_taxs      = $delete_options['selected_taxs'];
		$selected_tax_terms = $delete_options['selected_tax_terms'];

		$options = array(
			'post_status' => 'publish',
			'post_type'   => $post_type,
			'tax_query'   => array(
				array(
					'taxonomy' => $selected_taxs,
					'terms'    => $selected_tax_terms,
					'field'    => 'slug',
				),
			)
		);

		$private = $delete_options['private'];

		if ( $private == 'true' ) {
			$options['post_status']  = 'private';
		}

		$limit_to = $delete_options['limit_to'];

		if ( $limit_to > 0 ) {
			$options['showposts'] = $limit_to;
		} else {
			$options['nopaging'] = 'true';
		}

		$force_delete = $delete_options['force_delete'];

		if ( $force_delete == 'true' ) {
			$force_delete = true;
		} else {
			$force_delete = false;
		}

		if ( $delete_options['restrict'] == "true" ) {
			$options['op'] = $delete_options['taxs_op'];
			$options['days'] = $delete_options['taxs_days'];

			if ( ! class_exists( 'Bulk_Delete_By_Days' ) ) {
				require_once Bulk_Delete::$PLUGIN_DIR . '/include/util/class-bulk-delete-by-days.php';
			}
			new Bulk_Delete_By_Days;
		}

		$post_ids = bd_query( $options );
		foreach ( $post_ids as $post_id ) {
			// $force delete parameter to custom post types doesn't work
			if ( $force_delete ) {
				wp_delete_post( $post_id, true );
			} else {
				wp_trash_post( $post_id );
			}
		}

		return count( $post_ids );
	}


	/**
	 * Render delete by custom post type box
	 *
	 * @static
	 */
	public static function render_delete_posts_by_post_type_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_POST_TYPE ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		$types_array = array();

		$types =  get_post_types( array(
				'_builtin' => false
			), 'names'
		);

		if ( count( $types ) > 0 ) {
			foreach ( $types as $type ) {
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
                    <label for="smbd_types"><?php echo BD_Util::display_post_type_status( $type ), ' (', $count, ')'; ?></label>
                    </td>
                </tr>
<?php
			}
?>
                <tr>
                    <td colspan="2">
                        <h4><?php _e( "Choose your filtering options", 'bulk-delete' ); ?></h4>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_types_restrict" id ="smbd_types_restrict" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e( "Only restrict to posts which are ", 'bulk-delete' );?>
                        <select name="smbd_types_op" id="smbd_types_op" disabled>
                            <option value ="<"><?php _e( "older than", 'bulk-delete' );?></option>
                            <option value =">"><?php _e( "posted within last", 'bulk-delete' );?></option>
                        </select>
                        <input type ="textbox" name="smbd_types_days" id ="smbd_types_days" value ="0"  maxlength="4" size="4" disabled /><?php _e( "days", 'bulk-delete' );?>
                    </td>
                </tr>

                <tr>
                    <td scope="row" colspan="2">
                        <input name="smbd_types_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e( 'Move to Trash', 'bulk-delete' ); ?>
                        <input name="smbd_types_force_delete" value = "true" type = "radio" /> <?php _e( 'Delete permanently', 'bulk-delete' ); ?>
                    </td>
                </tr>

                <tr>
                    <td scope="row">
                        <input name="smbd_types_limit" id="smbd_types_limit" value = "true" type = "checkbox">
                    </td>
                    <td>
                        <?php _e( "Only delete first ", 'bulk-delete' );?>
                        <input type ="textbox" name="smbd_types_limit_to" id="smbd_types_limit_to" disabled value ="0" maxlength="4" size="4" /><?php _e( "posts.", 'bulk-delete' );?>
                        <?php _e( "Use this option if there are more than 1000 posts and the script timesout.", 'bulk-delete' ) ?>
                    </td>
                </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbd_types_cron" value = "false" type = "radio" checked="checked" > <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbd_types_cron" value = "true" type = "radio" id = "smbd_types_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbd_types_cron_start" id = "smbd_types_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name = "smbd_types_cron_freq" id = "smbd_types_cron_freq" disabled>
                        <option value = "-1"><?php _e( "Don't repeat", 'bulk-delete' ); ?></option>
<?php
			$schedules = wp_get_schedules();
			foreach ( $schedules as $key => $value ) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php
			}
?>
                    </select>
                    <span class = "bd-types-pro" style = "color:red"><?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/scheduler-for-deleting-posts-by-post-type/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-spt">Buy now</a></span>
                </td>
            </tr>

            </table>
            </fieldset>
            <p class="submit">
                <button type="submit" name="bd_action" value = "delete_posts_by_post_type" class="button-primary"><?php _e( "Bulk Delete ", 'bulk-delete' ) ?>&raquo;</button>
            </p>
            <!-- Custom post type end-->
<?php
		} else {
?>
            <h4><?php _e( "You don't have any posts assigned to custom post types in this blog.", 'bulk-delete' ) ?></h4>
<?php
		}
	}

	/**
	 * Process request to delete posts by post type
	 *
	 * @static
	 * @since 5.0
	 */
	public static function do_delete_posts_by_post_type() {

		$delete_options                   = array();

		$delete_options['selected_types'] = array_get( $_POST, 'smbd_types' );
		$delete_options['restrict']       = array_get( $_POST, 'smbd_types_restrict', false );
		$delete_options['limit_to']       = absint( array_get( $_POST, 'smbd_types_limit_to', 0 ) );
		$delete_options['force_delete']   = array_get( $_POST, 'smbd_types_force_delete', 'false' );

		$delete_options['types_op']       = array_get( $_POST, 'smbd_types_op' );
		$delete_options['types_days']     = array_get( $_POST, 'smbd_types_days' );

		if ( array_get( $_POST, 'smbd_types_cron', 'false' ) == 'true' ) {
			$freq = $_POST['smbd_types_cron_freq'];
			$time = strtotime( $_POST['smbd_types_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

			if ( $freq == -1 ) {
				wp_schedule_single_event( $time, Bulk_Delete::CRON_HOOK_POST_TYPE, array( $delete_options ) );
			} else {
				wp_schedule_event( $time, $freq, Bulk_Delete::CRON_HOOK_POST_TYPE, array( $delete_options ) );
			}

			$msg = __( 'Posts from the selected custom post type are scheduled for deletion.', 'bulk-delete' ) . ' ' .
				sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/admin.php?page=' . Bulk_Delete::CRON_PAGE_SLUG );
		} else {
			$deleted_count = self::delete_posts_by_post_type( $delete_options );
			$msg = sprintf( _n( 'Deleted %d post from the selected custom post type', 'Deleted %d posts from the selected custom post type' , $deleted_count, 'bulk-delete' ), $deleted_count );
		}

		add_settings_error(
			Bulk_Delete::POSTS_PAGE_SLUG,
			'deleted-posts',
			$msg,
			'updated'
		);
	}

	/**
	 * Delete posts by custom post type
	 *
	 * @static
	 * @since  5.0
	 * @param  array $delete_options Options for deleting posts
	 * @return int   $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_post_type( $delete_options ) {
		$count = 0;
		$selected_types = $delete_options['selected_types'];

		foreach ( $selected_types as $selected_type ) {

			$type_status = BD_Util::split_post_type_status( $selected_type );

			$type        = $type_status['type'];
			$status      = $type_status['status'];

			$options = array(
				'post_status' => $status,
				'post_type'   => $type,
			);

			$limit_to = $delete_options['limit_to'];

			if ( $limit_to > 0 ) {
				$options['showposts'] = $limit_to;
			} else {
				$options['nopaging'] = 'true';
			}

			$force_delete = $delete_options['force_delete'];

			if ( $force_delete == 'true' ) {
				$force_delete = true;
			} else {
				$force_delete = false;
			}

			if ( $delete_options['restrict'] == "true" ) {
				$options['op'] = $delete_options['types_op'];
				$options['days'] = $delete_options['types_days'];

				if ( ! class_exists( 'Bulk_Delete_By_Days' ) ) {
					require_once Bulk_Delete::$PLUGIN_DIR . '/include/util/class-bulk-delete-by-days.php';
				}
				new Bulk_Delete_By_Days;
			}

			$post_ids = bd_query( $options );
			foreach ( $post_ids as $post_id ) {
				// $force delete parameter to custom post types doesn't work
				if ( $force_delete ) {
					wp_delete_post( $post_id, true );
				} else {
					wp_trash_post( $post_id );
				}
			}

			$count += count( $post_ids );
		}

		return $count;
	}

	/**
	 * Render delete by url box
	 *
	 * @static
	 */
	public static function render_delete_posts_by_url_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_URL ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}
?>
        <!-- URLs start-->
        <h4><?php _e( 'Delete posts and pages that have the following Permalink', 'bulk-delete' ); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td scope="row">
                    <label for="smdb_specific_pages"><?php _e( "Enter one post url (not post ids) per line", 'bulk-delete' ); ?></label>
                    <br/>
                    <textarea style="width: 450px; height: 80px;" id="smdb_specific_pages_urls" name="smdb_specific_pages_urls" rows="5" columns="80" ></textarea>
                </td>
            </tr>

            <tr>
                <td>
                    <h4><?php _e( "Choose your filtering options", 'bulk-delete' ); ?></h4>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbd_specific_force_delete" value = "false" type = "radio" checked="checked" /> <?php _e( 'Move to Trash', 'bulk-delete' ); ?>
                    <input name="smbd_specific_force_delete" value = "true" type = "radio" /> <?php _e( 'Delete permanently', 'bulk-delete' ); ?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="bd_action" value = "delete_posts_by_url" class="button-primary"><?php _e( "Bulk Delete ", 'bulk-delete' ) ?>&raquo;</button>
        </p>
        <!-- URLs end-->
<?php
	}

	/**
	 * Delete posts by url
	 *
	 * @static
	 * @since 5.0
	 */
	public static function do_delete_posts_by_url() {

		$force_delete = array_get( $_POST, 'smbd_specific_force_delete' );
		if ( $force_delete == 'true' ) {
			$force_delete = true;
		} else {
			$force_delete = false;
		}

		$urls = preg_split( '/\r\n|\r|\n/', array_get( $_POST, 'smdb_specific_pages_urls' ) );
		foreach ( $urls as $url ) {
			$checkedurl = $url;
			if ( substr( $checkedurl , 0, 1 ) == '/' ) {
				$checkedurl = get_site_url() . $checkedurl ;
			}
			$postid = url_to_postid( $checkedurl );
			wp_delete_post( $postid, $force_delete );
		}

		$deleted_count = count( $urls );
		$msg = sprintf( _n( 'Deleted %d post with the specified urls', 'Deleted %d posts with the specified urls' , $deleted_count, 'bulk-delete' ), $deleted_count );

		add_settings_error(
			Bulk_Delete::POSTS_PAGE_SLUG,
			'deleted-posts',
			$msg,
			'updated'
		);
	}

	/**
	 * Render delete by post revisions box
	 *
	 * @static
	 */
	public static function render_posts_by_revision_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_POST_REVISION ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		global $wpdb;

		$revisions = $wpdb->get_var( "select count(*) from $wpdb->posts where post_type = 'revision'" );
?>
        <!-- Post Revisions start-->
        <h4><?php _e( "Select the posts which you want to delete", 'bulk-delete' ); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td>
                    <input name="smbd_revisions" id ="smbd_revisions" value = "revisions" type = "checkbox" />
                    <label for="smbd_revisions"><?php _e( "All Revisions", 'bulk-delete' ); ?> (<?php echo $revisions . " "; _e( "Revisions", 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="bd_action" value = "delete_posts_by_revision" class="button-primary"><?php _e( "Bulk Delete ", 'bulk-delete' ) ?>&raquo;</button>
        </p>
        <!-- Post Revisions end-->
<?php
	}

	/**
	 * Process delete revisions request
	 *
	 * @static
	 * @since 5.0
	 */
	public static function do_delete_posts_by_revision() {
		$delete_options['revisions'] = array_get( $_POST, 'smbd_revisions' );
		$deleted_count = self::delete_posts_by_revision( $delete_options );

		$msg = sprintf( _n( 'Deleted %d post revision', 'Deleted %d post revisions' , $deleted_count, 'bulk-delete' ), $deleted_count );

		add_settings_error(
			Bulk_Delete::POSTS_PAGE_SLUG,
			'deleted-posts',
			$msg,
			'updated'
		);
	}

	/**
	 * Delete all post revisions
	 *
	 * @since 5.0
	 * @static
	 * @param unknown $delete_options
	 * @return integer
	 */
	public static function delete_posts_by_revision( $delete_options ) {
		global $wpdb;

		// Revisions
		if ( "revisions" == $delete_options['revisions'] ) {
			$revisions = $wpdb->get_results( "select ID from $wpdb->posts where post_type = 'revision'" );

			foreach ( $revisions as $revision ) {
				wp_delete_post( $revision->ID );
			}

			return count( $revisions );
		}

		return 0;
	}

	/**
	 * Render delete posts by custom field box
	 *
	 * @static
	 */
	public static function render_delete_posts_by_custom_field_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_CUSTOM_FIELD ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		if ( !class_exists( 'Bulk_Delete_Posts_By_Custom_Field' ) ) {
?>
        <!-- Custom Field box start-->
        <p>
            <span class = "bd-post-custom-field-pro" style = "color:red">
                <?php _e( 'You need "Bulk Delete Posts by Custom Field" Addon, to delete post by custom field.', 'bulk-delete' ); ?>
                <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-custom-field/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-cf">Buy now</a>
            </span>
        </p>
        <!-- Custom Field box end-->
<?php
		} else {
			Bulk_Delete_Posts_By_Custom_Field::render_delete_posts_by_custom_field_box();
		}
	}

	/**
	 * Render posts by title box
	 *
	 * @since 5.0
	 * @static
	 */
	public static function render_delete_posts_by_title_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_TITLE ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		if ( !class_exists( 'Bulk_Delete_Posts_By_Title' ) ) {
?>
        <!-- Title box start-->
        <p>
            <span class = "bd-post-title-pro" style = "color:red">
                <?php _e( 'You need "Bulk Delete Posts by Title" Addon, to delete post by title.', 'bulk-delete' ); ?>
                <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-title/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-ti">Buy now</a>
            </span>
        </p>
        <!-- Title box end-->
<?php
		} else {
			Bulk_Delete_Posts_By_Title::render_delete_posts_by_title_box();
		}
	}

	/**
	 * Render delete posts by duplicate title box
	 *
	 * @since 5.0
	 * @static
	 * @access public
	 */
	public static function render_delete_posts_by_duplicate_title_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_DUPLICATE_TITLE ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		if ( !class_exists( 'Bulk_Delete_Posts_By_Duplicate_Title' ) ) {
?>
        <!-- Duplicate Title box start-->
        <p>
            <span class = "bd-post-title-pro" style = "color:red">
                <?php _e( 'You need "Bulk Delete Posts by Duplicate Title" Addon, to delete post by duplicate title.', 'bulk-delete' ); ?>
                <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-duplicate-title/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-dti">Buy now</a>
            </span>
        </p>
        <!-- Duplicate Title box end-->
<?php
		} else {
			Bulk_Delete_Posts_By_Duplicate_Title::render_delete_posts_by_duplicate_title_box();
		}
	}

	/**
	 * Delete posts by user role
	 *
	 * @static
	 * @since 5.2
	 */
	public static function render_delete_posts_by_user_role_box() {

		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_POST_BY_ROLE ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}
		if ( !class_exists( 'Bulk_Delete_Posts_By_User_Role' ) ) {
?>
        <!-- Posts by user role start-->
        <p>
            <span class = "bd-post-by-role-pro" style = "color:red">
                <?php _e( 'You need "Bulk Delete Posts by User Role" Addon, to delete post based on User Role', 'bulk-delete' ); ?>
                <a href = "http://bulkwp.com/addons/bulk-delete-posts-by-user-role/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-ur">Buy now</a>
            </span>
        </p>
        <!-- Posts by user role end-->
<?php
		} else {
			Bulk_Delete_Posts_By_User_Role::render_delete_posts_by_user_role_box();
		}
	}

	/**
	 * Render delete posts from trash box
	 *
	 * @since 5.1
	 * @static
	 */
	public static function render_delete_posts_from_trash() {
		if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_POST_FROM_TRASH ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );
			return;
		}

		if ( !class_exists( 'Bulk_Delete_From_Trash' ) ) {
?>
        <!-- Posts In Trash box start-->
        <p>
            <span class = "bd-post-trash-pro" style = "color:red">
                <?php _e( 'You need "Bulk Delete From Trash" Addon, to delete post in Trash.', 'bulk-delete' ); ?>
                <a href = "http://bulkwp.com/addons/bulk-delete-from-trash/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-th">Buy now</a>
            </span>
        </p>
        <!-- Posts In Trash box end-->
<?php
		} else {

			/**
			 * Render delete posts from trash box
			 *
			 * @since 5.4
			 */
			do_action( 'bd_render_delete_posts_from_trash' );
		}
	}

	/**
	 * Filter JS Array and add validation hooks
	 *
	 * @since 5.4
	 * @static
	 * @param array   $js_array JavaScript Array
	 * @return array           Modified JavaScript Array
	 */
	public static function filter_js_array( $js_array ) {
		$js_array['msg']['deletePostsWarning'] = __( 'Are you sure you want to delete all the posts based on the selected option?', 'bulk-delete' );
		$js_array['msg']['selectPostOption'] = __( 'Please select posts from at least one option', 'bulk-delete' );

		$js_array['validators']['delete_posts_by_url'] = 'validateUrl';
		$js_array['error_msg']['delete_posts_by_url'] = 'enterUrl';
		$js_array['msg']['enterUrl'] = __( 'Please enter at least one post url', 'bulk-delete' );

		$js_array['dt_iterators'][] = '_cats';
		$js_array['dt_iterators'][] = '_tags';
		$js_array['dt_iterators'][] = '_taxs';
		$js_array['dt_iterators'][] = '_types';
		$js_array['dt_iterators'][] = '_post_status';
		return $js_array;
	}

	/**
	 * Process delete cron job request.
	 * This should ideally go in a separate class. But I was
	 * lazy to create a separate class for a single function
	 *
	 * @since 5.0
	 * @static
	 */
	public static function do_delete_cron() {
		if ( check_admin_referer( 'sm-bulk-delete-cron', 'sm-bulk-delete-cron-nonce' ) ) {
			$cron_id = absint( $_GET['cron_id'] );
			$cron_items = BD_Util::get_cron_schedules();
			wp_unschedule_event( $cron_items[$cron_id]['timestamp'], $cron_items[$cron_id]['type'], $cron_items[$cron_id]['args'] );

			$msg = __( 'The selected scheduled job was successfully deleted ', 'bulk-delete' );

			add_settings_error(
				Bulk_Delete::CRON_PAGE_SLUG,
				'deleted-cron',
				$msg,
				'updated'
			);
		}
	}
}

// hooks
add_action( 'bd_delete_posts_by_status'    , array( 'Bulk_Delete_Posts', 'do_delete_posts_by_status' ) );
add_action( 'bd_delete_posts_by_category'  , array( 'Bulk_Delete_Posts', 'do_delete_posts_by_category' ) );
add_action( 'bd_delete_posts_by_tag'       , array( 'Bulk_Delete_Posts', 'do_delete_posts_by_tag' ) );
add_action( 'bd_delete_posts_by_taxonomy'  , array( 'Bulk_Delete_Posts', 'do_delete_posts_by_taxonomy' ) );
add_action( 'bd_delete_posts_by_post_type' , array( 'Bulk_Delete_Posts', 'do_delete_posts_by_post_type' ) );
add_action( 'bd_delete_posts_by_url'       , array( 'Bulk_Delete_Posts', 'do_delete_posts_by_url' ) );
add_action( 'bd_delete_posts_by_revision'  , array( 'Bulk_Delete_Posts', 'do_delete_posts_by_revision' ) );

add_action( 'bd_delete_cron'               , array( 'Bulk_Delete_Posts', 'do_delete_cron' ) );
add_filter( 'bd_javascript_array'          , array( 'Bulk_Delete_Posts', 'filter_js_array' ) );
?>
