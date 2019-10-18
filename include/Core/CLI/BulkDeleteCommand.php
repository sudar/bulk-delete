<?php

namespace BulkWP\BulkDelete\Core\CLI;

class BulkDeleteCommand extends \WP_CLI_Command {
	public function posts() {
		WP_CLI::line( 'I am post' );
	}

	public function users() {
		WP_CLI::line( 'I am user' );
	}
}
