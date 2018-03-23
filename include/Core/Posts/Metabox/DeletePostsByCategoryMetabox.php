<?php
namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Category Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByCategoryMetabox extends PostsMetabox {
	private $cat_limit = 50;
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'cats';
		$this->meta_box_slug = 'bd_by_category';
		$this->action        = 'delete_posts_by_category';
		$this->cron_hook     = 'do-bulk-delete-cat';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-category/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sc';
		$this->messages      = array(
			'box_label' => __( 'By Post Category', 'bulk-delete' ),
			'scheduled' => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
		);
	}

	public function render() {
        ?>
        <!-- Category Start-->
        <h4><?php _e( 'Select the post type from which you want to delete posts by category', 'bulk-delete' ); ?></h4>
        <fieldset class="options">
            <table class="optiontable">
				<?php bd_render_post_type_dropdown( 'cats' ); ?>
            </table>

            <h4><?php _e( 'Select the categories from which you wan to delete posts', 'bulk-delete' ); ?></h4>
            <p><?php _e( 'Note: The post count below for each category is the total number of posts in that category, irrespective of post type', 'bulk-delete' ); ?>.</p>
			<?php
			$bd_select2_ajax_limit_categories = apply_filters( 'bd_select2_ajax_limit_categories', $this->cat_limit );

			$categories = get_categories( array(
					'hide_empty'    => false,
					'number'        => $bd_select2_ajax_limit_categories,
				)
			);
			?>
            <table class="form-table">
                <tr>
                    <td scope="row">
						<?php if( count($categories) >= $bd_select2_ajax_limit_categories ){?>
                            <select class="select2Ajax" name="smbd_cats[]" data-taxonomy="category" multiple data-placeholder="<?php _e( 'Select Categories', 'bulk-delete' ); ?>">
                                <option value="all" selected="selected"><?php _e( 'All Categories', 'bulk-delete' ); ?></option>
                            </select>
						<?php }else{?>
                            <select class="select2" name="smbd_cats[]" multiple data-placeholder="<?php _e( 'Select Categories', 'bulk-delete' ); ?>">
                                <option value="all" selected="selected"><?php _e( 'All Categories', 'bulk-delete' ); ?></option>
								<?php foreach ( $categories as $category ) { ?>
                                    <option value="<?php echo $category->cat_ID; ?>"><?php echo $category->cat_name, ' (', $category->count, ' ', __( 'Posts', 'bulk-delete' ), ')'; ?></option>
								<?php } ?>
                            </select>
						<?php }?>
                    </td>
                </tr>
            </table>

			<table class="optiontable">
				<?php
				$this->render_filtering_table_header();
				$this->render_restrict_settings();
				$this->render_delete_settings();
				//$this->render_private_post_settings();
				bd_render_private_post_settings( $this->field_slug );
				$this->render_limit_settings();
				$this->render_cron_settings();
				?>
			</table>

		</fieldset>
<?php
		$this->render_submit_button( 'delete_posts_by_category' );
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_type']     = bd_array_get( $_POST, 'smbd_cats_post_type', 'post' );
		$options['selected_cats'] = bd_array_get( $_POST, 'smbd_cats' );
		$options['private']       = bd_array_get_bool( $_POST, 'smbd_cats_private', false );

		return $options;
	}

	public function delete( $delete_options ) {
		$posts_deleted = 0;
		$delete_options['post_type'] = bd_array_get( $delete_options, 'post_type', 'post' );

		if ( array_key_exists( 'cats_op', $delete_options ) ) {
			$delete_options['date_op'] = $delete_options['cats_op'];
			$delete_options['days']    = $delete_options['cats_days'];
		}

		$delete_options = apply_filters( 'bd_delete_options', $delete_options );

		$options       = array();
		$selected_cats = $delete_options['selected_cats'];
		if ( in_array( 'all', $selected_cats ) ) {
			$options['category__not__in'] = array(0);
		} else {
			$options['category__in'] = $selected_cats;
		}

		$options  = bd_build_query_options( $delete_options, $options );
		$post_ids = bd_query( $options );

		foreach ( $post_ids as $post_id ) {
			// $force delete parameter to custom post types doesn't work
			if ( $delete_options['force_delete'] ) {
				wp_delete_post( $post_id, true );
			} else {
				wp_trash_post( $post_id );
			}
		}

		$posts_deleted += count( $post_ids );

		return $posts_deleted;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted %d post with the selected post category', 'Deleted %d posts with the selected post category', $items_deleted, 'bulk-delete' );
	}
}
