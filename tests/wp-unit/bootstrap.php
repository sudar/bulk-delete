<?php
/**
 * PHPUnit bootstrap file.
 */

define( 'BD_TEST_PLUGIN_BASE_FILE', dirname( dirname( __FILE__ ) ) . '/../bulk-delete.php' );

// Load BulkWP test tools.
if ( ! file_exists( dirname( dirname( __FILE__ ) ) . '/../vendor/sudar/wp-plugin-test-tools/src/Tests/WPCore/bootstrap.php' ) ) {
	echo 'Could not find BulkWP Test tools. Have you run composer install?' . PHP_EOL;
	exit( 1 );
}
require_once dirname( dirname( __FILE__ ) ) . '/../vendor/sudar/wp-plugin-test-tools/src/Tests/WPCore/bootstrap.php';
