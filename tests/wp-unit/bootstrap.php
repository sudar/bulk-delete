<?php
/**
 * PHPUnit bootstrap file.
 */

// Load bootstrap file from BulkWP test tools.
if ( ! file_exists( dirname( dirname( __FILE__ ) ) . '/../vendor/autoload.php' ) ) {
	echo 'Could not find BulkWP Test tools. Have you run composer install?' . PHP_EOL;
	exit( 1 );
}
require_once dirname( dirname( __FILE__ ) ) . '/../vendor/autoload.php';

/**
 * Manually load the plugin being tested.
 */
\BulkWP\Tests\WPCore\load_plugins_for_testing(
	array(
		dirname( dirname( __FILE__ ) ) . '/../bulk-delete.php',
	)
);
