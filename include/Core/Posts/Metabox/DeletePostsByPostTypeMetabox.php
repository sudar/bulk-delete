<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Post Type Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByPostTypeMetabox extends PostsMetabox {
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'types';
		$this->meta_box_slug = 'bd_posts_by_types';
		$this->action        = 'delete_posts_by_post_type';
		$this->cron_hook     = 'do-bulk-delete-post-type';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-post-type/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-spt';
		$this->messages      = array(
			'box_label' => __( 'By Custom Post Type', 'bulk-delete' ),
			'scheduled' => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
		);
	}

	public function render() {
		$types_array = array();

		$types = get_post_types( array(
			'_builtin' => false,
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
			<h4><?php _e( 'Select the custom post types from which you want to delete posts', 'bulk-delete' ) ?></h4>

			<fieldset class="options">
				<table class="optiontable">
					<?php
					foreach ( $types_array as $type => $count ) {
						?>
						<tr>
							<td scope="row">
								<input name="smbd_types[]" value="<?php echo $type; ?>" type="checkbox">
							</td>
							<td>
								<label for="smbd_types"><?php echo $this->display_post_type_status( $type ), ' (', $count, ')'; ?></label>
							</td>
						</tr>
						<?php
					}

					$this->render_filtering_table_header();
					$this->render_restrict_settings();
					$this->render_delete_settings();
					$this->render_limit_settings();
					$this->render_cron_settings();
					?>
				</table>
			</fieldset>
			<?php
			$this->render_submit_button();
		} else {
			printf( '<h4>%s</h4>', __( "This WordPress installation doesn't have any non-empty custom post types", 'bulk-delete' ) );
		}
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['selected_types'] = bd_array_get( $request, 'smbd_types' );

		return $options;
	}

	public function delete( $options ) {
		/**
		 * Filter delete options before deleting posts.
		 *
		 * @since 6.0.0 Added `Metabox` parameter.
		 *
		 * @param array $options Delete options.
		 * @param \BulkWP\BulkDelete\Core\Base\BaseMetabox Metabox that is triggering deletion.
		 */
		$options = apply_filters( 'bd_delete_options', $options, $this );

		$posts_deleted  = 0;
		$selected_types = $options['selected_types'];

		foreach ( $selected_types as $selected_type ) {
			$query = $this->build_query( $selected_type );

			$posts_deleted += $this->delete_posts_from_query( $query, $options );
		}

		return $posts_deleted;
	}

	/**
	 * Build the query from the selected type.
	 *
	 * In this Module, this function accepts a string and not an array.
	 *
	 * @param string $selected_type Post type.
	 *
	 * @return array Query params.
	 */
	protected function build_query( $selected_type ) {
		$type_status = $this->split_post_type_status( $selected_type );

		$type   = $type_status['type'];
		$status = $type_status['status'];

		$query = array(
			'post_status' => $status,
			'post_type'   => $type,
		);

		return $query;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d post with the selected post type', 'Deleted %d posts with the selected post type', $items_deleted, 'bulk-delete' );
	}
}
