<?php

namespace BulkWP\BulkDelete\Core\SystemInfo;

use Sudar\WPSystemInfo\SystemInfo;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Email Log System Info.
 *
 * Uses the WPSystemInfo library.
 *
 * @since 2.3.0
 * @link https://github.com/sudar/wp-system-info
 */
class BulkDeleteSystemInfo extends SystemInfo {

	/**
	 * Setup hooks and filters.
	 */
	public function load() {
		add_action( 'before_system_info_for_bulk-delete', array( $this, 'print_bulk_delete_details' ) );
	}

	/**
	 * Print details about Bulk Delete.
	 *
	 * PHPCS is disabled for this function since alignment will mess up the system info output.
	 * phpcs:disable
	 */
	public function print_bulk_delete_details() {
	?>
-- Bulk Delete Configuration --
Bulk Delete Version: <?php echo Bulk_Delete::VERSION . "\n"; ?>

<?php
	}
	// phpcs:enable

	/**
	 * Change the default config.
	 *
	 * @return array Modified config.
	 */
	protected function get_default_config() {
		$config = parent::get_default_config();

		$config['show_posts']      = false;
		$config['show_taxonomies'] = false;
		$config['show_users']      = false;

		return $config;
	}
}
