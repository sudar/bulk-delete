<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Category Module. This class is kept to run the old cron jobs.
 *
 * @since 6.0.0
 * @since 6.1.0 Deprecated.
 */
class DeletePostsByCategoryModule extends DeletePostsByTaxonomyModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		$query = array();
		if ( in_array( 'all', $options['selected_cats'], true ) ) {
			$query['category__not__in'] = array( 0 );
		} else {
			$query['category__in'] = $options['selected_cats'];
		}

		return $query;
	}
}
