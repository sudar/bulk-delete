<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\Pages\Modules\DeletePagesByStatusModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Meta CLI Command.
 *
 * @since 6.1.0
 */
class DeletePagesCommand extends BaseCommand {
	/**
	 * Get the command.
	 *
	 * @return string Command name.
	 */
	public static function get_command() {
		return 'pages';
	}

	/**
	 * Delete pages by status.
	 *
	 * ## OPTIONS
	 *
	 * --status=<status>
	 * : Comma separeated list of post status from which pages need to be deleted. You can also use any custom post status.
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
	 * [--op=<op>]
	 * : Can be used only when --restrict=true. Restricts pages deletion with older than(before) or in the last(after) filter.
	 * ---
	 * options:
	 *   - before
	 *   - after
	 * ---
	 *
	 * [--days=<days>]
	 * : Can be used only when --restrict=true.  Restricts pages deletion with creation date.
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of pages to be deleted.
	 * ---
	 * default: 0
	 * ---
	 *
	 *
	 * [--force_delete=<force_delete>]
	 * : Should pages be permanently deleted. Set to false to move them to trash.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 *  ## EXAMPLES
	 *
	 *     # Delete all draft and publish pages.
	 *     $ wp bulk-delete pages by-status --status=publish,draft
	 *     Success: Deleted 10 pages from the selected post status
	 *
	 *     # Delete published pages that are older than 10 days.
	 *     $ wp bulk-delete pages by-status --status=publish --restrict=true --op=before --days=10
	 *     Success: Deleted 10 pages from the selected post status
	 *
	 *     # Delete published and draft pages that are created in the last 5 days.
	 *     $ wp bulk-delete pages by-status --status=publish,draft --restrict=true --op=after --days=5
	 *     Success: Deleted 10 pages from the selected post status
	 *
	 *     # Move 500 published pages to trash.
	 *     $ wp bulk-delete pages by-status --status=publish --limit_to=500
	 *     Success: Deleted 500 pages from the selected post status
	 *
	 *     # Permanently delete 500 published pages.
	 *     $ wp bulk-delete pages by-status --status=publish --limit_to=500 --force_delete=true
	 *     Success: Deleted 500 pages from the selected post status
	 *
	 * @subcommand by-status
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_status( $args, $assoc_args ) {
		$module = new DeletePagesByStatusModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
