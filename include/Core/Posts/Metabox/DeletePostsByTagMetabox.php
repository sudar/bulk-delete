<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Tag Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByTagMetabox extends PostsMetabox {
	/**
	 * Base parameters setup.
	 */
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'tags';
		$this->meta_box_slug = 'bd_by_tag';
		$this->action        = 'delete_posts_by_tag';
		$this->cron_hook     = 'do-bulk-delete-tag';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-tag/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-st';
		$this->messages      = array(
			'box_label' => __( 'By Post Tag', 'bulk-delete' ),
			'scheduled' => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
		);
	}

	/**
	 * Render Delete posts by tag box.
	 */
	public function render() {
		if ( ! $this->are_tags_present() ) : ?>
			<h4>
				<?php _e( 'There are no tags present in this WordPress installation.', 'bulk-delete' ); ?>
			</h4>
			<?php return; ?>
		<?php endif; ?>

		<h4><?php _e( 'Select the tags from which you want to delete posts', 'bulk-delete' ); ?></h4>

		<!-- Tags start-->
		<fieldset class="options">
			<table class="form-table">
				<tr>
					<td scope="row" colspan="2">
						<?php $this->render_tags_dropdown(); ?>
					</td>
				</tr>
			</table>

			<table class="optiontable">
				<?php
				$this->render_filtering_table_header();
				$this->render_restrict_settings();
				$this->render_delete_settings();
				$this->render_private_post_settings();
				$this->render_limit_settings();
				$this->render_cron_settings();
				?>
			</table>
		</fieldset>
<?php
		$this->render_submit_button();
	}

	/**
	 * Process delete posts user inputs by tag.
	 *
	 * @param array $request Request array.
	 * @param array $options Options for deleting posts.
	 *
	 * @return array $options  Inputs from user for posts that were need to delete
	 */
	protected function convert_user_input_to_options( $request, $options ) {
		$options['selected_tags'] = bd_array_get( $request, 'smbd_tags' );
		$options['private']       = bd_array_get( $request, 'smbd_tags_private' );

		return $options;
	}

	/**
	 * Delete posts by tag.
	 *
	 * @param array $delete_options Options for deleting posts.
	 *
	 * @return int $posts_deleted  Number of posts that were deleted
	 */
	public function delete( $delete_options ) {
		$posts_deleted = 0;

		// Backward compatibility code. Will be removed in Bulk Delete v6.0.
		if ( array_key_exists( 'tags_op', $delete_options ) ) {
			$delete_options['date_op'] = $delete_options['tags_op'];
			$delete_options['days']    = $delete_options['tags_days'];
		}

		$delete_options = apply_filters( 'bd_delete_options', $delete_options );

		$options = array();

		$selected_tags = $delete_options['selected_tags'];
		if ( in_array( 'all', $selected_tags ) ) {
			$options['tag__not__in'] = array( 0 );
		} else {
			$options['tag__in'] = $selected_tags;
		}

		$options  = bd_build_query_options( $delete_options, $options );
		$post_ids = bd_query( $options );
		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, $delete_options['force_delete'] );
		}

		$posts_deleted += count( $post_ids );

		return $posts_deleted;
	}

	/**
	 * Response message for deleting posts.
	 *
	 * @param int $items_deleted count of items deleted.
	 *
	 * @return string Response message
	 */
	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted %d post with the selected post tag', 'Deleted %d posts with the selected post tag', $items_deleted, 'bulk-delete' );
	}
}
