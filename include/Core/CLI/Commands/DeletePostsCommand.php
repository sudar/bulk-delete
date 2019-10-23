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
	 * Validate Delete Options.
	 *
	 * @param array $options          Delete Options.
	 * @param array $mandatory_fields Mandatory fields list.
	 *
	 * @return boolean True for success and False for failure.
	 */
	public function validate( $options, $mandatory_fields ) {
		foreach ( $mandatory_fields as $field ) {
			if ( empty( $options[ $field ] ) ) {
				\WP_CLI::error( $field . ' can not be empty.' );
				return false;
			}
		}
		return true;
	}

	/**
	 * Get default options.
	 *
	 * @return array $defaults
	 */
	public function get_defaults() {
		$defaults                   = array();
		$defaults['restrict']       = false;
		$defaults['limit_to']       = 0;
		$defaults['exclude_sticky'] = false;
		$defaults['force_delete']   = false;
		return $defaults;
	}

	/**
	 * Delete post by status.
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 * @return void
	 * ## OPTIONS
	 *
	 * [--post_status=<post_status>]
	 * : Post with the entered post status will be deleted.
	 * ---
	 * options:
	 *   - draft
	 *   - publish
	 *   - private
	 *   - any custom post status
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
	 */
	public function by_status( $args, $assoc_args ) {
		$options          = $this->get_defaults();
		$mandatory_fields = array( 'post_status' );
		$this->validate( $assoc_args, $mandatory_fields );
		$status_module          = new DeletePostsByStatusModule();
		$options['post_status'] = $assoc_args['post_status'];

		if ( array_key_exists( 'limit_to', $assoc_args ) ) {
			$options['limit_to'] = $assoc_args['limit_to'];
		}

		if ( array_key_exists( 'restrict', $assoc_args ) ) {
			$options['restrict'] = $assoc_args['restrict'];
		}

		if ( array_key_exists( 'force_delete', $assoc_args ) ) {
			$options['force_delete'] = $assoc_args['force_delete'];
		}

		$count = $status_module->delete( $options );
		\WP_CLI::success( 'Deleted ' . $count . ' posts with ' . $options['post_status'] . ' status' );
	}
}
