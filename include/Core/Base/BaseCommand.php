<?php
namespace BulkWP\BulkDelete\Core\Base;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Base class for a Bulk Delete WP CLI command.
 *
 * @since 6.1.0
 */
abstract class BaseCommand extends \WP_CLI_Command {
	/**
	 * Get the command.
	 *
	 * This method is commented since PHP doesn't allow static abstract methods in PHP 5.6 or below.
	 * Refer to https://stackoverflow.com/a/31235907/24949 for the full explanation.
	 *
	 * Once WordPress increases the minimum PHP to 7.0 or above this method will be uncommented.
	 *
	 * @return string Command name.
	 */
	// abstract public static function get_command();
}
