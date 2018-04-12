<?php

namespace BulkWP\BulkDelete\Core\Pages;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Metabox for deleting pages.
 *
 * This class extends PostsMetabox since Page is a type of Post.
 *
 * @since 6.0.0
 */
abstract class PagesMetabox extends PostsMetabox {
	protected $item_type = 'pages';

	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][] = '_pages';

		return $js_array;
	}
}
