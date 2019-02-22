<?php

namespace BulkWP\BulkDelete\Core\Users\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of users by user role.
 *
 * Tests \BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserRoleModule
 *
 * @since 6.0.0
 */
class DeleteUsersByUserRoleModuleInOldFormatTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserRoleModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeleteUsersByUserRoleModule();
	}

	/**
	 * Test basic case of delete users by role.
	 */
	public function test_delete_users_by_role_without_filters() {
		// Create one user and assign to subscriber role.
		$this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber' ) );

		// Assert that user role has one user.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 1, count( $subscribers ) );

		// call our method.
		$delete_options = array(
			'selected_roles'      => array( 'subscriber' ),
			'limit_to'            => false,
			'registered_restrict' => false,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->module->delete( $delete_options );

		// Assert that user role has no user.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 0, count( $subscribers ) );
	}

	/**
	 * Test case of delete users by role with filter set registered day at least two days.
	 */
	public function test_delete_users_by_role_with_filter_set_registered_day_at_least_two_days() {
		// Set registered date.
		$day_past = date( 'Y-m-d', strtotime( '-2 day' ) );

		// Create one user and assign to subscriber role.
		$this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber', 'user_registered' => $day_past ) );

		// Assert that user role has one user.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 1, count( $subscribers ) );

		// call our method.
		$delete_options = array(
			'selected_roles'      => array( 'subscriber' ),
			'limit_to'            => false,
			'registered_restrict' => true,
			'registered_days'     => 3,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->module->delete( $delete_options );

		// Assert that user role has one user.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 1, count( $subscribers ) );
	}

	/**
	 * Test case of delete users by role with filter set registered day at least one days.
	 */
	public function test_delete_users_by_role_with_filter_set_registered_day_at_least_one_days() {
		// Set registered date.
		$day_past = date( 'Y-m-d', strtotime( '-2 day' ) );

		// Create one user and assign to subscriber role.
		$this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber', 'user_registered' => $day_past ) );

		// Assert that user role has one user.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 1, count( $subscribers ) );

		// call our method.
		$delete_options = array(
			'selected_roles'      => array( 'subscriber' ),
			'limit_to'            => false,
			'registered_restrict' => true,
			'registered_days'     => 1,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->module->delete( $delete_options );

		// Assert that user role has no user.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 0, count( $subscribers ) );
	}

	/**
	 * Test case of delete users by role with filter set post type.
	 */
	public function test_delete_users_by_role_with_filter_set_post_type() {
		// Create two user and assign to subscriber role.
		$user1 = $this->factory->user->create( array( 'user_login' => 'user_test1', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber' ) );
		$user2 = $this->factory->user->create( array( 'user_login' => 'user_test2', 'user_pass' => 'ZXC987abc2', 'role' => 'subscriber' ) );

		// Create post and assign author.
		$post = $this->factory->post->create( array( 'post_title' => 'post1', 'post_author' => $user1 ) );
		$page = $this->factory->post->create( array( 'post_title' => 'page1', 'post_type' => 'page', 'post_author' => $user2 ) );

		// Assert that user role has two users $user1 and $user2.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( array( $user1, $user2 ), wp_list_pluck( $subscribers, 'ID' ) );

		// call our method.
		$delete_options = array(
			'selected_roles'      => array( 'subscriber' ),
			'limit_to'            => false,
			'registered_restrict' => false,
			'login_restrict'      => false,
			'no_posts'            => true,
			'no_posts_post_types' => 'post',
		);
		$this->module->delete( $delete_options );

		// Assert that user role has one $user1 and $user2 is deleted.
		$subscribers = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( array( $user1 ), wp_list_pluck( $subscribers, 'ID' ) );
	}
}
