<?php

namespace BulkWP\BulkDelete\Core\Metas;

use BulkWP\BulkDelete\Core\Base\BaseMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Metabox for Deleting Meta fields.
 *
 * @since 6.0.0
 */
abstract class MetasMetabox extends BaseMetabox {

	protected $item_type = 'metas';

	protected function render_restrict_settings() {
		bd_render_restrict_settings( $this->field_slug, 'posts' );
	}
}
