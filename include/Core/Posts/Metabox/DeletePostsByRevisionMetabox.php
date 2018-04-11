<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Status Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByRevisionMetabox extends PostsMetabox {
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'revisions';
		$this->meta_box_slug = 'bd_posts_by_revision';
		$this->action        = 'delete_posts_by_revision';
		$this->messages      = array(
			'box_label' => __( 'By Post Revisions', 'bulk-delete' ),
		);
	}

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
                    <input name="smbd_revisions" id ="smbd_revisions" value="revisions" type="checkbox">
                    <label for="smbd_revisions"><?php _e( 'All Revisions', 'bulk-delete' ); ?> (<?php echo $revisions . ' '; _e( 'Revisions', 'bulk-delete' ); ?>)</label>
                </td>
            </tr>

        </table>
        </fieldset>
<?php
		$this->render_submit_button( 'delete_posts_by_revision' );
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options = array( 'revisions' => bd_array_get( $request, 'smbd_revisions' ) );

		return $options;
	}

	public function delete( $delete_options ) {
		$deleted_count = $this->delete_posts_by_revision( $delete_options );

		return $deleted_count;
	}

	/**
	 * Delete all post revisions.
	 *
	 * @since 5.0
	 * @static
	 *
	 * @param array $delete_options
	 *
	 * @return int The number of posts that were deleted
	 */
	public static function delete_posts_by_revision( $delete_options ) {
		global $wpdb;

		// Revisions
		if ( 'revisions' == $delete_options['revisions'] ) {
			$revisions = $wpdb->get_results( "select ID from $wpdb->posts where post_type = 'revision'" );

			foreach ( $revisions as $revision ) {
				wp_delete_post( $revision->ID );
			}

			return count( $revisions );
		}

		return 0;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d post with the selected post status', 'Deleted %d posts with the selected post status', $items_deleted, 'bulk-delete' );
	}
}
