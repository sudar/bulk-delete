<?php

namespace BulkWP\BulkDelete\Core\Pages\Metabox;

use BulkWP\BulkDelete\Core\Pages\PagesMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Pages by Status Metabox.
 *
 * @since 6.0.0
 */
class DeletePagesByStatusMetabox extends PagesMetabox {
	protected function initialize() {
		$this->item_type     = 'pages';
		$this->field_slug    = 'pages';
		$this->meta_box_slug = 'bd_pages_by_status';
		$this->action        = 'delete_pages_by_status';
		$this->cron_hook     = 'do-bulk-delete-pages-by-status';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-pages-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp';
		$this->messages      = array(
			'box_label' => __( 'By Page Status', 'bulk-delete' ),
			'scheduled' => __( 'The selected pages are scheduled for deletion', 'bulk-delete' ),
		);
	}

	public function render() {
		$pages_count  = wp_count_posts( 'page' );
		$pages        = $pages_count->publish;
		$page_drafts  = $pages_count->draft;
		$page_future  = $pages_count->future;
		$page_pending = $pages_count->pending;
		$page_private = $pages_count->private;
		?>
		<!-- Pages start-->
		<h4><?php _e( 'Select the status from which you want to delete pages', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<tr>
					<td>
						<input name="smbd_published_pages" value="published_pages" type="checkbox">
						<label for="smbd_published_pages"><?php _e( 'All Published Pages', 'bulk-delete' ); ?> (<?php echo $pages . ' '; _e( 'Pages', 'bulk-delete' ); ?>)</label>
					</td>
				</tr>

				<tr>
					<td>
						<input name="smbd_draft_pages" value="draft_pages" type="checkbox">
						<label for="smbd_draft_pages"><?php _e( 'All Draft Pages', 'bulk-delete' ); ?> (<?php echo $page_drafts . ' '; _e( 'Pages', 'bulk-delete' ); ?>)</label>
					</td>
				</tr>

				<tr>
					<td>
						<input name="smbd_future_pages" value="scheduled_pages" type="checkbox">
						<label for="smbd_future_pages"><?php _e( 'All Scheduled Pages', 'bulk-delete' ); ?> (<?php echo $page_future . ' '; _e( 'Pages', 'bulk-delete' ); ?>)</label>
					</td>
				</tr>

				<tr>
					<td>
						<input name="smbd_pending_pages" value="pending_pages" type="checkbox">
						<label for="smbd_pending_pages"><?php _e( 'All Pending Pages', 'bulk-delete' ); ?> (<?php echo $page_pending . ' '; _e( 'Pages', 'bulk-delete' ); ?>)</label>
					</td>
				</tr>

				<tr>
					<td>
						<input name="smbd_private_pages" value="private_pages" type="checkbox">
						<label for="smbd_private_pages"><?php _e( 'All Private Pages', 'bulk-delete' ); ?> (<?php echo $page_private . ' '; _e( 'Pages', 'bulk-delete' ); ?>)</label>
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
		$options['publish'] = bd_array_get( $request, 'smbd_published_pages' );
		$options['drafts']  = bd_array_get( $request, 'smbd_draft_pages' );
		$options['pending'] = bd_array_get( $request, 'smbd_pending_pages' );
		$options['future']  = bd_array_get( $request, 'smbd_future_pages' );
		$options['private'] = bd_array_get( $request, 'smbd_private_pages' );

		return $options;
	}

	public function delete( $options ) {
		global $wp_query;

		/**
		 * Filter Delete options.
		 *
		 * @param array $options Delete options.
		 */
		$options = apply_filters( 'bd_delete_options', $options );

		$post_status = array();

		// published pages
		if ( 'published_pages' == $options['publish'] ) {
			$post_status[] = 'publish';
		}

		// Drafts
		if ( 'draft_pages' == $options['drafts'] ) {
			$post_status[] = 'draft';
		}

		// Pending pages
		if ( 'pending_pages' == $options['pending'] ) {
			$post_status[] = 'pending';
		}

		// Future pages
		if ( 'future_pages' == $options['future'] ) {
			$post_status[] = 'future';
		}

		// Private pages
		if ( 'private_pages' == $options['private'] ) {
			$post_status[] = 'private';
		}

		$query = array(
			'post_type'   => 'page',
			'post_status' => $post_status,
		);

		$query = bd_build_query_options( $options, $query );
		$pages = $wp_query->query( $query );
		foreach ( $pages as $page ) {
			wp_delete_post( $page->ID, $options['force_delete'] );
		}

		return count( $pages );
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d page with the selected page status', 'Deleted %d pages with the selected page status', $items_deleted, 'bulk-delete' );
	}
}
