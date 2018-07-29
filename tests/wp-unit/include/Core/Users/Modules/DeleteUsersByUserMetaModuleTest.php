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

	protected $subscriber;
	protected $subscriber_with_two_days_old_registration_date;
	protected $second_subscriber;

	private $common_filter_defaults;

	public function setUp() {
		parent::setUp();

		$this->common_filter_defaults = array(
			'login_restrict'      => false,
			'login_days'          => 0,
			'registered_restrict' => false,
			'registered_days'     => 0,
			'no_posts'            => false,
			'no_posts_post_types' => array(),
			'limit_to'            => 0,
		);

		$this->module = new DeleteUsersByUserMetaModule();

		$user_role = 'subscriber';

		// Create one user with role as `subscriber`.
		$this->subscriber = $this->factory->user->create( array(
			'role' => $user_role,
		) );

		// Create one user with role as `subscriber` with
		// registration date two days older than the current date.
		$this->subscriber_with_two_days_old_registration_date = $this->factory->user->create( array(
			'role'            => $user_role,
			'user_registered' => date( 'Y-m-d', strtotime( '-2 day' ) ),
		) );

		// Create another user with role as `subscriber`.
		$this->second_subscriber = $this->factory->user->create( array(
			'role' => $user_role,
		) );
	}

	public function tearDown() {
		wp_delete_user( $this->subscriber );
		wp_delete_user( $this->subscriber_with_two_days_old_registration_date );
		wp_delete_user( $this->second_subscriber );
	}

	/**
	 * Data provider to test `is_scheduled` method.
	 *
	 * @see BaseModuleTest::test_is_scheduled_method() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_users_can_be_deleted_when_user_and_no_filters_set() {
		$meta_key   = 'bwp_plugin_name';
		$meta_value = 'bulk-delete';

		$delete_options_when_meta_equals_to = array(
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => '=',
		);

		$get_users_when_meta_equals_to = array(
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => '=',
		);

		$delete_options_when_meta_equals_not_equals_to = array(
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => '!=',
		);

		$get_users_when_meta_equals_not_equals_to = array(
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => '!=',
		);

		return array(
			array(
				array(
					'delete_options' => $delete_options_when_meta_equals_to,
					'get_options'    => $get_users_when_meta_equals_to,
				), 0
			),
			array(
				array(
					'delete_options' => $delete_options_when_meta_equals_not_equals_to,
					'get_options'    => $get_users_when_meta_equals_not_equals_to,
				), 0
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
	public function test_that_users_can_be_deleted_when_user_and_no_filters_set($input, $expected_output) {
		$meta_key     = 'bwp_plugin_name';
		$meta_value   = 'bulk-delete';

		// Update user meta.
		update_user_meta( $this->subscriber, $meta_key, $meta_value );
		update_user_meta( $this->second_subscriber, $meta_key, 'my_awesome_plugin' );

		$delete_options = wp_parse_args( $input['delete_options'], $this->common_filter_defaults );
		$this->module->delete( $delete_options );

		// Assert that user meta has no user.
		$users_with_meta = get_users( $input['get_options'] );

		$output = count( $users_with_meta );
		$this->assertEquals( $expected_output, $output );
	}

//	/**
//	 * Test case for deleting users by user meta with user registration filter set.
//	 */
//	public function test_that_users_deleted_when_user_meta_value_equals_to_and_user_registration_filter_fulfilled() {
//		$meta_key     = 'plugin_name';
//		$meta_value   = 'bulk_delete';
//		$meta_compare = '=';
//
//		// Update user meta.
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, 'plugin_name', 'bulk_delete' );
//
//		// Assert that user meta has one user.
//		$users_with_meta = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//		$this->assertEquals( 1, count( $users_with_meta ) );
//
//		// call our method.
//		$delete_options = array(
//			'meta_key'            => $meta_key,
//			'meta_value'          => $meta_value,
//			'meta_compare'        => $meta_compare,
//			'registered_restrict' => true,
//			'registered_days'     => 1,
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that user meta has one user.
//		$users_with_meta = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//		$this->assertEquals( 0, count( $users_with_meta ) );
//	}
//
//	/**
//	 * Test case for not deleting users when user registration filter is not fulfilled.
//	 */
//	public function test_that_users_not_deleted_when_user_meta_value_equals_to_and_user_registration_filter_not_fulfilled(
//	) {
//		// Update user meta.
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, 'plugin_name', 'bulk_delete' );
//
//		// Assert that user meta has one user.
//		$users_in_meta = get_users( array(
//			'meta_key'     => 'plugin_name',
//			'meta_value'   => 'bulk_delete',
//			'meta_compare' => '=',
//		) );
//		$this->assertEquals( 1, count( $users_in_meta ) );
//
//		// call our method.
//		$delete_options = array(
//			'meta_key'            => 'plugin_name',
//			'meta_value'          => 'bulk_delete',
//			'meta_compare'        => '=',
//			'registered_restrict' => true,
//			'registered_days'     => 4,
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that user meta has no user.
//		$users_in_meta = get_users( array(
//			'meta_key'     => 'plugin_name',
//			'meta_value'   => 'bulk_delete',
//			'meta_compare' => '=',
//		) );
//		$this->assertEquals( 1, count( $users_in_meta ) );
//	}
//
//	/**
//	 * Test case of delete users by meta with filter set post type.
//	 */
//	public function test_that_users_deleted_when_user_meta_value_equals_to_and_restricted_by_post_type_filter() {
//		// Update user meta.
//		update_user_meta( $this->subscriber, 'plugin_name', 'bulk_delete' );
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, 'plugin_name', 'bulk_delete' );
//
//		// Create post and assign author.
//		$this->factory->post->create( array(
//			'post_title'  => 'post1',
//			'post_author' => $this->subscriber,
//		) );
//		$this->factory->post->create( array(
//			'post_title'  => 'page1',
//			'post_type'   => 'page',
//			'post_author' => $this->subscriber_with_two_days_old_registration_date,
//		) );
//
//		// Assert that user meta has two users $user1 and $user2.
//		$users_with_meta = get_users( array(
//			'meta_key'     => 'plugin_name',
//			'meta_value'   => 'bulk_delete',
//			'meta_compare' => '=',
//		) );
//
//		$this->assertEquals( array( $this->subscriber, $this->subscriber_with_two_days_old_registration_date ),
//			wp_list_pluck( $users_with_meta, 'ID' ) );
//
//		// Delete Users who do not have any posts except for `post` post type.
//		$delete_options = array(
//			'meta_key'            => 'plugin_name',
//			'meta_value'          => 'bulk_delete',
//			'meta_compare'        => '=',
//			'no_posts'            => true,
//			'no_posts_post_types' => 'post',
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that user meta has one $user1 and $user2 is deleted.
//		$users_in_meta = get_users( array(
//			'meta_key'     => 'plugin_name',
//			'meta_value'   => 'bulk_delete',
//			'meta_compare' => '=',
//		) );
//
//		$this->assertEquals( array( $this->subscriber ), wp_list_pluck( $users_in_meta, 'ID' ) );
//	}
//
//	public function test_that_users_deleted_when_user_meta_value_equals_to_and_in_batches() {
//		$meta_key     = 'plugin_name';
//		$meta_value   = 'bulk_delete';
//		$meta_compare = '=';
//
//		// Update user meta.
//		update_user_meta( $this->subscriber, $meta_key, $meta_value );
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, $meta_key, $meta_value );
//
//		// Assert that user meta has two users.
//		$users_with_meta = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//		$this->assertEquals( 2, count( $users_with_meta ) );
//
//		// Delete the 1st set of Users.
//		$delete_options = array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//			'limit_to'     => 1,
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that user meta has no user.
//		$users_remaining_after_1st_batch = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//		$this->assertEquals( 1, count( $users_remaining_after_1st_batch ) );
//
//		// Delete the 2nd set of Users.
//		$this->module->delete( $delete_options );
//
//		// Assert that user meta has no user.
//		$users_remaining_after_2nd_batch = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//		$this->assertEquals( 0, count( $users_remaining_after_2nd_batch ) );
//	}
//
//	/**
//	 * Test case for user deletion when the given value not equals to the meta value.
//	 */
//	public function test_that_users_deleted_when_user_meta_value_not_equals_to_and_no_filters_set() {
//		$meta_key     = 'plugin_name';
//		$meta_value   = 'bulk_delete';
//		$meta_compare = '!=';
//
//		// Update user meta.
//		update_user_meta( $this->subscriber, $meta_key, $meta_value );
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, $meta_key, 'my_awesome_plugin' );
//
//		// Assert that two users have the same meta key.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//		$this->assertEquals( 2, count( $users_with_same_meta_key ) );
//
//		// call our method.
//		$delete_options = array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that one of the users is deleted.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//
//		$this->assertEquals( 1, count( $users_with_same_meta_key ) );
//	}
//
//	/**
//	 * Test case for deleting users when user meta not equals to and user registration filter set.
//	 */
//	public function test_that_users_deleted_when_user_meta_value_not_equals_to_and_user_registration_filter_fulfilled(
//	) {
//		$meta_key     = 'plugin_name';
//		$meta_value   = 'bulk_delete';
//		$meta_compare = '!=';
//
//		// Update user meta.
//		update_user_meta( $this->subscriber, $meta_key, $meta_value );
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, $meta_key, 'my_awesome_plugin' );
//		update_user_meta( $this->second_subscriber, $meta_key, 'my_second_plugin' );
//
//		// Assert that two users have the same meta key.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//		$this->assertEquals( 3, count( $users_with_same_meta_key ) );
//
//		// call our method.
//		$delete_options = array(
//			'meta_key'            => $meta_key,
//			'meta_value'          => $meta_value,
//			'meta_compare'        => $meta_compare,
//			'registered_restrict' => true,
//			'registered_days'     => 1,
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that one of the users is deleted.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//
//		$this->assertEquals( 2, count( $users_with_same_meta_key ) );
//	}
//
//	/**
//	 * Test case for not deleting users when user registration filter is not fulfilled.
//	 */
//	public function
//	test_that_users_not_deleted_when_user_meta_value_not_equals_to_and_user_registration_filter_not_fulfilled() {
//		$meta_key     = 'plugin_name';
//		$meta_value   = 'bulk_delete';
//		$meta_compare = '!=';
//
//		// Update user meta.
//		update_user_meta( $this->subscriber, $meta_key, $meta_value );
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, $meta_key, 'my_awesome_plugin' );
//		update_user_meta( $this->second_subscriber, $meta_key, 'my_second_plugin' );
//
//		// Assert that two users have the same meta key.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//		$this->assertEquals( 3, count( $users_with_same_meta_key ) );
//
//		// call our method.
//		$delete_options = array(
//			'meta_key'            => $meta_key,
//			'meta_value'          => $meta_value,
//			'meta_compare'        => $meta_compare,
//			'registered_restrict' => true,
//			'registered_days'     => 4,
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that one of the users is deleted.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//
//		$this->assertEquals( 3, count( $users_with_same_meta_key ) );
//	}
//
//	/**
//	 * Test case of delete users by meta with filter set post type.
//	 */
//	public function test_that_users_deleted_when_user_meta_value_not_equals_to_and_restricted_by_post_type_filter() {
//		$meta_key     = 'plugin_name';
//		$meta_value   = 'bulk_delete';
//		$meta_compare = '!=';
//
//		// Update user meta.
//		update_user_meta( $this->subscriber, $meta_key, $meta_value );
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, $meta_key, 'my_awesome_plugin' );
//		update_user_meta( $this->second_subscriber, $meta_key, 'my_second_plugin' );
//
//		// Create post and assign author.
//		$this->factory->post->create( array(
//			'post_title'  => 'A sample Post',
//			'post_author' => $this->subscriber,
//		) );
//
//		$this->factory->post->create( array(
//			'post_title'  => 'Another sample Post',
//			'post_author' => $this->second_subscriber,
//		) );
//
//		// Create Page and assign author.
//		$this->factory->post->create( array(
//			'post_title'  => 'A sample Page',
//			'post_type'   => 'page',
//			'post_author' => $this->subscriber_with_two_days_old_registration_date,
//		) );
//
//		// Assert that three users have the same meta key.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//
//		$this->assertEquals( array(
//			$this->subscriber,
//			$this->subscriber_with_two_days_old_registration_date,
//			$this->second_subscriber,
//		),
//			wp_list_pluck( $users_with_same_meta_key, 'ID' )
//		);
//
//		// call our method.
//		$delete_options = array(
//			'meta_key'            => $meta_key,
//			'meta_value'          => $meta_value,
//			'meta_compare'        => $meta_compare,
//			'no_posts'            => true,
//			'no_posts_post_types' => 'post',
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that two of the users is deleted.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//
//		$this->assertEquals(
//			array( $this->second_subscriber ),
//			wp_list_pluck( $users_with_same_meta_key, 'ID' )
//		);
//	}
//
//	/**
//	 * Test case of delete users by meta and in batches.
//	 */
//	public function test_that_users_deleted_when_user_meta_value_not_equals_to_and_in_batches() {
//		$meta_key     = 'plugin_name';
//		$meta_value   = 'bulk_delete';
//		$meta_compare = '!=';
//
//		// Update user meta.
//		update_user_meta( $this->subscriber, $meta_key, $meta_value );
//		update_user_meta( $this->subscriber_with_two_days_old_registration_date, $meta_key, 'my_awesome_plugin' );
//		update_user_meta( $this->second_subscriber, $meta_key, 'my_second_plugin' );
//
//		// Assert that user meta has two users.
//		$users_with_same_meta_key = get_users( array(
//			'meta_key' => $meta_key,
//		) );
//		$this->assertEquals( array(
//			$this->subscriber,
//			$this->subscriber_with_two_days_old_registration_date,
//			$this->second_subscriber,
//		),
//			wp_list_pluck( $users_with_same_meta_key, 'ID' )
//		);
//
//		// Delete the 1st set of Users.
//		$delete_options = array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//			'limit_to'     => 1,
//		);
//
//		$delete_options = wp_parse_args( $delete_options, $this->common_filter_defaults );
//		$this->module->delete( $delete_options );
//
//		// Assert that user meta has one user with no matching meta value.
//		$users_remaining_after_1st_batch = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//		$this->assertEquals( 1, count( $users_remaining_after_1st_batch ) );
//
//		// Delete the 2nd set of Users.
//		$this->module->delete( $delete_options );
//
//		// Assert that both Users with non matching meta value are deleted.
//		$users_remaining_after_2nd_batch = get_users( array(
//			'meta_key'     => $meta_key,
//			'meta_value'   => $meta_value,
//			'meta_compare' => $meta_compare,
//		) );
//		$this->assertEquals( 0, count( $users_remaining_after_2nd_batch ) );
//	}
//
//	public function test_that_users_deleted_when_user_meta_value_greater_than_and_no_filters_set() {
//
//	}
//
//	public function test_that_users_deleted_when_user_meta_value_greater_than_and_user_registration_filter_fulfilled() {
//
//	}
//
//	public function test_that_users_deleted_when_user_meta_value_greater_than_and_user_registration_filter_not_fulfilled(
//	) {
//
//	}
//
//	public function test_that_users_deleted_when_user_meta_value_greater_than_and_restricted_by_post_type_filter() {
//
//	}
//
//	public function test_that_users_deleted_when_user_meta_value_greater_than_and_in_batches() {
//
//	}
}
