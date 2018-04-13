<?php

namespace BulkWP\BulkDelete\Core\Base;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test the abstract interfaces of BaseMetabox class.
 *
 * Tests \BulkWP\BulkDelete\Core\Base\BaseMetabox
 */
class BaseMetaboxTest extends WPCoreUnitTestCase {
	/**
	 * Name of the class to be tested.
	 *
	 * @var string
	 */
	protected $class_name = 'BulkWP\\BulkDelete\\Core\\Base\\BaseMetabox';

	/**
	 * When a new object is created, the `initialize` abstract method should be called.
	 */
	public function test_that_initialize_is_called_by_constructor() {
		$stub = $this->getMockForAbstractClass( $this->class_name );
		$stub->expects( $this->once() )->method( 'initialize' );

		$stub->__construct();
	}

	/**
	 *  The `render` abstract method should be called by `render_box` method.
	 */
	public function test_that_render_is_called_by_render_box() {
		$stub = $this->getMockForAbstractClass( $this->class_name );
		$stub->expects( $this->once() )->method( 'render' );

		$stub->render_box();
	}

	/**
	 * Process method should call all the abstract methods used inside it.
	 */
	public function test_that_process_method_calls_all_the_required_methods() {
		$stub = $this->getMockForAbstractClass( $this->class_name );
		$stub->expects( $this->once() )->method( 'convert_user_input_to_options' );

		$stub->process( array() );
	}

	/**
	 * Test `is_scheduled` method.
	 *
	 * @param array $input           Input to the `is_scheduled` method.
	 * @param bool  $expected_output Expected output of `is_scheduled` method.
	 *
	 * @throws \ReflectionException Throws an exception if something went wrong trying to access protected method.
	 *
	 * @dataProvider provide_data_to_test_is_scheduled_method
	 */
	public function test_is_scheduled_method( $input, $expected_output ) {
		$stub = $this->getMockForAbstractClass( $this->class_name );

		$output = $this->invoke_protected_method( $stub, 'is_scheduled', array( $input ) );

		$this->assertEquals( $expected_output, $output );
	}

	/**
	 * Data provider to test `is_scheduled` method.
	 *
	 * @see BaseMetaboxTest::test_is_scheduled_method() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_is_scheduled_method() {
		return array(
			array( array( 'is_scheduled' => false ), false ),
			array( array( 'is_scheduled' => true ), true ),
		);
	}

	/**
	 * Test `parse_cron_request` method.
	 *
	 * @param string $field_slug      The field slug that will be used.
	 * @param array  $input           Input to the method.
	 * @param array  $expected_output Expected output.
	 *
	 * @throws \ReflectionException Throws an exception if something went wrong trying to access protected property.
	 *
	 * @dataProvider provide_data_to_test_parse_cron_filters_method
	 */
	public function test_parse_cron_filters( $field_slug, $input, $expected_output ) {
		$stub = $this->getMockForAbstractClass( $this->class_name );

		$property_name = 'field_slug';

		$this->set_protected_property( $stub, $property_name, $field_slug );

		$output = $this->invoke_protected_method( $stub, 'parse_cron_filters', array( $input ) );

		$this->assertEquals( $expected_output, $output );
	}

	/**
	 * Data provider to test `parse_cron_request` method.
	 *
	 * @see BaseMetaboxTest::parse_cron_filters() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_parse_cron_filters_method() {
		$test_data = array(
			array( 'some_slug', array( 'smbd_some_slug_cron' => 'false' ), array( 'is_scheduled' => false ) ),
			array( 'some_slug', array( 'smbd_some_slug_cron' => false ), array( 'is_scheduled' => false ) ),
		);

		$frequencies = array( '-1', 'hourly', 'twicedaily', 'daily' );

		$now = date( 'Y-m-d H:i:s' );

		// TODO: Add test for `now` string.
		$start_times = array(
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 5 minutes' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 10 minutes' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 30 minutes' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 1 hour' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 10 hour' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 1 days' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 2 days' ) ),
		);

		$time_pairs = array_combine( $start_times, array_map( 'bd_get_gmt_offseted_time', $start_times ) );

		foreach ( $frequencies as $frequency ) {
			foreach ( $time_pairs as $start_time => $gmt_time ) {
				$test_data[] = array(
					'some_slug',
					array(
						'smbd_some_slug_cron'       => 'true',
						'smbd_some_slug_cron_freq'  => $frequency,
						'smbd_some_slug_cron_start' => $start_time,
					),
					array(
						'is_scheduled' => true,
						'frequency'    => $frequency,
						'start_time'   => $gmt_time,
					),
				);
			}
		}

		return $test_data;
	}

	/**
	 * Test scheduling of deletion.
	 *
	 * @param array $cron_options Cron settings.
	 *
	 * @throws \ReflectionException Throws an exception if something went wrong.
	 *
	 * @dataProvider provide_data_to_test_scheduling
	 */
	public function test_that_scheduling_deletion_works( $cron_options ) {
		$message   = array( 'scheduled' => 'Scheduled Message' );
		$cron_hook = 'bd_some_cron_hook';
		$options   = array(
			'key1' => 'value1',
		);

		$stub = $this->getMockForAbstractClass( $this->class_name );

		$this->set_protected_property( $stub, 'messages', $message );
		$this->set_protected_property( $stub, 'cron_hook', $cron_hook );

		$output = $this->invoke_protected_method( $stub, 'schedule_deletion', array( $cron_options, $options ) );

		$this->assertStringStartsWith( $message['scheduled'], $output );

		$crons = _get_cron_array();
		$this->assertArrayHasKey( $cron_options['start_time'], $crons );
		$this->assertArrayHasKey( $cron_hook, $crons[ $cron_options['start_time'] ] );

		$hash = md5( serialize( array( $options ) ) );
		$this->assertArrayHasKey( $hash, $crons[ $cron_options['start_time'] ][ $cron_hook ] );

		$scheduled_cron_details = $crons[ $cron_options['start_time'] ][ $cron_hook ][ $hash ];

		if ( '-1' === $cron_options['frequency'] ) {
			$this->assertFalse( $scheduled_cron_details['schedule'] );
		} else {
			$this->assertEquals( $cron_options['frequency'], $scheduled_cron_details['schedule'] );
		}

		$this->assertEquals( $scheduled_cron_details['args'], array( $options ) );
	}

	/**
	 * Data provider to test scheduling of deletion.
	 *
	 * @see BaseMetaboxTest::test_that_scheduling_deletion_works() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_scheduling() {

		$frequencies = array( '-1', 'hourly', 'twicedaily', 'daily' );

		$now = date( 'Y-m-d H:i:s' );

		$start_times = array(
			'now',
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 5 minutes' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 10 minutes' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 30 minutes' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 1 hour' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 10 hour' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 1 days' ) ),
			date( 'Y-m-d H:i:s', strtotime( $now . ' + 2 days' ) ),
		);

		$gmt_start_times = array_map( 'bd_get_gmt_offseted_time', $start_times );

		$test_data = array();

		foreach ( $frequencies as $frequency ) {
			foreach ( $gmt_start_times as $gmt_start_time ) {
				$test_data[] = array(
					array(
						'frequency'  => $frequency,
						'start_time' => $gmt_start_time,
					),
				);
			}
		}

		return $test_data;
	}
}
