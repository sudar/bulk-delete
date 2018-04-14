<?php

namespace BulkWP\BulkDelete\Core\Base\Mixin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Container of all Fetch related methods.
 *
 * Ideally this should be a Trait. Since Bulk Delete still supports PHP 5.3, this is implemented as a class.
 * Once the minimum requirement is increased to PHP 5.3, this will be changed into a Trait.
 *
 * @since 6.0.0
 */
abstract class Fetcher {
	/**
	 * Get the list of public post types registered in WordPress.
	 *
	 * @return \WP_Post_Type[]
	 */
	protected function get_post_types() {
		return bd_get_post_types();

	}
}
