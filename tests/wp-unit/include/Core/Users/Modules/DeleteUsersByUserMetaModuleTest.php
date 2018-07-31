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
	 * User with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_1;

	/**
	 * User with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_2;

	/**
	 * User with role as 'Subscriber' whose registration date is two days
	 * older than the current
	 *
	 * @var int
	 */
	protected $subscriber_3;

	/**
	 * User with role as 'Subscriber'.
	 *
	 * @var int
	 */
	protected $subscriber_4;

	/**
	 * Filters with default values.
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
	}

	public function tearDown() {
		wp_delete_user( $this->subscriber_1 );
		wp_delete_user( $this->subscriber_2 );
		wp_delete_user( $this->subscriber_3 );
		wp_delete_user( $this->subscriber_4 );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_operators_and_with_no_filters_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_operators_and_with_no_filters_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_when_user_and_no_filters_set() {
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
					'count_of_deleted_users' => 2,
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
						'meta_value'   => 'bulk',
						'meta_compare' => 'STARTS WITH',
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
						'meta_value'   => 'hulk',
						'meta_compare' => 'ENDS WITH',
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
	 * @param array $input           Input to the `users_can_be_deleted_when_user_and_no_filters_set` method.
	 * @param bool  $expected_output Expected output of `users_can_be_deleted_when_user_and_no_filters_set` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_when_user_and_no_filters_set
	 */
	public function test_that_users_can_be_deleted_with_string_meta_operators_and_with_no_filters_set($input, $expected_output) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$users_with_meta_value_3 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_3, 'ID' ) );

		$users_with_meta_value_4 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_4, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_operators_and_with_user_registration_filter_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_operators_and_with_user_registration_filter_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_with_string_meta_operators_and_with_user_registration_filter_set(
	) {
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
						'meta_value'          => 'bulk',
						'meta_compare'        => 'STARTS WITH',
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
						'meta_compare'        => 'ENDS WITH',
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
	 * Test User deletion with string meta operators and with user registration filter set.
	 *
	 * @param array $input           Input to the `users_can_be_deleted_with_string_meta_operators_and_with_user_registration_filter_set` method.
	 * @param bool  $expected_output Expected output of `users_can_be_deleted_when_user_with_user_registration_filter_set` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_operators_and_with_user_registration_filter_set
	 */
	public function test_that_users_can_be_deleted_with_string_meta_operators_and_with_user_registration_filter_set
	($input, $expected_output) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$users_with_meta_value_3 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_3, 'ID' ) );

		$subscriber_data = get_userdata( $this->subscriber_3 );

		if ( $subscriber_data instanceof \WP_User ) {
			$todays_date                    = new \DateTime();
			$subscriber_3_registration_date = new \DateTime( $subscriber_data->user_registered );
			$diff_in_days                   = $todays_date->diff( $subscriber_3_registration_date )->format( '%R%a' );

			$this->assertEquals( '-2', $diff_in_days );
		}

		$users_with_meta_value_4 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_4, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Data provider to test `test_that_users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set` method.
	 *
	 * @see DeleteUsersByUserMetaModuleTest::test_that_users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function
	provide_data_to_test_that_users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set (
	) {
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
						'meta_value'          => 'bulk',
						'meta_compare'        => 'STARTS WITH',
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
						'meta_compare'        => 'ENDS WITH',
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
	 * Test User deletion with string meta operators and with posts filter set.
	 *
	 * @param array $input           Input to the `users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set` method.
	 * @param bool  $expected_output Expected output of `users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set
	 */
	public function test_that_users_can_be_deleted_with_string_meta_operators_and_with_posts_filter_set
	($input, $expected_output) {
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

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$subscriber_1_posts = get_posts( array(
			'author'    => $this->subscriber_1,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_1_posts ) );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$subscriber_2_posts = get_posts( array(
			'author' => $this->subscriber_2,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_2_posts ) );

		$users_with_meta_value_3 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_3, 'ID' ) );

		$subscriber_data = get_userdata( $this->subscriber_3 );

		if ( $subscriber_data instanceof \WP_User ) {
			$todays_date                    = new \DateTime();
			$subscriber_3_registration_date = new \DateTime( $subscriber_data->user_registered );
			$diff_in_days                   = $todays_date->diff( $subscriber_3_registration_date )->format( '%R%a' );

			$this->assertEquals( '-2', $diff_in_days );
		}

		$users_with_meta_value_4 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_4, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}

	/**
	 * Test User deletion with string meta operators and with posts filter set.
	 *
	 * @param array $input           Input to the `delete()` method.
	 * @param bool  $expected_output Expected output of `delete()` method.
	 *
	 * @dataProvider provide_data_to_test_that_users_can_be_deleted_with_string_meta_operators_and_in_batches
	 */
	public function test_that_users_can_be_deleted_with_string_meta_operators_and_in_batches
	($input, $expected_output) {
		// Update user meta.
		update_user_meta( $this->subscriber_1, 'bwp_plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_2, 'bwp_plugin_name', 'my_awesome_plugin' );
		update_user_meta( $this->subscriber_3, 'bwp_plugin_name', 'bulk_move' );
		update_user_meta( $this->subscriber_4, 'bwp_plugin_name', 'the_green_hulk' );

		$users_with_meta_value_1 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_1 ), wp_list_pluck( $users_with_meta_value_1, 'ID' ) );

		$subscriber_1_posts = get_posts( array(
			'author'    => $this->subscriber_1,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_1_posts ) );

		$users_with_meta_value_2 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'my_awesome_plugin',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_2 ), wp_list_pluck( $users_with_meta_value_2, 'ID' ) );

		$subscriber_2_posts = get_posts( array(
			'author' => $this->subscriber_2,
			'post_type' => array( 'post' ),
		) );

		$this->assertEquals( 1, count( $subscriber_2_posts ) );

		$users_with_meta_value_3 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'bulk_move',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_3 ), wp_list_pluck( $users_with_meta_value_3, 'ID' ) );

		$subscriber_data = get_userdata( $this->subscriber_3 );

		if ( $subscriber_data instanceof \WP_User ) {
			$todays_date                    = new \DateTime();
			$subscriber_3_registration_date = new \DateTime( $subscriber_data->user_registered );
			$diff_in_days                   = $todays_date->diff( $subscriber_3_registration_date )->format( '%R%a' );

			$this->assertEquals( '-2', $diff_in_days );
		}

		$users_with_meta_value_4 = get_users( array(
			'meta_key'     => 'bwp_plugin_name',
			'meta_value'   => 'the_green_hulk',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber_4 ), wp_list_pluck( $users_with_meta_value_4, 'ID' ) );

		$delete_options         = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$count_of_deleted_users = $this->module->delete( $delete_options );

		$this->assertEquals( $expected_output['count_of_deleted_users'], $count_of_deleted_users );
	}
}
