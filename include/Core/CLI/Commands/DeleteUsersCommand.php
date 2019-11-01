<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Users CLI Command.
 *
 * @since 6.1.0
 */
class DeleteUsersCommand extends BaseCommand {
	/**
	 * Get the command.
	 *
	 * @return string Command name.
	 */
	public static function get_command() {
		return 'users';
	}

	/**
	 * Delete user by user meta.
	 *
	 * ## OPTIONS
	 *
	 * --key=<key>
	 * : User meta key.
	 *
	 * --value=<value>
	 * : User meta value.
	 *
	 * [--compare=<compare>]
	 * : Comparison operator. Use STARTS WITH, ENDS WITH, NOT LIKE operators enclosed in single quotes.
	 * ---
	 * default: =
	 * options:
	 *   - =
	 *   - !=
	 *   - <
	 *   - <=
	 *   - >
	 *   - >=
	 *   - LIKE
	 *   - NOT LIKE
	 *   - STARTS WITH
	 *   - ENDS WITH
	 * ---
	 *
	 * [--registered_restrict=<registered_restrict>]
	 * : Restricts users deletion with registration date filter.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--op=<op>]
	 * : Can be used only when --registered_restrict=true. Restricts users deletion with registered date older than(before) or in the last(after) filter.
	 * ---
	 * default: before
	 * options:
	 *   - before
	 *   - after
	 * ---
	 *
	 * [--registered_days=<registered_days>]
	 * : Restricts users deletion with registration date filter.
	 *
	 * [--no_posts=<no_posts>]
	 * : Restrict to users who don't have any posts.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--no_post_post_types=<no_post_post_types>]
	 * : Can be used only when --no_posts=true.  Restrict to users who don't have any posts of the comma separated list of post types. You can use built in as well as custom post types.
	 * ---
	 * default: post
	 * ---
	 *
	 * [--limit_to=<limit_to>]
	 * : Limits the number of users to be deleted.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--post_reassign=<post_reassign>]
	 * : Whether reassign the posts of users that are going to be deleted.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--<field>=<value>]
	 * : Additional associative args for the deletion.
	 *
	 *  ## EXAMPLES
	 *
	 *     # Delete all users and their posts with user meta key status and value spam.
	 *     $ wp bulk-delete users by-user-meta --key=status --value=spam
	 *     Success: Deleted 10 users with the selected user meta
	 *
	 *     # Delete all users and their posts with user meta key status and value spam who are registered with in last 10 days.
	 *     $ wp bulk-delete users by-user-meta --key=status --value=spam --registered_restrict=true --op=after --registered_days=10
	 *     Success: Deleted 5 users with the selected user meta
	 *
	 *     # Delete users with user meta key status and value spam, and reassign posts to another user.
	 *     $ wp bulk-delete users by-user-meta --key=status --value=spam  --post_reassign=true --reassign_user_id=243
	 *     Success: Deleted 3 users with the selected user meta
	 *
	 *     # Delete users with user meta key status and value spam where user does not have any post/product(custom post type) created.
	 *     $ wp bulk-delete users by-user-meta --key=status --value=spam  --no_posts=true --no_posts_post_types=post,product
	 *     Success: Deleted 3 users with the selected user meta
	 *
	 * @subcommand by-user-meta
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_user_meta( $args, $assoc_args ) {
		$module = new DeleteUsersByUserMetaModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
