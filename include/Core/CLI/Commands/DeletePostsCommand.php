<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\SystemInfo\BulkDeleteSystemInfo;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts CLI Command.
 *
 * @since 6.1.0
 */
class DeletePostsCommand extends BaseCommand {
	/**
	 * Get the command.
	 *
	 * @return string Command name.
	 */
	public static function get_command() {
		return 'posts';
	}

	/**
	 * Delete post by status.
	 *
	 * @subcommand by-status
	 */
	public function by_status() {
		\WP_CLI::line( 'This command will delete posts by status' );
	}
}
