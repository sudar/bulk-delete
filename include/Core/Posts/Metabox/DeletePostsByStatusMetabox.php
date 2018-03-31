<?php
namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Status Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByStatusMetabox extends PostsMetabox {
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'post_status';
		$this->meta_box_slug = 'bd_posts_by_status';
		$this->action        = 'delete_posts_by_status';
		$this->cron_hook     = 'do-bulk-delete-post-by-status';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sps';
		$this->messages      = array(
			'box_label' => __( 'By Post Status', 'bulk-delete' ),
			'scheduled' => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
		);
	}

	public function render() {
		$post_statuses = $this->get_post_statuses();
		$post_count    = wp_count_posts();
		?>
		<h4><?php _e( 'Select the post statuses from which you want to delete posts', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
		<table class="optiontable">

			<?php foreach ( $post_statuses as $post_status ) : ?>
				<tr>
					<td>
						<input name="smbd_post_status[]" id="smbd_<?php echo esc_attr( $post_status->name ); ?>"
							value="<?php echo esc_attr( $post_status->name ); ?>" type="checkbox">

						<label for="smbd_<?php echo esc_attr( $post_status->name ); ?>">
							<?php echo esc_html( $post_status->label ), ' '; ?>
							<?php if ( property_exists( $post_count, $post_status->name ) ) : ?>
								(<?php echo absint( $post_count->{ $post_status->name } ) . ' ', __( 'Posts', 'bulk-delete' ); ?>)
							<?php endif; ?>
						</label>
					</td>
				</tr>
			<?php endforeach; ?>

			<?php $sticky_post_count = count( get_option( 'sticky_posts' ) ); ?>

			<tr>
				<td>
					<input name="smbd_sticky" id="smbd_sticky" value="on" type="checkbox">
					<label for="smbd_sticky">
						<?php echo __( 'All Sticky Posts', 'bulk-delete' ), ' '; ?>
						(<?php echo absint( $sticky_post_count ), ' ', __( 'Posts', 'bulk-delete' ); ?>)
						<?php echo '<strong>', __( 'Note', 'bulk-delete' ), '</strong>: ', __( 'The date filter will not work for sticky posts', 'bulk-delete' ); ?>
					</label>
				</td>
			</tr>

		</table>

		<table class="optiontable">
			<?php
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
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_status'] = array_map( 'sanitize_text_field', bd_array_get( $request, 'smbd_post_status', array() ) );

		$options['delete-sticky-posts'] = bd_array_get_bool( $request, 'smbd_sticky', false );

		return $options;
	}

	public function delete( $delete_options ) {
		$delete_options = bd_convert_old_options_for_delete_post_by_status( $delete_options );
		$delete_options = apply_filters( 'bd_delete_options', $delete_options );

		$posts_deleted = 0;

		if ( isset( $delete_options['delete-sticky-posts'] ) ) {
			$posts_deleted += $this->delete_sticky_posts( $delete_options['force_delete'] );
		}

		if ( empty( $delete_options['post_status'] ) ) {
			return $posts_deleted;
		}

		$options = array(
			'post_status'  => $delete_options['post_status'],
			'post__not_in' => get_option( 'sticky_posts' ),
		);

		$options = bd_build_query_options( $delete_options, $options );

		$post_ids = bd_query( $options );
		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, $delete_options['force_delete'] );
		}

		$posts_deleted += count( $post_ids );

		return $posts_deleted;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d post with the selected post status', 'Deleted %d posts with the selected post status', $items_deleted, 'bulk-delete' );
	}
}
