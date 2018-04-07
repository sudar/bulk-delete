<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Status Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByPostTypeMetabox extends PostsMetabox {
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'post_status';
		$this->meta_box_slug = 'bd_posts_by_status';
		$this->action        = 'delete_posts_by_status';
		$this->cron_hook     = 'do-bulk-delete-post-by-status';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-status/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sps';
		$this->messages      = array(
			'box_label' => __( 'By Post Status', 'bulk-delete' ),
			'scheduled' => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
		);
	}

	public function render() {}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_status'] = array_map( 'sanitize_text_field', bd_array_get( $request, 'smbd_post_status', array() ) );

		$options['delete-sticky-posts'] = bd_array_get_bool( $request, 'smbd_sticky', false );

		return $options;
	}

	public function delete( $delete_options ) {}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d post with the selected post status', 'Deleted %d posts with the selected post status', $items_deleted, 'bulk-delete' );
	}
}
