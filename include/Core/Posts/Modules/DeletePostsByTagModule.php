<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTaxonomyModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Tag Module.
 *
 * @since 6.0.0
 * @since 6.1.0 Deprecated. This class is kept to run the old cron jobs.
 */
class DeletePostsByTagModule extends DeletePostsByTaxonomyModule {
	/**
	 * Builds Query.
	 *
	 * @param array $options Delete Options.
	 */
	protected function build_query( $options ) {
		$query = array();

		if ( in_array( 'all', $options['selected_tags'], true ) ) {
			$query['tag__not__in'] = array( 0 );
		} else {
			$query['tag__in'] = $options['selected_tags'];
		}

		return $query;
	}
}
