<?php
/**
 * PHPUnit bootstrap file for WP Mock.
 */

// Plugin root.
if ( ! defined( 'PLUGIN_ROOT' ) ) {
	define( 'PLUGIN_ROOT', __DIR__ . '/../../' );
}

// First we need to load the composer autoloader so we can use WP Mock.
require_once __DIR__ . '/../../vendor/autoload.php';

// Now call the bootstrap method of WP Mock.
WP_Mock::setUsePatchwork( true );
WP_Mock::bootstrap();

// Mocks for WordPress core functions.
if ( ! file_exists( dirname( dirname( __FILE__ ) ) . '/../vendor/sudar/wp-plugin-test-tools/src/Tests/WPMock/wp-function-mocks.php' ) ) {
	echo 'Could not find BulkWP Test tools. Have you run composer install?' . PHP_EOL;
	exit( 1 );
}
require_once dirname( dirname( __FILE__ ) ) . '/../vendor/sudar/wp-plugin-test-tools/src/Tests/WPMock/wp-function-mocks.php';
