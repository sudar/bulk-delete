<?php

namespace BulkWP\BulkDelete\Core\CLI\Commands;

use BulkWP\BulkDelete\Core\Base\BaseCommand;
use BulkWP\BulkDelete\Core\SystemInfo\BulkDeleteSystemInfo;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * SystemInfo CLI Command.
 *
 * @since 6.1.0
 */
class SystemInfoCommand extends BaseCommand {
	/**
	 * Get the command.
	 *
	 * @return string Command name.
	 */
	public static function get_command() {
		return 'system-info';
	}

	/**
	 * Print System Info.
	 *
	 * @subcommand print
	 */
	public function get() {
		$system_info = new BulkDeleteSystemInfo( 'bulk-delete' );
		$system_info->print_bulk_delete_details();
	}
}
