<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\BulkDelete\Core\Posts\PostsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Tag Module.
 *
 * @since 6.0.0
 * @since 6.1.0 Deprecated. This class is kept to run the old cron jobs.
 */
class DeletePostsByTagModule extends PostsModule {
	/**
	 * Base parameters setup.
	 */
	protected function initialize() {
		$this->cron_hook     = 'do-bulk-delete-tag';
		$this->scheduler_url = 'https://bulkwp.com/addons/scheduler-for-deleting-posts-by-tag/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-st';
		$this->messages      = array(
			'cron_label' => __( 'Delete Post By Tag', 'bulk-delete' ),
		);
	}

	/**
	 * Render Delete posts by tag box.
	 */
	public function render() {
	}

	/**
	 * Process delete posts user inputs by tag.
	 *
	 * @param array $request Request array.
	 * @param array $options Options for deleting posts.
	 */
	protected function convert_user_input_to_options( $request, $options ) {
	}

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
