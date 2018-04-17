<?php

namespace BulkWP\BulkDelete\Core\Metas;

use BulkWP\BulkDelete\Core\Base\BaseModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Module for Deleting Meta fields.
 *
 * @since 6.0.0
 */
abstract class MetasModule extends BaseModule {
	protected $item_type = 'metas';

	protected function render_restrict_settings() {
		bd_render_restrict_settings( $this->field_slug, 'posts' );
	}
}
