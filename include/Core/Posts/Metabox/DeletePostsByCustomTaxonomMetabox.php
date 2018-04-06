<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Custom Taxonom Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByCustomTaxonomMetabox extends PostsMetabox {
	protected function initialize() {
	}

	public function render() {
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_status'] = array_map( 'sanitize_text_field', bd_array_get( $request, 'smbd_post_status', array() ) );

		$options['delete-sticky-posts'] = bd_array_get_bool( $request, 'smbd_sticky', false );

		return $options;
	}

	public function delete( $delete_options ) {
	}

	protected function get_success_message( $items_deleted ) {
	}
}
