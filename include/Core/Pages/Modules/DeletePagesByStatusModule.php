<?php

namespace BulkWP\BulkDelete\Core\Pages\Modules;

use BulkWP\BulkDelete\Core\Pages\PagesModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Pages by Status Module.
 *
 * @since 6.0.0
 */
class DeletePagesByStatusModule extends PagesModule {
	protected function initialize() {
		$this->item_type     = 'pages';
		$this->field_slug    = 'pages';
		$this->meta_box_slug = 'bd_pages_by_status';
		$this->action        = 'delete_pages_by_status';
		$this->cron_hook     = 'do-bulk-delete-pages-by-status';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-pages-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp';
		$this->messages      = array(
			'box_label'  => __( 'By Page Status', 'bulk-delete' ),
			'scheduled'  => __( 'The selected pages are scheduled for deletion', 'bulk-delete' ),
			'cron_label' => __( 'Delete Pages By status', 'bulk-delete' ),
		);
	}

	public function render() {
		$post_statuses = $this->get_post_statuses();
		$pages_count    = wp_count_posts( 'page' );
		?>
		<!-- Pages start-->
		<h4><?php _e( 'Select the status from which you want to delete pages', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<?php foreach ( $post_statuses as $post_status ) : ?>
				<tr>
					<td>
						<input name="smbd_post_status[]" id="smbd_<?php echo esc_attr( $post_status->name ); ?>"
							value="<?php echo esc_attr( $post_status->name ); ?>" type="checkbox">

						<label for="smbd_<?php echo esc_attr( $post_status->name ); ?>">
							<?php echo esc_html( $post_status->label ), ' '; ?>
							<?php if ( property_exists( $pages_count, $post_status->name ) ) : ?>
								(<?php echo absint( $pages_count->{ $post_status->name } ) . ' ', __( 'Posts', 'bulk-delete' ); ?>)
							<?php endif; ?>
						</label>
					</td>
				</tr>
			<?php endforeach; ?>
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

		return $options;
	}

	protected function build_query( $options ) {
		if ( empty( $options['post_status'] ) ) {
			return array();
		}

		$query = array(
			'post_type'   => 'page',
			'post_status'  => $options['post_status'],
		);

		return $query;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d page with the selected page status', 'Deleted %d pages with the selected page status', $items_deleted, 'bulk-delete' );
	}
}
