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

		// Assert that user meta has one user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 1, count( $users_in_meta ) );

		// call our method.
		$delete_options = array(
			'meta_key'            => 'first_name',
			'meta_value'          => 'bulk_delete',
			'meta_compare'        => '=',
			'limit_to'            => false,
			'registered_restrict' => false,
			'registered_days'     => false,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->delete_by_user_meta->delete( $delete_options );

		// Assert that user meta has no user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 0, count( $users_in_meta ) );
	}

	/**
	 * Test case of delete users by user meta with filter set registered day at least two days.
	 */
	public function test_delete_users_by_meta_with_filter_set_registered_day_at_least_two_days() {
		//Set registered date
		$day_past = date('Y-m-d', strtotime('-2 day'));

		//Create one user and set meta and registered date
		$user = $this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber', 'first_name' => 'bulk_delete', 'user_registered' => $day_past ) );

		// Assert that user meta has one user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 1, count( $users_in_meta ) );

		// call our method.
		$delete_options = array(
			'meta_key'            => 'first_name',
			'meta_value'          => 'bulk_delete',
			'meta_compare'        => '=',
			'limit_to'            => false,
			'registered_restrict' => true,
			'registered_days'     => 3,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->delete_by_user_meta->delete( $delete_options );

		// Assert that user meta has one user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 1, count( $users_in_meta ) );
	}

	/**
	 * Test case of delete users by user meta with filter set registered day at least two days.
	 */
	public function test_delete_users_by_meta_with_filter_set_registered_day_at_least_one_day() {
		//Set registered date
		$day_past = date('Y-m-d', strtotime('-2 day'));

		//Create one user and set meta and registered date
		$user = $this->factory->user->create( array( 'user_login' => 'user_test', 'user_pass' => 'ZXC987abc', 'role' => 'subscriber', 'first_name' => 'bulk_delete', 'user_registered' => $day_past ) );

		// Assert that user meta has one user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 1, count( $users_in_meta ) );

		// call our method.
		$delete_options = array(
			'meta_key'            => 'first_name',
			'meta_value'          => 'bulk_delete',
			'meta_compare'        => '=',
			'limit_to'            => false,
			'registered_restrict' => true,
			'registered_days'     => 1,
			'login_restrict'      => false,
			'no_posts'            => false,
		);
		$this->delete_by_user_meta->delete( $delete_options );

		// Assert that user meta has no user.
		$users_in_meta = get_users( array( 'meta_key' => 'first_name', 'meta_value' => 'bulk_delete', 'meta_compare' => '=' ) );
		$this->assertEquals( 0, count( $users_in_meta ) );
	}
}
