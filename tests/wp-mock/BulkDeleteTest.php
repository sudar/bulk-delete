<?php

use BulkWP\Tests\WPMock\WPMockTestCase;

/**
 * Test Bulk_Delete.
 */
class BulkDeleteTest extends WPMockTestCase {

	protected $test_files = [
		'bulk-delete.php',
	];

	function test_it_is_singleton() {
		$a = \Bulk_Delete::get_instance();
		$b = \Bulk_Delete::get_instance();

		$this->assertSame( $a, $b );
	}

	function test_if_cone_is_not_supported() {
		\WP_Mock::userFunction( '_doing_it_wrong', array(
			'times' => 1,
		) );

		$bulk_delete = \Bulk_Delete::get_instance();
		clone $bulk_delete;

		$this->assertConditionsMet();
	}

	function test_if_wakeup_is_not_supported() {
		\WP_Mock::userFunction( '_doing_it_wrong', array(
			'times' => 1,
		) );

		$bulk_delete = \Bulk_Delete::get_instance();
		unserialize( serialize( $bulk_delete ) );

		$this->assertConditionsMet();
	}

	function test_load_action() {
		\WP_Mock::expectAction( 'bd_loaded' );

		$bulk_delete = \Bulk_Delete::get_instance();
		$bulk_delete->load();

		$this->assertConditionsMet();
	}

	function test_translation_is_loaded() {
		\WP_Mock::userFunction( 'load_plugin_textdomain', array(
			'times' => 1,
			'args' => array( 'bulk-delete', false, \WP_Mock\Functions::type( 'string' ) )
		) );

		$bulk_delete = \Bulk_Delete::get_instance();
		$bulk_delete->on_init();

		$this->assertConditionsMet();
	}
}
