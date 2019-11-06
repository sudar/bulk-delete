<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\Comments\Modules\DeleteCommentsByAuthorModule;
use BulkWP\BulkDelete\Core\Comments\Modules\DeleteCommentsByIPModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Comments CLI Command.
 *
 * @since 6.1.0
 */
class DeleteCommentsCommand extends BaseCommand {
	/**
	 * Get the command.
	 *
	 * @return string Command name.
	 */
	public static function get_command() {
		return 'comments';
	}

	/**
	 * Delete comments by author details.
	 *
	 * ## OPTIONS
	 *
	 * --details=<details>
	 * : Comment author details based on which comments should be deleted. You can either enter the name, email or the url of the comment author.
	 *
	 * [--restrict=<restrict>]
	 * : Restricts comments deletion with comment date filter.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--op=<op>]
	 * : Can be used only when --restrict=true. Restricts comments deletion with older than(before) or in the last(after) filter.
	 * ---
	 * options:
	 *   - before
	 *   - after
	 * ---
	 *
	 * [--days=<days>]
	 * : Can be used only when --restrict=true.  Restricts comments deletion with creation date.
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of comments to be deleted.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should comments be permanently deleted. Set to false to move them to trash.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 *  ## EXAMPLES
	 *
	 *     # Delete all comments with author name apple.
	 *     $ wp bulk-delete comments by-author --details=apple
	 *     Success: Deleted 10 comments with selected condition
	 *
	 *     # Delete all comments with author email apple@apple.com and older than 10 days.
	 *     $ wp bulk-delete comments by-author --details=apple@apple.com --restrict=true --op=before --days=10
	 *     Success: Deleted 5 comments with selected condition
	 *
	 *     # Delete all comments with author url www.apple.com that are created in the last 5 days.
	 *     $ wp bulk-delete comments by-author --details=www.apple.com --restrict=true --op=after --days=5
	 *     Success: Deleted 5 comments with selected condition
	 *
	 *     # Move 500 comments to trash.
	 *     $ wp bulk-delete comments by-author --details=www.apple.com --limit_to=500
	 *     Success: Deleted 500 comments with selected condition
	 *
	 *     # Permanently delete 500 comments by author apple.
	 *     $ wp bulk-delete comments by-author --details=apple --limit_to=500 --force_delete=true
	 *     Success: Deleted 500 comments with selected condition
	 *
	 * @subcommand by-author
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_author( $args, $assoc_args ) {
		$module = new DeleteCommentsByAuthorModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}

	/**
	 * Delete comments by ip address.
	 *
	 * ## OPTIONS
	 *
	 * --address=<address>
	 * : IP Address based on which comments should be deleted.
	 *
	 * [--restrict=<restrict>]
	 * : Restricts comments deletion with comment date filter.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--op=<op>]
	 * : Can be used only when --restrict=true. Restricts comments deletion with older than(before) or in the last(after) filter.
	 * ---
	 * options:
	 *   - before
	 *   - after
	 * ---
	 *
	 * [--days=<days>]
	 * : Can be used only when --restrict=true.  Restricts comments deletion with creation date.
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of comments to be deleted.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should comments be permanently deleted. Set to false to move them to trash.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 *  ## EXAMPLES
	 *
	 *     # Delete all comments with ip address 127.0.0.1.
	 *     $ wp bulk-delete comments by-ip --address=127.0.0.1
	 *     Success: Deleted 10 comments with selected condition
	 *
	 *     # Delete all comments with ip address 127.0.0.1 and older than 10 days.
	 *     $ wp bulk-delete comments by-ip --address=127.0.0.1 --restrict=true --op=before --days=10
	 *     Success: Deleted 5 comments with selected condition
	 *
	 *     # Delete all comments with ip address 127.0.0.1 that are created in the last 5 days.
	 *     $ wp bulk-delete comments by-ip --address=127.0.0.1 --restrict=true --op=after --days=5
	 *     Success: Deleted 5 comments with selected condition
	 *
	 *     # Move 500 comments to trash with ip address 127.0.0.1.
	 *     $ wp bulk-delete comments by-ip --address=127.0.0.1 --limit_to=500
	 *     Success: Deleted 500 comments with selected condition
	 *
	 *     # Permanently delete 500 comments with ip address 127.0.0.1.
	 *     $ wp bulk-delete comments by-ip --address=127.0.0.1 --limit_to=500 --force_delete=true
	 *     Success: Deleted 500 comments with selected condition
	 *
	 * @subcommand by-ip
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_ip( $args, $assoc_args ) {
		$module = new DeleteCommentsByIPModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
