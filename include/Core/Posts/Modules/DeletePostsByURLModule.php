<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\BulkDelete\Core\Posts\PostsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by URL Module.
 *
 * @since 6.0.0
 */
class DeletePostsByURLModule extends PostsModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'specific';
		$this->meta_box_slug = 'bd_posts_by_url';
		$this->action        = 'delete_posts_by_url';
		$this->messages      = array(
			'box_label'        => __( 'By URL', 'bulk-delete' ),
			'confirm_deletion' => __( 'Are you sure you want to delete all the posts based on the entered url?', 'bulk-delete' ),
			'validation_error' => __( 'Please enter at least one post url', 'bulk-delete' ),
			/* translators: 1 Number of posts deleted */
			'deleted_one'      => __( 'Deleted %d post that had the entered URL(s)', 'bulk-delete' ),
			/* translators: 1 Number of posts deleted */
			'deleted_multiple' => __( 'Deleted %d posts that had the entered URL(s)', 'bulk-delete' ),
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function render() { ?>
		<!-- URLs start-->
		<h4><?php _e( 'Delete posts and pages that have the following Permalink', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<tr>
					<td scope="row" colspan="2">
						<label for="smdb_specific_pages"><?php _e( 'Enter one post url (not post ids) per line', 'bulk-delete' ); ?></label>
						<br>
						<textarea id="smdb_specific_pages_urls" name="smdb_specific_pages_urls" rows="5" columns="80" class="validate"></textarea>
					</td>
				</tr>

				<?php $this->render_filtering_table_header(); ?>
				<?php $this->render_delete_settings(); ?>

			</table>
		</fieldset>
		<?php
		$this->render_submit_button();
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateTextbox';

		return $js_array;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		$options['force_delete'] = bd_array_get_bool( $request, 'smbd_specific_force_delete', false );

		$options['urls'] = preg_split( '/\r\n|\r|\n/', bd_array_get( $request, 'smdb_specific_pages_urls' ) );

		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function do_delete( $delete_options ) {
		$post_ids = array();

		foreach ( $delete_options['urls'] as $url ) {
			if ( substr( $url, 0, 1 ) === '/' ) {
				$url = get_site_url() . $url;
			}

			$post_id = url_to_postid( $url );

			if ( $post_id > 0 ) {
				$post_ids[] = $post_id;
			}
		}

		return $this->delete_posts_by_id( $post_ids, $delete_options['force_delete'] );
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		// Left empty on purpose.
	}
}
