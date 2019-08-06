<?php

namespace BulkWP\BulkDelete\Core\Comments\Modules;

use BulkWP\BulkDelete\Core\Comments\CommentsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Comments by Author Module.
 *
 * @since 6.1.0
 */
class DeleteCommentsByAuthorModule extends CommentsModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'comments';
		$this->field_slug    = 'comments_by_author';
		$this->meta_box_slug = 'bd_comments_by_author';
		$this->action        = 'delete_comments_by_author';
		$this->cron_hook     = 'do-bulk-delete-comments-by-author';
		$this->scheduler_url = 'https://bulkwp.com/addons/scheduler-for-deleting-comments-by-author/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sp';
		$this->messages      = array(
			'box_label'         => __( 'By Author', 'bulk-delete' ),
			'scheduled'         => __( 'The selected comments are scheduled for deletion', 'bulk-delete' ),
			'cron_label'        => __( 'Delete Comments By author', 'bulk-delete' ),
			'confirm_deletion'  => __( 'Are you sure you want to delete all the comments from the selected condition?', 'bulk-delete' ),
			'confirm_scheduled' => __( 'Are you sure you want to schedule deletion for all the comments from the selected condition?', 'bulk-delete' ),
			'validation_error'  => __( 'Please enter author name or email or URL based on which comments should be deleted', 'bulk-delete' ),
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
		<h4><?php _e( 'Enter Author name or email or URL whose comments you want to delete', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_name" type="text" placeholder="<?php _e( 'Author Name', 'bulk-delete' ); ?>">
					</td>
					<td> <?php _e( 'Or', 'bulk-delete' ); ?></td>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_email" type="text" placeholder="<?php _e( 'Author Email', 'bulk-delete' ); ?>">
					</td>
					<td> <?php _e( 'Or', 'bulk-delete' ); ?></td>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_url" type="text" placeholder="<?php _e( 'Author URL', 'bulk-delete' ); ?>">
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
		$js_array['validators'][ $this->action ] = 'noValidation';

		return $js_array;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		$options['author_name']  = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_name', '' ) );
		$options['author_email'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_email', '' ) );
		$options['author_url']   = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_url', '' ) );

		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		$query = array();
		if ( ! empty( $options['author_name'] ) ) {
			// TODO: to be decided.
			$query['author__in'] = array();
		}
		if ( ! empty( $options['author_email'] ) ) {
			$query['author_email'] = $options['author_email'];
		}
		if ( ! empty( $options['author_url'] ) ) {
			$query['author_url'] = $options['author_url'];
		}
		$date_query = $this->get_date_query( $options );

		if ( ! empty( $date_query ) ) {
			$query['date_query'] = $date_query;
		}
		error_log( var_export( $query, true ) );
		return $query;
	}
}
