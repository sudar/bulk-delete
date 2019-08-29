<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\BulkDelete\Core\Posts\PostsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Category Module. This class is kept to run the old cron jobs.
 *
 * @since 6.0.0
 * @since 6.1.0 Deprecated.
 */
class DeletePostsByCategoryModule extends PostsModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->cron_hook = 'do-bulk-delete-cat';
		$this->messages  = array(
			'cron_label' => __( 'Delete Post By Category', 'bulk-delete' ),
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function render() {
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
	}

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
