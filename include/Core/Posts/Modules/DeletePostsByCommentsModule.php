<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\BulkDelete\Core\Posts\PostsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Comments Module.
 *
 * @since 6.0.0
 */
class DeletePostsByCommentsModule extends PostsModule {
	/**
	 * Base parameters setup.
	 */
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'comments';
		$this->meta_box_slug = 'bd_by_comments';
		$this->action        = 'delete_posts_by_comments';
		$this->cron_hook     = 'do-bulk-delete-posts-by-comments';
		$this->scheduler_url = 'https://bulkwp.com/addons/scheduler-for-deleting-posts-by-comments/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bds-p-c';
		$this->messages      = array(
			'box_label'         => __( 'By Comment count', 'bulk-delete' ),
			'scheduled'         => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
			'cron_label'        => __( 'Delete Post By Comments', 'bulk-delete' ),
			'confirm_deletion'  => __( 'Are you sure you want to delete all the posts based on the selected comment count setting?', 'bulk-delete' ),
			'confirm_scheduled' => __( 'Are you sure you want to schedule the deletion of all the posts based on the selected comment count setting?', 'bulk-delete' ),
			'validation_error'  => __( 'Please enter the comments count based on which posts should be deleted. A valid comment count will be greater than or equal to zero', 'bulk-delete' ),
			/* translators: 1 Number of posts deleted */
			'deleted_one'       => __( 'Deleted %d post with the selected comments count', 'bulk-delete' ),
			/* translators: 1 Number of posts deleted */
			'deleted_multiple'  => __( 'Deleted %d posts with the selected comments count', 'bulk-delete' ),
		);
	}

	/**
	 * Render Delete posts by comments box.
	 */
	public function render() {
		?>
		<h4><?php _e( 'Delete Posts based on the number of comments', 'bulk-delete' ); ?></h4>

		<!-- Comments start-->
		<fieldset class="options">
			<table class="optiontable">
				<?php $this->render_post_type_with_status( false, 'comments' ); ?>
				<tr>
					<td>
						<?php _e( 'Delete posts that have comments', 'bulk-delete' ); ?>
						<?php $this->render_operators_dropdown( [ 'equals', 'numeric' ] ); ?>
						<input type="number" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_count_value"
						id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_count_value" placeholder="Comments Count" min="0" class="comments_count_num">
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
	 * Process delete posts, user inputs by comments count.
	 *
	 * @param array $request Request array.
	 * @param array $options Options for deleting posts.
	 *
	 * @return array $options Inputs from user for posts that were need to be deleted.
	 */
	protected function convert_user_input_to_options( $request, $options ) {
		$options['operator']           = bd_array_get( $request, 'smbd_' . $this->field_slug . '_operator' );
		$options['comment_count']      = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_count_value' ) );
		$options['selected_post_type'] = bd_array_get( $request, 'smbd_' . $this->field_slug );

		return $options;
	}

	/**
	 * Build the Query from user input.
	 *
	 * @param array $options User Input.
	 *
	 * @return array $query Query Params.
	 */
	protected function build_query( $options ) {
		$query = array();

		if ( array_key_exists( 'selected_post_type', $options ) ) {
			$type_status = $this->split_post_type_and_status( $options['selected_post_type'] );
			$type        = $type_status['type'];
			$status      = $type_status['status'];

			$query['post_type']   = $type;
			$query['post_status'] = $status;
		}

		$query['comment_count'] = array(
			'compare' => $options['operator'],
			'value'   => $options['comment_count'],
		);

		return $query;
	}

	protected function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateCommentsCount';

		return $js_array;
	}
}
