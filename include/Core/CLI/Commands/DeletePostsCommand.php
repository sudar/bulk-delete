<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStatusModule;

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
	 * ## OPTIONS
	 *
	 * [--post_status=<post_status>]
	 * : Comma seperated list of post status from which posts should be deleted. You can also use any custom post status.
	 * ---
	 * options:
	 *   - publish (default)
	 *   - draft,publish
	 *   - draft,publish,private
	 *   - private
	 * ---
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of posts to be deleted.
	 *
	 * [--restrict=<restrict>]
	 * : Restricts posts deletion with post date filter.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : True for permanent deletion and false for moving to trash.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * @subcommand by-status
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_status( $args, $assoc_args ) {
		$module = new DeletePostsByStatusModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
