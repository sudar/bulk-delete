<?php

namespace BulkWP\BulkDelete\Core\Comments\Modules;

use BulkWP\BulkDelete\Core\Comments\CommentsModule;
use BulkWP\BulkDelete\Core\Comments\QueryOverriders\DeleteCommentsQueryOverrider;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Comments by Author Module.
 *
 * @since 6.1.0
 */
class DeleteCommentsByIPModule extends CommentsModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'comments';
		$this->field_slug    = 'comments_by_ip';
		$this->meta_box_slug = 'bd_comments_by_ip';
		$this->action        = 'delete_comments_by_ip';
		$this->cron_hook     = 'do-bulk-delete-comments-by-ip';
		$this->scheduler_url = 'https://bulkwp.com/addons/scheduler-for-deleting-comments/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp';
		$this->messages      = array(
			'box_label'         => __( 'Delete Comments by IP Address', 'bulk-delete' ),
			'scheduled'         => __( 'The selected comments are scheduled for deletion', 'bulk-delete' ),
			'cron_label'        => __( 'Delete Comments By IP Address', 'bulk-delete' ),
			'confirm_deletion'  => __( 'Are you sure you want to delete all the comments from the selected condition?', 'bulk-delete' ),
			'confirm_scheduled' => __( 'Are you sure you want to schedule deletion for all the comments from the selected condition?', 'bulk-delete' ),
			'validation_error'  => __( 'Please enter valid IP Address based on which comments should be deleted', 'bulk-delete' ),
			/* translators: 1 Number of comments deleted */
			'deleted_one'       => __( 'Deleted %d comment with selected condition', 'bulk-delete' ),
			/* translators: 1 Number of comments deleted */
			'deleted_multiple'  => __( 'Deleted %d comments with selected condition', 'bulk-delete' ),
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function render() {
		?>
		<!-- Pages start-->
		<h4><?php _e( 'Enter IP Address based on which you want to delete comments', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_address" type="text" placeholder="<?php _e( 'IP Address', 'bulk-delete' ); ?>" class="validate">
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

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateTextbox';

		return $js_array;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		$options['ip_address'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_address', '' ) );

		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		$query = array();

		if ( ! empty( $options['ip_address'] ) ) {
			$query['bd_db_column_name']  = 'comment_author_IP';
			$query['bd_db_column_value'] = $options['ip_address'];
			$query_overrider             = new DeleteCommentsQueryOverrider();
			$query_overrider->load();
		}

		$date_query = $this->get_date_query( $options );
		if ( ! empty( $date_query ) ) {
			$query['date_query'] = $date_query;
		}

		return $query;
	}
}
