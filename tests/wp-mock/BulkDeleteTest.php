<?php

use BulkWP\BulkDelete\Core\BulkDelete;
use BulkWP\Tests\WPMock\WPMockTestCase;

/**
 * Test BulkDelete class.
 */
class BulkDeleteTest extends WPMockTestCase {

	protected $test_files = [
		'include/Core/BulkDelete.php',
		'include/Core/Controller.php',
		'include/Core/Addon/Upseller.php',
	];

	function test_it_is_singleton() {
		$a = BulkDelete::get_instance();
		$b = BulkDelete::get_instance();

		$this->assertSame( $a, $b );
	}

	function test_if_cone_is_not_supported() {
		\WP_Mock::userFunction( '_doing_it_wrong', array(
			'times' => 1,
		) );

		$bulk_delete = BulkDelete::get_instance();
		clone $bulk_delete;

		$this->assertConditionsMet();
	}

	function test_if_wakeup_is_not_supported() {
		\WP_Mock::userFunction( '_doing_it_wrong', array(
			'times' => 1,
		) );

		$bulk_delete = BulkDelete::get_instance();
		unserialize( serialize( $bulk_delete ) );

		$this->assertConditionsMet();
	}

	function test_load_action() {
		$plugin_file = 'path/to/some/file';
		\WP_Mock::expectAction( 'bd_loaded', $plugin_file );

		$bulk_delete = BulkDelete::get_instance();
		$bulk_delete->set_plugin_file( $plugin_file );
		$bulk_delete->load();

		$this->assertConditionsMet();
	}

	function test_translation_is_loaded() {
		\WP_Mock::userFunction( 'load_plugin_textdomain', array(
			'times' => 1,
			'args' => array( 'bulk-delete', false, \WP_Mock\Functions::type( 'string' ) )
		) );

		$bulk_delete = BulkDelete::get_instance();
		$bulk_delete->set_plugin_file( 'path/to/some/file/' );
		$bulk_delete->on_init();

		$this->assertConditionsMet();
	}
}
