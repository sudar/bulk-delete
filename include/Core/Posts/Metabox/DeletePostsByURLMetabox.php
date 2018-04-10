<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by URL Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByURLMetabox extends PostsMetabox {
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'specific';
		$this->meta_box_slug = 'bd_posts_by_url';
		$this->action        = 'delete_posts_by_url';
		$this->messages      = array(
			'box_label' => __( 'By URL', 'bulk-delete' ),
		);
	}

	public function render() { ?>
		<!-- URLs start-->
        <h4><?php _e( 'Delete posts and pages that have the following Permalink', 'bulk-delete' ); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
            <tr>
                <td scope="row" colspan="2">
                    <label for="smdb_specific_pages"><?php _e( 'Enter one post url (not post ids) per line', 'bulk-delete' ); ?></label>
                    <br>
                    <textarea style="width: 450px; height: 80px;" id="smdb_specific_pages_urls" name="smdb_specific_pages_urls" rows="5" columns="80"></textarea>
                </td>
            </tr>

			<?php $this->render_filtering_table_header(); ?>
			<?php $this->render_delete_settings(); ?>

        </table>
        </fieldset>
<?php
		$this->render_submit_button( 'delete_posts_by_url' );
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['force_delete'] = bd_array_get_bool( $request, 'smbd_specific_force_delete', false );

		$options['urls'] = preg_split( '/\r\n|\r|\n/', bd_array_get( $request, 'smdb_specific_pages_urls' ) );

		return $options;
	}

	public function delete( $delete_options ) {
		foreach ( $delete_options['urls'] as $url ) {
			$checkedurl = $url;
			if ( substr( $checkedurl , 0, 1 ) == '/' ) {
				$checkedurl = get_site_url() . $checkedurl ;
			}
			$postid = url_to_postid( $checkedurl );
			wp_delete_post( $postid, $delete_options['force_delete'] );
		}

		$deleted_count = count( $delete_options['urls'] );

		return $deleted_count;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d post with the selected post status', 'Deleted %d posts with the selected post status', $items_deleted, 'bulk-delete' );
	}
}
