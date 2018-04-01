<?php
namespace BulkWP\BulkDelete\Core\Pages;

use BulkWP\BulkDelete\Core\Base\BaseMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Metabox for deleting pages.
 *
 * @since 6.0.0
 */
abstract class PagesMetabox extends BaseMetabox {
	protected $item_type = 'pages';

	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][] = '_pages';

		return $js_array;
	}
}
