<?php

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Sample test case.
 */
class BulkDeleteTest extends WPCoreUnitTestCase {

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
			'selected_roles' => 'subscriber',
		);
		$this->delete_by_user_role->delete( $delete_options );

		// Assert that user role has no user.
		$users_in_role = get_users( array( 'role' => 'subscriber' ) );

		$this->assertEquals( 0, count( $users_in_role ) );
	}
}