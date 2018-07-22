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
	protected $subscriber_with_past_registration_date;

	public function setUp() {
		parent::setUp();

		$this->module = new DeleteUsersByUserMetaModule();

		$user_role = 'subscriber';

		// Create one user with role as `subscriber`.
		$this->subscriber = $this->factory->user->create( array(
			'role' => $user_role,
		) );

		// Set registered date.
		$days_past = date( 'Y-m-d', strtotime( '-2 day' ) );

		// Create one user with role as `subscriber` and set registration date older than the current date.
		$this->subscriber_with_past_registration_date = $this->factory->user->create( array(
			'role'            => $user_role,
			'user_registered' => $days_past,
		) );
	}

	public function tearDown() {
		wp_delete_user( $this->subscriber );
		wp_delete_user( $this->subscriber_with_past_registration_date );
	}

	/**
	 * Test basic case of delete users by user meta.
	 */
	public function test_that_users_can_be_deleted_with_no_filters_set() {
		$meta_key     = 'plugin_name';
		$meta_value   = 'bulk_delete';
		$meta_compare = '=';

		// Update user meta.
		update_user_meta( $this->subscriber, $meta_key, $meta_value );

		// Assert that user meta has one user.
		$users_with_meta = get_users( array(
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => $meta_compare,
		) );
		$this->assertEquals( 1, count( $users_with_meta ) );

		// call our method.
		$delete_options = array(
			'meta_key'            => $meta_key,
			'meta_value'          => $meta_value,
			'meta_compare'        => $meta_compare,
			'limit_to'            => false,
			'registered_restrict' => false,
			'registered_days'     => false,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->module->delete( $delete_options );

		// Assert that user meta has no user.
		$users_with_meta = get_users( array(
			'meta_key'     => $meta_key,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => $meta_compare,
		) );
		$this->assertEquals( 0, count( $users_with_meta ) );
	}

	/**
	 * Test case for deleting users by user meta with user registration filter set.
	 */
	public function test_that_users_can_be_deleted_with_user_registration_filter() {
		$meta_key     = 'plugin_name';
		$meta_value   = 'bulk_delete';
		$meta_compare = '=';

		// Update user meta.
		update_user_meta( $this->subscriber_with_past_registration_date, 'plugin_name', 'bulk_delete' );

		// Assert that user meta has one user.
		$users_with_meta = get_users( array(
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => $meta_compare,
		) );
		$this->assertEquals( 1, count( $users_with_meta ) );

		// call our method.
		$delete_options = array(
			'meta_key'            => $meta_key,
			'meta_value'          => $meta_value,
			'meta_compare'        => $meta_compare,
			'registered_restrict' => true,
			'registered_days'     => 1,
			'limit_to'            => false,
			'login_restrict'      => false,
		);
		$this->module->delete( $delete_options );

		// Assert that user meta has one user.
		$users_with_meta = get_users( array(
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_compare' => $meta_compare,
		) );
		$this->assertEquals( 0, count( $users_with_meta ) );
	}

	/**
	 * Test case for not deleting users when user registration filter is not fulfilled.
	 */
	public function test_that_user_is_not_deleted_when_user_registration_filter_is_not_fulfilled() {
		// Update user meta.
		update_user_meta( $this->subscriber_with_past_registration_date, 'plugin_name', 'bulk_delete' );

		// Assert that user meta has one user.
		$users_in_meta = get_users( array(
			'meta_key'     => 'plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );
		$this->assertEquals( 1, count( $users_in_meta ) );

		// call our method.
		$delete_options = array(
			'meta_key'            => 'plugin_name',
			'meta_value'          => 'bulk_delete',
			'meta_compare'        => '=',
			'limit_to'            => false,
			'registered_restrict' => true,
			'registered_days'     => 4,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->module->delete( $delete_options );

		// Assert that user meta has no user.
		$users_in_meta = get_users( array(
			'meta_key'     => 'plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );
		$this->assertEquals( 1, count( $users_in_meta ) );
	}

	/**
	 * Test case of delete users by meta with filter set post type.
	 */
	public function test_that_users_can_be_deleted_when_restricted_by_post_type_filter() {
		// Update user meta.
		update_user_meta( $this->subscriber, 'plugin_name', 'bulk_delete' );
		update_user_meta( $this->subscriber_with_past_registration_date, 'plugin_name', 'bulk_delete' );

		// Create post and assign author.
		$this->factory->post->create( array(
			'post_title'  => 'post1',
			'post_author' => $this->subscriber,
		) );
		$this->factory->post->create( array(
			'post_title'  => 'page1',
			'post_type'   => 'page',
			'post_author' =>
				$this->subscriber_with_past_registration_date,
		) );

		// Assert that user meta has two users $user1 and $user2.
		$users_with_meta = get_users( array(
			'meta_key'   => 'plugin_name',
			'meta_value' => 'bulk_delete',
			'meta_compare'
			             => '=',
		) );

		$this->assertEquals( array( $this->subscriber, $this->subscriber_with_past_registration_date ),
			wp_list_pluck( $users_with_meta, 'ID' ) );

		// Delete Users who do not have any posts except for `post` post type.
		$delete_options = array(
			'meta_key'            => 'plugin_name',
			'meta_value'          => 'bulk_delete',
			'meta_compare'        => '=',
			'limit_to'            => false,
			'registered_restrict' => false,
			'login_restrict'      => false,
			'no_posts'            => true,
			'no_posts_post_types' => 'post',
		);
		$this->module->delete( $delete_options );

		// Assert that user meta has one $user1 and $user2 is deleted.
		$users_in_meta = get_users( array(
			'meta_key'     => 'plugin_name',
			'meta_value'   => 'bulk_delete',
			'meta_compare' => '=',
		) );

		$this->assertEquals( array( $this->subscriber ), wp_list_pluck( $users_in_meta, 'ID' ) );
	}
}
