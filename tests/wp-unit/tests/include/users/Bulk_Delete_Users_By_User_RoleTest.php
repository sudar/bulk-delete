<?php

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Sample test case.
 */
class Bulk_Delete_Users_By_User_RoleTest extends WPCoreUnitTestCase {

	/**
	 * A single example test.
	 */
	protected $delete_by_user_role;

	public function setUp() {
		parent::setUp();

		$this->delete_by_user_role = new \Bulk_Delete_Users_By_User_Role();
	}

	/**
	 * Test basic case of delete users by role.
	 */
	public function test_delete_users_by_role_without_filters() {
		//Create one user and assign to subscriber role
		$user = $this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber' ) );

		// Assert that user role has one user.
		$users_in_role = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 1, count( $users_in_role ) );

		// call our method.
		$delete_options = array(
			'selected_roles'      => array( 'subscriber' ),
			'limit_to'            => false,
			'registered_restrict' => false,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->delete_by_user_role->delete( $delete_options );

		// Assert that user role has no user.
		$users_in_role = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 0, count( $users_in_role ) );
	}

	/**
	 * Test case of delete users by role with filter set registered day at least two days.
	 */
	public function test_delete_users_by_role_with_filter_set_registered_day_at_least_two_days() {
		//Set registered date
		$day_past = date('Y-m-d', strtotime('-2 day'));

		//Create one user and assign to subscriber role
		$user = $this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber', 'user_registered' => $day_past ) );

		// Assert that user role has one user.
		$users_in_role = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 1, count( $users_in_role ) );

		// call our method.
		$delete_options = array(
			'selected_roles'      => array( 'subscriber' ),
			'limit_to'            => 0,
			'registered_restrict' => true,
			'registered_days'     => 3,
			'login_restrict'      => 0,
			'no_posts'            => false,
		);
		$this->delete_by_user_role->delete( $delete_options );

		// Assert that user role has no user.
		$users_in_role = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 1, count( $users_in_role ) );
	}
}