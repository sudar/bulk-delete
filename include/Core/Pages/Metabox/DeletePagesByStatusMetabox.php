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
//		$this->field_slug    = 'u_meta';
		$this->meta_box_slug = 'bd_pages_by_status';
		$this->action        = 'delete_pages_by_status';
		$this->cron_hook     = 'do-bulk-delete-pages-by-status';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-pages-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-u-ma';
		$this->messages      = array(
			'box_label'      => __( 'By Page Status', 'bulk-delete' ),
			'scheduled'      => __( 'The selected pages are scheduled for deletion', 'bulk-delete' ),
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
				bd_render_filtering_table_header();
				bd_render_restrict_settings( 'pages', 'pages' );
				bd_render_delete_settings( 'pages' );
				bd_render_limit_settings( 'pages' );
				bd_render_cron_settings( 'pages','http://bulkwp.com/addons/scheduler-for-deleting-pages-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp' );
				?>
			</table>
		</fieldset>
		<?php
		$this->render_submit_button();
	}

	protected function convert_user_input_to_options( $request ) {
		$delete_options = array();

		$delete_options['restrict']     = bd_array_get_bool( $request, 'smbd_pages_restrict', false );
		$delete_options['limit_to']     = absint( bd_array_get( $request, 'smbd_pages_limit_to', 0 ) );
		$delete_options['force_delete'] = bd_array_get_bool( $request, 'smbd_pages_force_delete', false );

		$delete_options['date_op'] = bd_array_get( $request, 'smbd_pages_op' );
		$delete_options['days']    = absint( bd_array_get( $request, 'smbd_pages_days' ) );

		$delete_options['publish'] = bd_array_get( $request, 'smbd_published_pages' );
		$delete_options['drafts']  = bd_array_get( $request, 'smbd_draft_pages' );
		$delete_options['pending'] = bd_array_get( $request, 'smbd_pending_pages' );
		$delete_options['future']  = bd_array_get( $request, 'smbd_future_pages' );
		$delete_options['private'] = bd_array_get( $request, 'smbd_private_pages' );

		return $delete_options;
	}

	public function delete( $delete_options ) {
		global $wp_query;

		// Backward compatibility code. Will be removed in Bulk Delete v6.0
		if ( array_key_exists( 'page_op', $delete_options ) ) {
			$delete_options['date_op'] = $delete_options['page_op'];
			$delete_options['days']    = $delete_options['page_days'];
		}
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
