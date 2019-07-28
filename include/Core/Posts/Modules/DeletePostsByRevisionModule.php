<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\BulkDelete\Core\Posts\PostsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Revision Module.
 *
 * @since 6.0.0
 */
class DeletePostsByRevisionModule extends PostsModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'revisions';
		$this->meta_box_slug = 'bd_posts_by_revision';
		$this->action        = 'delete_posts_by_revision';
		$this->messages      = array(
			'box_label'        => __( 'By Post Revisions', 'bulk-delete' ),
			'confirm_deletion' => __( 'Are you sure you want to delete all the revisions based on the selected condition?', 'bulk-delete' ),
			'validation_error' => __( 'Please select the checkbox', 'bulk-delete' ),
			/* translators: 1 Number of posts deleted */
			'deleted_one'      => __( 'Deleted %d post revision', 'bulk-delete' ),
			/* translators: 1 Number of posts deleted */
			'deleted_multiple' => __( 'Deleted %d post revisions', 'bulk-delete' ),
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function render() {
		global $wpdb;
		$revisions = $wpdb->get_var( "select count(*) from $wpdb->posts where post_type = 'revision'" );
		?>
		<!-- Post Revisions start-->
		<h4><?php _e( 'Select the posts which you want to delete', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>" value="revisions" type="checkbox" class="validate">
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>"><?php _e( 'All Revisions', 'bulk-delete' ); ?> (<?php echo $revisions . ' '; _e( 'Revisions', 'bulk-delete' ); ?>)</label>
					</td>
				</tr>
			</table>
			<table class="optiontable">
				<?php
				$this->render_filtering_table_header();
				$this->render_restrict_settings();
				$this->render_limit_settings( 'revisions' );
				?>
			</table>
		</fieldset>
		<?php
			$this->render_submit_button();
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		$options['revisions']    = bd_array_get( $request, 'smbd_' . $this->field_slug );
		$options['force_delete'] = true;

		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateCheckbox';

		return $js_array;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		if ( 'revisions' === $options['revisions'] ) {
			return array(
				'post_type'   => 'revision',
				'post_status' => 'inherit',
			);
		}
	}
}
