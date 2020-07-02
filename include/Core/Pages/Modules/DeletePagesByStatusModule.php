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
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'pages';
		$this->field_slug    = 'page_status';
		$this->meta_box_slug = 'bd_pages_by_status';
		$this->action        = 'delete_pages_by_status';
		$this->cron_hook     = 'do-bulk-delete-pages-by-status';
		$this->scheduler_url = 'https://bulkwp.com/addons/scheduler-for-deleting-pages-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp';
		$this->messages      = array(
			'box_label'         => __( 'By Page Status', 'bulk-delete' ),
			'scheduled'         => __( 'The selected pages are scheduled for deletion', 'bulk-delete' ),
			'cron_label'        => __( 'Delete Pages By status', 'bulk-delete' ),
			'confirm_deletion'  => __( 'Are you sure you want to delete all the pages from the selected post status?', 'bulk-delete' ),
			'confirm_scheduled' => __( 'Are you sure you want to schedule deletion for all the pages from the selected post status?', 'bulk-delete' ),
			'validation_error'  => __( 'Please select at least one post status from which pages should be deleted', 'bulk-delete' ),
			/* translators: 1 Number of pages deleted */
			'deleted_one'       => __( 'Deleted %d page from the selected post status', 'bulk-delete' ),
			/* translators: 1 Number of pages deleted */
			'deleted_multiple'  => __( 'Deleted %d pages from the selected post status', 'bulk-delete' ),
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function render() {
		?>
		<!-- Pages start-->
		<h4><?php _e( 'Select the post statuses from which you want to delete pages', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<?php $this->render_post_status( 'page' ); ?>
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

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateCheckbox';

		return $js_array;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_status'] = array_map( 'sanitize_text_field', bd_array_get( $request, 'smbd_page_status', array() ) );

		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		if ( empty( $options['post_status'] ) ) {
			return array();
		}

		$query = array(
			'post_type'   => 'page',
			'post_status' => $options['post_status'],
		);

		return $query;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function get_non_standard_input_key_map() {
		$prefix = $this->get_ui_input_prefix();

		$prefix_without_underscore_at_end = substr( $prefix, 0, -1 );

		return array(
			$prefix_without_underscore_at_end => $prefix . 'status',
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function prepare_cli_input( $input ) {
		// Handle multiple post statuses.
		$input['status'] = explode( ',', $input['status'] );

		return parent::prepare_cli_input( $input );
	}
}
