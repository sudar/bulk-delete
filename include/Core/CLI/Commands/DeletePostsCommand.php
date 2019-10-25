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
	 * default: publish
	 * ---
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of posts to be deleted.
	 * ---
	 * default: -1
	 * ---
	 *
	 * [--restrict=<restrict>]
	 * : Restricts posts deletion with post date filter.
	 * ---
	 * default: false
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should posts be permanently deleted. Set to false to move them to trash.
	 * ---
	 * default: false
	 * ---
	 *
	 * [--<field>=<value>]
	 * : Additional associative args for the deletion.
	 *
	 *  ## EXAMPLES
	 *
	 *     # Delete all published posts.
	 *     $ wp bulk-delete posts by-status
	 *     Success: Deleted 1 post from the selected post status
	 *
	 *     # Delete all draft posts.
	 *     $ wp bulk-delete posts by-status --post_status=draft
	 *     Success: Deleted 1 post from the selected post status
	 *
	 *     # Delete all published and draft posts.
	 *     $ wp bulk-delete posts by-status --post_status=draft,publish
	 *     Success: Deleted 1 post from the selected post status
	 *
	 * @subcommand by-status
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_status( $args, $assoc_args ) {
		error_log(var_export($assoc_args, true));
		$module = new DeletePostsByStatusModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
