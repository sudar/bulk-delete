<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStatusModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByCommentsModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByPostTypeModule;

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
	 * default: 0
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
		$module = new DeletePostsByStatusModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}

	/**
	 * Delete posts by comments.
	 *
	 * ## OPTIONS
	 *
	 * --count_value=<count_value>
	 * : Comments count based on which posts should be deleted. A valid comment count will be greater than or equal to zero.
	 *
	 * [--operator=<operator>]
	 * : Comment count comparision operator.
	 * ---
	 * default: =
	 * options:
	 *   - =
	 *   - !=
	 *   - <
	 *   - >
	 * ---
	 *
	 * [--selected_post_type=<selected_post_type>]
	 * : Post type and status delimited with |
	 * ---
	 * default: post|publish
	 * ---
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of posts to be deleted.
	 * ---
	 * default: 0
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
	 *     # Delete all published posts with 2 comments.
	 *     $ wp bulk-delete posts by-comment --count_value=2
	 *     Success: Deleted 1 post with the selected comments count
	 *
	 *     # Delete all published products(custom post type) with less than 5 comments.
	 *     $ wp bulk-delete posts by-comment --count_value=5 --operator=< --selected_post_type=product|publish
	 *     Success: Deleted 10 post with the selected comments count
	 *
	 *     # Delete all private posts having more than 3 comments.
	 *     $ wp bulk-delete posts by-comment --count_value=3 --operator=> --selected_post_type=post|private
	 *     Success: Deleted 20 post with the selected comments count
	 *
	 * @subcommand by-comment
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_comment( $args, $assoc_args ) {
		$module = new DeletePostsByCommentsModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}

	/**
	 * Delete posts by type.
	 *
	 * ## OPTIONS
	 *
	 * --selected_types=<selected_types>
	 * : Comma seperated list of post type and status delimited with '|'. You can also use any custom post type or status.
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of posts to be deleted.
	 * ---
	 * default: 0
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
	 *     $ wp bulk-delete posts by-post-type --selected_types=post|publish
	 *     Success: Deleted 1 post from the selected post type and post status
	 *
	 *     # Delete all published products(custom post type).
	 *     $ wp bulk-delete posts by-post-type --selected_types=product|publish
	 *     Success: Deleted 10 post from the selected post type and post status
	 *
	 *     # Delete all private posts and products(custom post type).
	 *     $ wp bulk-delete posts by-post-type --selected_types=post|private,product|private
	 *     Success: Deleted 20 post from the selected post type and post status
	 *
	 * @subcommand by-post-type
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_post_type( $args, $assoc_args ) {
		$module = new DeletePostsByPostTypeModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
