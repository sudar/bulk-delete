<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByCommentsModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByPostTypeModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByRevisionModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStatusModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStickyPostModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTaxonomyModule;

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
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should posts be permanently deleted. Set to false to move them to trash.
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
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should posts be permanently deleted. Set to false to move them to trash.
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
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should posts be permanently deleted. Set to false to move them to trash.
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
	 *     # Delete all published posts.
	 *     $ wp bulk-delete posts by-post-type --selected_types=post|publish
	 *     Success: Deleted 1 post from the selected post type and post status
	 *
	 *     # Delete all published products(custom post type).
	 *     $ wp bulk-delete posts by-post-type --selected_types=product|publish
	 *     Success: Deleted 10 posts from the selected post type and post status
	 *
	 *     # Delete all private posts and products(custom post type).
	 *     $ wp bulk-delete posts by-post-type --selected_types='post|private,product|private'
	 *     Success: Deleted 20 posts from the selected post type and post status
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

	/**
	 * Delete post revisions.
	 *
	 * ## OPTIONS
	 *
	 * [--revisions=<revisions>]
	 * : Optional parameter which can take only 'revisions' as its value.
	 * ---
	 * default: revisions
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
	 *     # Delete all revisions.
	 *     $ wp bulk-delete posts by-revision
	 *     Success: Deleted 10 post revisions
	 *
	 *     # Delete all revisions.
	 *     $ wp bulk-delete posts by-revision --revisions=revisions
	 *     Success: Deleted 1 post revision
	 *
	 * @subcommand by-revision
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_revision( $args, $assoc_args ) {
		$module = new DeletePostsByRevisionModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}

	/**
	 * Remove sticky or delete sticky posts.
	 *
	 * ## OPTIONS
	 *
	 * [--sticky_action=<sticky_action>]
	 * : Determines whether post has to be made unsticky or deleted.
	 * ---
	 * default: unsticky
	 * options:
	 *   - unsticky
	 *   - delete
	 * ---
	 *
	 * --selected_posts=<selected_posts>
	 * : Comma separated list of post ids or 'all' for selecting all sticky posts.
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should posts be permanently deleted. Set to false to move them to trash.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 *  ## EXAMPLES
	 *
	 *     # Remove sticky for all sticky posts.
	 *     $ wp bulk-delete posts by-sticky --selected_posts=all
	 *     Success: 10 sticky posts were made into normal posts
	 *
	 *     # Delete selected sticky posts.
	 *     $ wp bulk-delete posts by-sticky --selected_posts=1,2,3 --sticky_action=delete --force_delete=true
	 *     Success: Deleted 3 sticky posts
	 *
	 *     # Move to trash all sticky posts.
	 *     $ wp bulk-delete posts by-sticky --selected_posts=all --sticky_action=delete
	 *     Success: Deleted 5 sticky posts
	 *
	 * @subcommand by-sticky
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_sticky( $args, $assoc_args ) {
		$module = new DeletePostsByStickyPostModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}

	/**
	 * Delete post by taxonomy.
	 *
	 * ## OPTIONS
	 *
	 * [--post_type=<post_type>]
	 * : Select a post type from which posts should be deleted. You can also use any custom post type.
	 * ---
	 * default: post
	 * ---
	 *
	 * --taxonomy=<taxonomy>
	 * : Select a taxonomy from which posts should be deleted. You can also use any custom taxonomy.
	 *
	 * --terms=<terms>
	 * : Comma separated list of terms from which posts should be deleted.
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
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--force_delete=<force_delete>]
	 * : Should posts be permanently deleted. Set to false to move them to trash.
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
	 *     # Delete all posts belong to category fruit.
	 *     $ wp bulk-delete posts by-taxonomy --taxonomy=category --terms=fruit
	 *     Success: Deleted 10 posts from the selected taxonomy
	 *
	 *     # Delete all products(custom post type) with product tag(custom taxonomy) skybag.
	 *     $ wp bulk-delete posts by-taxonomy --post_type=product --taxonomy=product_tag --terms=skybag
	 *     Success: Deleted 20 posts from the selected taxonomy
	 *
	 *     # Delete all posts belong to biography or story tags.
	 *     $ wp bulk-delete posts by-taxonomy --taxonomy=post_tag --terms=biography,story
	 *     Success: Deleted 30 posts from the selected taxonomy
	 *
	 * @subcommand by-taxonomy
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_taxonomy( $args, $assoc_args ) {
		$module = new DeletePostsByTaxonomyModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
