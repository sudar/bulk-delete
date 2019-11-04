<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByNameModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Terms CLI Command.
 *
 * @since 6.1.0
 */
class DeleteTermsCommand extends BaseCommand {
	/**
	 * Get the command.
	 *
	 * @return string Command name.
	 */
	public static function get_command() {
		return 'terms';
	}

	/**
	 * Delete terms by name.
	 *
	 * ## OPTIONS
	 *
	 * [--taxonomy=<taxonomy>]
	 * : Taxonomy the term going to be deleted belongs to. You can use any custom taxonomy.
	 * ---
	 * default: category
	 * ---
	 *
	 * [--operator=<operator>]
	 * : Comparison operator for name to compare with.
	 * ---
	 * default: =
	 * options:
	 *   - =
	 *   - !=
	 *   - LIKE
	 *   - NOT LIKE
	 *   - STARTS_WITH
	 *   - ENDS_WITH
	 * ---
	 *
	 * --value=<value>
	 * : Term name
	 *
	 *  ## EXAMPLES
	 *
	 *     # Delete terms with name fruit under category taxonomy.
	 *     $ wp bulk-delete terms by-name --value=fruit
	 *     Success: Deleted 10 terms with the selected options
	 *
	 *     # Delete terms with name containing apple under product_cat(custom taxonomy) taxonomy.
	 *     $ wp bulk-delete terms by-name --taxonomy=product_cat --operator=LIKE --value=apple
	 *     Success: Deleted 5 terms with the selected options
	 *
	 *     # Delete terms with name ends with apple under post tag taxonomy.
	 *     $ wp bulk-delete terms by-name --taxonomy=post_tag --operator=ENDS_WITH --value=apple
	 *     Success: Deleted 3 terms with the selected options
	 *
	 * @subcommand by-name
	 *
	 * @param array $args       Arguments to be supplied.
	 * @param array $assoc_args Associative arguments to be supplied.
	 *
	 * @return void
	 */
	public function by_name( $args, $assoc_args ) {
		$module = new DeleteTermsByNameModule();

		$message = $module->process_cli_request( $assoc_args );

		\WP_CLI::success( $message );
	}
}
