<?php

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Sample test case.
 */
class Bulk_Delete_Users_By_User_MetaTest extends WPCoreUnitTestCase {

	/**
	 * A single example test.
	 */
	protected $delete_by_user_meta;

	public function setUp() {
		parent::setUp();

		$this->delete_by_user_meta = new \Bulk_Delete_Users_By_User_Meta();
	}

	/**
	 * Test basic case of delete users by meta.
	 */
	public function test_delete_users_by_meta_without_filters() {
		//Create one user and set first_name meta to bulk_delete
		$user = $this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber', 'first_name' => 'bulk_delete' ) );

		// Assert that user meta field first_name has one user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 1, count( $users_in_meta ) );

		// call our method.
		$delete_options = array(
			'meta_key'            => 'first_name',
			'meta_value'          => 'bulk_delete',
			'meta_compare'        => '=',
			'limit_to'            => false,
			'registered_restrict' => false,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->delete_by_user_meta->delete( $delete_options );

		// Assert that user role has no user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 0, count( $users_in_meta ) );
	}
}
