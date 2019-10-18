<?php

namespace BulkWP\BulkDelete\Core\CLI;

use BulkWP\BulkDelete\Core\CLI\BulkDeleteCommand;

/**
 * Commands to support WP-CLI.
 */
class CLI extends \WP_CLI_Command {
	public function load() {
		WP_CLI::add_command( 'bulk-delete', 'BulkDeleteCommand' );
	}
}
