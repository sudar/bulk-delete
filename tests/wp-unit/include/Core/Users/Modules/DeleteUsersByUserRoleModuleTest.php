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
class DeleteUsersByUserRoleModuleTest extends WPCoreUnitTestCase {
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
	 * Data provider to test delete users.
	 */
	public function provide_data_to_test_delete_users() {
		return array(
			// (-ve Case) Delete Users with no specified role without any filter.
			array(
				array(
					'user_roles'  => array( 'editor', 'subscriber' ),
					'no_of_users' => array( 5, 10 ),
				),
				array(
					'selected_roles'      => array(),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 0,
					'available_users' => 15,
				),
			),
			// (+ve Case) Delete Users with specified role without any filter.
			array(
				array(
					'user_roles'  => array( 'editor', 'subscriber' ),
					'no_of_users' => array( 5, 10 ),
				),
				array(
					'selected_roles'      => array( 'subscriber' ),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 10,
					'available_users' => 5,
				),
			),
			// (-ve Case) Delete Users with specified role without any filter.
			array(
				array(
					'user_roles'  => array( 'editor', 'subscriber' ),
					'no_of_users' => array( 5, 10 ),
				),
				array(
					'selected_roles'      => array( 'author' ),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 0,
					'available_users' => 15,
				),
			),
		);
	}

	/**
	 * Test basic case of delete users by role.
	 *
	 * @dataProvider provide_data_to_test_delete_users
	 *
	 * @param array $setup           Create posts using supplied arguments.
	 * @param array $user_operations User operations.
	 * @param array $expected        Expected output for respective operations.
	 */
	public function test_delete_users_by_role_without_filters( $setup, $user_operations, $expected ) {
		$no_of_users = $setup['no_of_users'];
		$user_roles  = $setup['user_roles'];
		$size        = count( $user_roles );

		// Create users and assign to specified role.
		for ( $i = 0; $i < $size; $i++ ) {
			$user_ids = $this->factory->user->create_many( $no_of_users[ $i ] );

			foreach ( $user_ids as $user_id ) {
				$this->assign_role_by_user_id( $user_id, $user_roles[ $i ] );
			}

			$users = get_users( array( 'role' => $user_roles[ $i ] ) );
			$this->assertEquals( $no_of_users[ $i ], count( $users ) );
		}

		// call our method.
		$delete_options = $user_operations;
		$deleted_users  = $this->module->delete( $delete_options );

		$this->assertEquals( $expected['deleted_users'], $deleted_users );

		// Assert that user role has no user.
		$selected_role_users = get_users( array( 'role' => $user_operations['selected_roles'] ) );
		$this->assertEquals( 0, count( $selected_role_users ) );

		$available_users = get_users();
		$this->assertEquals( $expected['available_users'] + 1, count( $available_users ) );
	}
}
