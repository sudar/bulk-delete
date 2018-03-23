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
		$this->field_slug    = 'posts';
		$this->meta_box_slug = 'bd_posts_by_status';
		$this->action        = 'delete_posts_by_status';
		$this->cron_hook     = 'do-bulk-delete-posts-by-status';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp';
		$this->messages      = array(
			'box_label' => __( 'By Page Status', 'bulk-delete' ),
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
		$this->render_submit_button( 'delete_posts_by_status' );
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['publish'] = bd_array_get( $request, 'smbd_published_pages' );
		$options['drafts']  = bd_array_get( $request, 'smbd_draft_pages' );
		$options['pending'] = bd_array_get( $request, 'smbd_pending_pages' );
		$options['future']  = bd_array_get( $request, 'smbd_future_pages' );
		$options['private'] = bd_array_get( $request, 'smbd_private_pages' );

		return $options;
	}

	public function delete( $delete_options ) {
		global $wp_query;

		/**
		 * Filter Delete options.
		 *
		 * @param array $delete_options Delete options.
		 */
		$delete_options = apply_filters( 'bd_delete_options', $delete_options );

		$post_status = array();

		// published pages
		if ( 'published_pages' == $delete_options['publish'] ) {
			$post_status[] = 'publish';
		}

		// Drafts
		if ( 'draft_pages' == $delete_options['drafts'] ) {
			$post_status[] = 'draft';
		}

		// Pending pages
		if ( 'pending_pages' == $delete_options['pending'] ) {
			$post_status[] = 'pending';
		}

		// Future pages
		if ( 'future_pages' == $delete_options['future'] ) {
			$post_status[] = 'future';
		}

		// Private pages
		if ( 'private_pages' == $delete_options['private'] ) {
			$post_status[] = 'private';
		}

		$options = array(
			'post_type'   => 'page',
			'post_status' => $post_status,
		);

		$options = bd_build_query_options( $delete_options, $options );
		$pages   = $wp_query->query( $options );
		foreach ( $pages as $page ) {
			wp_delete_post( $page->ID, $delete_options['force_delete'] );
		}

		return count( $pages );
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d page with the selected page status', 'Deleted %d pages with the selected page status', $items_deleted, 'bulk-delete' );
	}
}
