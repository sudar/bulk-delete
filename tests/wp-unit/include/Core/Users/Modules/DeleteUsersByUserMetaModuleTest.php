<?php

namespace BulkWP\BulkDelete\Core\Users\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of users by user meta.
 *
 * Tests \BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaModule
 *
 * @since 6.0.0
 */
class DeleteUsersByUserMetaModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaModule
	 */
	protected $module;

	/**
	 * User ID with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_1;

	/**
	 * User ID with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_2;

	/**
	 * User ID with role as 'Subscriber' whose registration date is two days
	 * older than the current date.
	 *
	 * @var int
	 */
	protected $subscriber_3;

	/**
	 * User ID with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_4;

	/**
	 * User ID with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_5;

	/**
	 * User ID with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_6;

	/**
	 * User ID with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_7;

	/**
	 * User ID with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_8;

	/**
	 * Delete Options default values.
	 *
	 * @var array
	 */
	protected $common_filter_defaults = array(
		'login_restrict'      => false,
		'login_days'          => 0,
		'registered_restrict' => false,
		'registered_days'     => 0,
		'no_posts'            => false,
		'no_posts_post_types' => array(),
		'limit_to'            => 0,
	);

	public function setUp() {
		parent::setUp();

		$this->module = new DeleteUsersByUserMetaModule();

		$user_role = 'subscriber';

		$this->subscriber_1 = $this->factory->user->create( array(
			'role' => $user_role,
		) );
		$this->subscriber_2 = $this->factory->user->create( array(
			'role' => $user_role,
		) );
		$this->subscriber_3 = $this->factory->user->create( array(
			'role'            => $user_role,
			'user_registered' => date( 'Y-m-d', strtotime( '-2 day' ) ),
		) );
		$this->subscriber_4 = $this->factory->user->create( array(
			'role' => $user_role,
		) );
		$this->subscriber_5 = $this->factory->user->create( array(
			'role'            => $user_role,
			'user_registered' => date( 'Y-m-d', strtotime( '-5 day' ) ),
		) );
		$this->subscriber_6 = $this->factory->user->create( array(
			'role'            => $user_role,
			'user_registered' => date( 'Y-m-d', strtotime( '-3 day' ) ),
		) );
		$this->subscriber_7 = $this->factory->user->create( array(
			'role'            => $user_role,
			'user_registered' => date( 'Y-m-d', strtotime( '-5 day' ) ),
		) );
		$this->subscriber_8 = $this->factory->user->create( array(
			'role'            => $user_role,
			'user_registered' => date( 'Y-m-d', strtotime( '-3 day' ) ),
		) );
	}

	public function tearDown() {
		wp_delete_user( $this->subscriber_1 );
		wp_delete_user( $this->subscriber_2 );
		wp_delete_user( $this->subscriber_3 );
		wp_delete_user( $this->subscriber_4 );
		wp_delete_user( $this->subscriber_5 );
		wp_delete_user( $this->subscriber_6 );
		wp_delete_user( $this->subscriber_7 );
		wp_delete_user( $this->subscriber_8 );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_value_and_with_no_filters_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_value_and_with_no_filters_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_with_no_filters_set() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'bulk_delete',
						'meta_compare' => '=',
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'my_awesome_plugin',
						'meta_compare' => '!=',
					),
				),
				array(
					'count_of_deleted_users' => 3,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'hulk',
						'meta_compare' => 'NOT LIKE',
					),
				),
				array(
					'count_of_deleted_users' => 3,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'hulk',
						'meta_compare' => 'NOT LIKE',
					),
				),
				array(
					'count_of_deleted_users' => 3,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => '^' . 'bulk', // phpcs:ignore
						'meta_compare' => 'REGEXP',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'hulk' . '$', // phpcs:ignore
						'meta_compare' => 'REGEXP',
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
		);
	}

	/**
	 * Test basic case of delete users by user meta.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_with_no_filters_set
	 */
	public function test_that_users_can_be_deleted_with_string_meta_value_and_with_no_filters_set( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		$users_with_meta_value_bulk_delete = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_bulk_delete, 'ID' ) );

		$users_with_meta_value_my_awesome_plugin = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_my_awesome_plugin, 'ID' ) );

		$users_with_meta_value_bulk_move = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_bulk_move, 'ID' ) );

		$users_with_meta_value_the_green_hulk = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_the_green_hulk, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_value_and_with_user_registration_filter_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_value_and_with_user_registration_filter_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_with_user_registration_filter_set() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk_delete',
						'meta_compare'        => '=',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 0,
				),
			),

			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'my_awesome_plugin',
						'meta_compare'        => '!=',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk',
						'meta_compare'        => 'LIKE',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk',
						'meta_compare'        => 'NOT LIKE',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => '^' . 'bulk', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'registered_restrict' => true,
						'registered_days'     => 1,

					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk' . '$', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 0,
				),
			),
		);
	}

	/**
	 * Test User deletion whose meta value is of string type and with user registration filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_with_user_registration_filter_set
	 */
	public function test_that_users_can_be_deleted_with_string_meta_value_and_with_user_registration_filter_set( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		$users_with_meta_value_bulk_delete = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_bulk_delete, 'ID' ) );

		$users_with_meta_value_my_awesome_plugin = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_my_awesome_plugin, 'ID' ) );

		$users_with_meta_value_bulk_move = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_bulk_move, 'ID' ) );

		$subscriber_3_data = get_userdata( $this->subscriber_3 );

		// Assert Subscriber 3 registration date is two days older than current date.
		$this->assertTrue( $subscriber_3_data instanceof \WP_User );

		$todays_date                    = new \DateTime();
		$subscriber_3_registration_date = new \DateTime( $subscriber_3_data->user_registered );
		$diff_in_days                   = $todays_date->diff( $subscriber_3_registration_date )->format( '%R%a' );

		$this->assertEquals( '-2', $diff_in_days );

		$users_with_meta_value_the_green_hulk = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_the_green_hulk, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_value_and_with_posts_filter_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_value_and_with_posts_filter_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_with_posts_filter_set() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk_delete',
						'meta_compare'        => '=',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 0,
				),
			),

			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'my_awesome_plugin',
						'meta_compare'        => '!=',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk',
						'meta_compare'        => 'LIKE',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk',
						'meta_compare'        => 'NOT LIKE',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => '^' . 'bulk', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk' . '$', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
		);
	}

	/**
	 * Test User deletion with meta value whose type is string and with posts filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_with_posts_filter_set
	 */
	public function test_that_users_can_be_deleted_with_string_meta_value_and_with_posts_filter_set( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		$post_1 = $this->factory->post->create( array(
			'post_title'  => 'Post 1',
			'post_author' => $this->subscriber_1,
		) );

		$post_2 = $this->factory->post->create( array(
			'post_title'  => 'Post 2',
			'post_author' => $this->subscriber_2,
		) );

		$users_with_meta_value_bulk_delete = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_bulk_delete, 'ID' ) );

		$subscriber_1_posts = get_posts( array(
			'author'    => $this->subscriber_1,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_1_posts ) );

		$users_with_meta_value_my_awesome_plugin = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_my_awesome_plugin, 'ID' ) );

		$subscriber_2_posts = get_posts( array(
			'author'    => $this->subscriber_2,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_2_posts ) );

		$users_with_meta_value_bulk_move = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_bulk_move, 'ID' ) );

		$subscriber_3_posts = get_posts( array(
			'author'    => $this->subscriber_3,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 0, count( $subscriber_3_posts ) );

		$subscriber_3_data = get_userdata( $this->subscriber_3 );

		$this->assertTrue( $subscriber_3_data instanceof \WP_User );

		$todays_date                    = new \DateTime();
		$subscriber_3_registration_date = new \DateTime( $subscriber_3_data->user_registered );
		$diff_in_days                   = $todays_date->diff( $subscriber_3_registration_date )->format( '%R%a' );

		$this->assertEquals( '-2', $diff_in_days );

		$users_with_meta_value_4 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_4, 'ID' ) );

		$subscriber_4_posts = get_posts( array(
			'author'    => $this->subscriber_4,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 0, count( $subscriber_4_posts ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_in_batches` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_value_and_in_batches()
	 *      To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_in_batches() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'bulk_delete',
						'meta_compare' => '=',
						'limit_to'     => 1,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 1,
					'count_of_deleted_users_in_batch_2' => 1,
				),
			),

			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'my_awesome_plugin',
						'meta_compare' => '!=',
						'limit_to'     => 4,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 4,
					'count_of_deleted_users_in_batch_2' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'bulk',
						'meta_compare' => 'LIKE',
						'limit_to'     => 4,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 4,
					'count_of_deleted_users_in_batch_2' => 0,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'hulk',
						'meta_compare' => 'NOT LIKE',
						'limit_to'     => 6,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 6,
					'count_of_deleted_users_in_batch_2' => 0,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => '^' . 'bulk', // phpcs:ignore
						'meta_compare' => 'REGEXP',
						'limit_to'     => 2,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 2,
					'count_of_deleted_users_in_batch_2' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_plugin_name',
						'meta_value'   => 'hulk' . '$', // phpcs:ignore
						'meta_compare' => 'REGEXP',
						'limit_to'     => 1,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 1,
					'count_of_deleted_users_in_batch_2' => 1,
				),
			),
		);
	}

	/**
	 * Test User deletion whose meta value is of type string and with posts filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_in_batches
	 */
	public function test_that_users_can_be_deleted_with_string_meta_value_and_in_batches( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		update_user_meta( $this->subscriber_5, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_6, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_7, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_8, 'bwp_plugin_name', 'the_green_hulk' );

		$users_with_meta_value_bulk_delete = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1, $this->subscriber_5 ), wp_list_pluck( $users_with_meta_value_bulk_delete, 'ID' ) );

		$users_with_meta_value_my_awesome_plugin = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2, $this->subscriber_6 ), wp_list_pluck( $users_with_meta_value_my_awesome_plugin, 'ID' ) );

		$users_with_meta_value_bulk_move = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3, $this->subscriber_7 ), wp_list_pluck( $users_with_meta_value_bulk_move, 'ID' ) );

		$users_with_meta_value_the_green_hulk = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4, $this->subscriber_8 ), wp_list_pluck( $users_with_meta_value_the_green_hulk, 'ID' ) );

		$delete_options = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		// 1st Batch deletion.
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users_in_batch_1'], $count_of_deleted_users );

		// 2nd Batch deletion.
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users_in_batch_2'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_numeric_meta_value_and_no_filters_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_numeric_meta_value_and_no_filters_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_no_filters_set() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '0',
						'meta_compare' => '>',
						'type'         => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '1',
						'meta_compare' => '>=',
						'type'         => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '1',
						'meta_compare' => '<',
						'type'         => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '0',
						'meta_compare' => '<=',
						'type'         => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
		);
	}

	/**
	 * Test basic case of delete users by user meta whose meta value is of type int.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_no_filters_set
	 */
	public function test_that_users_can_be_deleted_with_numeric_meta_value_and_no_filters_set( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_integer', '-1' );
		update_user_meta( $this->subscriber_2, 'bwp_integer', '0' );
		update_user_meta( $this->subscriber_3, 'bwp_integer', '1' );
		update_user_meta( $this->subscriber_4, 'bwp_integer', '2' );

		$users_with_meta_value_negative_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '-1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_negative_1, 'ID' ) );

		$users_with_meta_value_0 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '0',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_0, 'ID' ) );

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '2',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_operators_and_with_no_filters_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_operators_and_with_no_filters_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_with_user_registration_filter_set() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '0',
						'meta_compare'        => '>',
						'type'                => 'NUMERIC',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '1',
						'meta_compare'        => '>=',
						'type'                => 'NUMERIC',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '1',
						'meta_compare'        => '<',
						'type'                => 'NUMERIC',
						'registered_restrict' => true,
						'registered_days'     => 0,
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '0',
						'meta_compare'        => '<=',
						'type'                => 'NUMERIC',
						'registered_restrict' => true,
						'registered_days'     => 1,
					),
				),
				array(
					'count_of_deleted_users' => 0,
				),
			),
		);
	}

	/**
	 * Test user deletion whose meta value is of type int and with user registration filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_with_user_registration_filter_set
	 */
	public function test_that_users_can_be_deleted_with_numeric_meta_value_and_with_user_registration_filter_set( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_integer', '-1' );
		update_user_meta( $this->subscriber_2, 'bwp_integer', '0' );
		update_user_meta( $this->subscriber_3, 'bwp_integer', '1' );
		update_user_meta( $this->subscriber_4, 'bwp_integer', '2' );

		$users_with_meta_value_negative_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '-1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_negative_1, 'ID' ) );

		$users_with_meta_value_0 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '0',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_0, 'ID' ) );

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$subscriber_3_data = get_userdata( $this->subscriber_3 );

		// Assert Subscriber 3 registration date is two days older than current date.
		$this->assertTrue( $subscriber_3_data instanceof \WP_User );

		$todays_date                    = new \DateTime();
		$subscriber_3_registration_date = new \DateTime( $subscriber_3_data->user_registered );
		$diff_in_days                   = $todays_date->diff( $subscriber_3_registration_date )->format( '%R%a' );

		$this->assertEquals( '-2', $diff_in_days );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '2',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set`
	 * method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set()
	 *      To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_with_posts_filter_set() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '0',
						'meta_compare'        => '>',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
						'type'                => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),

			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '0',
						'meta_compare'        => '>=',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
						'type'                => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '2',
						'meta_compare'        => '<',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
						'type'                => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_integer',
						'meta_value'          => '2',
						'meta_compare'        => '<=',
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
						'type'                => 'NUMERIC',
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
		);
	}

	/**
	 * Test User deletion whose meta value is of type int and with posts filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_with_posts_filter_set
	 */
	public function test_that_users_can_be_deleted_with_numeric_meta_value_and_with_posts_filter_set( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_integer', '-1' );
		update_user_meta( $this->subscriber_2, 'bwp_integer', '0' );
		update_user_meta( $this->subscriber_3, 'bwp_integer', '1' );
		update_user_meta( $this->subscriber_4, 'bwp_integer', '2' );

		$post_1 = $this->factory->post->create( array(
			'post_title'  => 'Post 1',
			'post_author' => $this->subscriber_1,
		) );

		$post_2 = $this->factory->post->create( array(
			'post_title'  => 'Post 2',
			'post_author' => $this->subscriber_2,
		) );

		$users_with_meta_value_negative_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '-1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_negative_1, 'ID' ) );

		$subscriber_1_posts = get_posts( array(
			'author'    => $this->subscriber_1,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_1_posts ) );

		$users_with_meta_value_0 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '0',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_0, 'ID' ) );

		$subscriber_2_posts = get_posts( array(
			'author'    => $this->subscriber_2,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_2_posts ) );

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$subscriber_3_posts = get_posts( array(
			'author'    => $this->subscriber_3,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 0, count( $subscriber_3_posts ) );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '2',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$subscriber_4_posts = get_posts( array(
			'author'    => $this->subscriber_4,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 0, count( $subscriber_4_posts ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_operators_and_in_batches` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_operators_and_in_batches()
	 *      To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_in_batches() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '-1',
						'meta_compare' => '>',
						'limit_to'     => 2,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 2,
					'count_of_deleted_users_in_batch_2' => 1,
				),
			),

			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '0',
						'meta_compare' => '>=',
						'limit_to'     => 2,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 2,
					'count_of_deleted_users_in_batch_2' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '1',
						'meta_compare' => '<',
						'limit_to'     => 1,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 1,
					'count_of_deleted_users_in_batch_2' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'     => 'bwp_integer',
						'meta_value'   => '2',
						'meta_compare' => '<=',
						'limit_to'     => 2,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 2,
					'count_of_deleted_users_in_batch_2' => 2,
				),
			),
		);
	}

	/**
	 * Test User deletion with string meta operators and with posts filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_numeric_meta_value_and_in_batches
	 */
	public function test_that_users_can_be_deleted_with_numeric_meta_value_and_in_batches( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_integer', '-1' );
		update_user_meta( $this->subscriber_2, 'bwp_integer', '0' );
		update_user_meta( $this->subscriber_3, 'bwp_integer', '1' );
		update_user_meta( $this->subscriber_4, 'bwp_integer', '2' );

		$users_with_meta_value_negative_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '-1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_negative_1, 'ID' ) );

		$users_with_meta_value_0 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '0',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_0, 'ID' ) );

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '1',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_integer',
			'meta_value'   => '2',
			'meta_compare' => '=',
			'type'         => 'NUMERIC',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$delete_options = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		// 1st Batch deletion.
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users_in_batch_1'], $count_of_deleted_users );

		// 2nd Batch deletion.
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users_in_batch_2'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_value_with_user_registration_and_post_filter_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_value_with_user_registration_and_post_filter_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_with_user_registration_and_post_filter_set() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk_delete',
						'meta_compare'        => '=',
						'registered_restrict' => true,
						'registered_days'     => 3,
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 0,
				),
			),

			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'my_awesome_plugin',
						'meta_compare'        => '!=',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'no_posts'            => true,
						'no_posts_post_types' => array( 'page' ),
					),
				),
				array(
					'count_of_deleted_users' => 2,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk',
						'meta_compare'        => 'LIKE',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk',
						'meta_compare'        => 'NOT LIKE',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => '^' . 'bulk', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'registered_restrict' => true,
						'registered_days'     => 4,
						'no_posts'            => true,
						'no_posts_post_types' => array( 'attachment' ),

					),
				),
				array(
					'count_of_deleted_users' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk' . '$', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'no_posts'            => true,
						'no_posts_post_types' => array( 'post' ),
					),
				),
				array(
					'count_of_deleted_users' => 0,
				),
			),
		);
	}

	/**
	 * Test User deletion whose meta value is of string type and with user registration and post filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_with_user_registration_and_post_filter_set
	 */
	public function test_that_users_can_be_deleted_with_string_meta_value_with_user_registration_and_post_filter_set( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );
		update_user_meta( $this->subscriber_5, 'bwp_plugin_name', 'bulk_delete' );

		$post_1 = $this->factory->post->create( array(
			'post_title'  => 'Post 1',
			'post_author' => $this->subscriber_4,
		) );

		$post_2 = $this->factory->post->create( array(
			'post_title'  => 'Post 2',
			'post_author' => $this->subscriber_5,
		) );

		$users_with_meta_value_bulk_delete = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1, $this->subscriber_5 ), wp_list_pluck( $users_with_meta_value_bulk_delete, 'ID' ) );

		$users_with_meta_value_my_awesome_plugin = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_my_awesome_plugin, 'ID' ) );

		$users_with_meta_value_bulk_move = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_bulk_move, 'ID' ) );

		$subscriber_3_data = get_userdata( $this->subscriber_3 );

		// Assert Subscriber 3 registration date is two days older than current date.
		$this->assertTrue( $subscriber_3_data instanceof \WP_User );

		$todays_date                    = new \DateTime();
		$subscriber_3_registration_date = new \DateTime( $subscriber_3_data->user_registered );
		$diff_in_days                   = $todays_date->diff( $subscriber_3_registration_date )->format( '%R%a' );

		$this->assertEquals( '-2', $diff_in_days );

		$users_with_meta_value_the_green_hulk = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_the_green_hulk, 'ID' ) );

		$subscriber_5_data = get_userdata( $this->subscriber_5 );

		// Assert Subscriber 5 registration date is five days older than current date.
		$this->assertTrue( $subscriber_5_data instanceof \WP_User );

		$subscriber_5_registration_date = new \DateTime( $subscriber_5_data->user_registered );
		$diff_in_days                   = $todays_date->diff( $subscriber_5_registration_date )->format( '%R%a' );

		$this->assertEquals( '-5', $diff_in_days );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );

	}

	/**
	 * Data provider to test `provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_user_registration_filter_in_batches` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_value_and_user_registration_filter_in_batches()
	 *      To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_user_registration_filter_in_batches() {
		return array(
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk_delete',
						'meta_compare'        => '=',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'limit_to'            => 1,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 1,
					'count_of_deleted_users_in_batch_2' => 0,
				),
			),

			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'my_awesome_plugin',
						'meta_compare'        => '!=',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'limit_to'            => 3,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 3,
					'count_of_deleted_users_in_batch_2' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'bulk',
						'meta_compare'        => 'LIKE',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'limit_to'            => 4,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 3,
					'count_of_deleted_users_in_batch_2' => 0,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk',
						'meta_compare'        => 'NOT LIKE',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'limit_to'            => 6,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 4,
					'count_of_deleted_users_in_batch_2' => 0,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => '^' . 'bulk', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'limit_to'            => 2,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 2,
					'count_of_deleted_users_in_batch_2' => 1,
				),
			),
			array(
				array(
					'delete_options' => array(
						'meta_key'            => 'bwp_plugin_name',
						'meta_value'          => 'hulk' . '$', // phpcs:ignore
						'meta_compare'        => 'REGEXP',
						'registered_restrict' => true,
						'registered_days'     => 1,
						'limit_to'            => 1,
					),
				),
				array(
					'count_of_deleted_users_in_batch_1' => 1,
					'count_of_deleted_users_in_batch_2' => 0,
				),
			),
		);
	}

	/**
	 * Test User deletion whose meta value is of type string and with user registration filter set in batches.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_value_and_user_registration_filter_in_batches
	 */
	public function test_that_users_can_be_deleted_with_string_meta_value_and_user_registration_filter_in_batches( $input, $expected_output ) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		update_user_meta( $this->subscriber_5, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_6, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_7, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_8, 'bwp_plugin_name', 'the_green_hulk' );

		$users_with_meta_value_bulk_delete = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1, $this->subscriber_5 ), wp_list_pluck( $users_with_meta_value_bulk_delete, 'ID' ) );

		$users_with_meta_value_my_awesome_plugin = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2, $this->subscriber_6 ), wp_list_pluck( $users_with_meta_value_my_awesome_plugin, 'ID' ) );

		$users_with_meta_value_bulk_move = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3, $this->subscriber_7 ), wp_list_pluck( $users_with_meta_value_bulk_move, 'ID' ) );

		$users_with_meta_value_the_green_hulk = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4, $this->subscriber_8 ), wp_list_pluck( $users_with_meta_value_the_green_hulk, 'ID' ) );

		$delete_options = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		// 1st Batch deletion.
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users_in_batch_1'], $count_of_deleted_users );

		// 2nd Batch deletion.
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users_in_batch_2'], $count_of_deleted_users );
	}
}
