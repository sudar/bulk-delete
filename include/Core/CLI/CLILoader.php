<?php

namespace BulkWP\BulkDelete\Core\CLI;

use BulkWP\BulkDelete\Core\CLI\Commands\DeletePostsCommand;
use BulkWP\BulkDelete\Core\CLI\Commands\DeleteTermsCommand;
use BulkWP\BulkDelete\Core\CLI\Commands\DeleteUsersCommand;
use BulkWP\BulkDelete\Core\CLI\Commands\SystemInfoCommand;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Loads all CLI command.
 *
 * @since 6.1.0
 */
class CLILoader {
	/**
	 * Base Command.
	 *
	 * @var string
	 */
	protected $base_command = 'bulk-delete';

	/**
	 * Load CLI command.
	 *
	 * This will be called after `bd_loaded` hook.
	 */
	public function load() {
		$commands = $this->get_commands();

		foreach ( $commands as $command ) {
			$this->register_command( $command );
		}
	}

	/**
	 * List of Bulk Delete WP CLI Commands.
	 *
	 * @return \BulkWP\BulkDelete\Core\Base\BaseCommand[]
	 */
	protected function get_commands() {
		$commands = array(
			SystemInfoCommand::class,
			DeletePostsCommand::class,
			DeleteTermsCommand::class,
			DeleteUsersCommand::class,
		);

		/**
		 * Filters the CLI command map.
		 *
		 * @since 6.1.0
		 *
		 * @param \BulkWP\BulkDelete\Core\Base\BaseCommand[] List of commands.
		 */
		return apply_filters( 'bd_cli_commands', $commands );
	}

	/**
	 * Register a command.
	 *
	 * @param \BulkWP\BulkDelete\Core\Base\BaseCommand $command Command to register.
	 */
	protected function register_command( $command ) {
		\WP_CLI::add_command( $this->base_command . ' ' . $command::get_command(), $command );
	}
}
