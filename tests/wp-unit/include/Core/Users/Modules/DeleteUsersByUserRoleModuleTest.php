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
	 * Data provider to test delete users without filter.
	 */
	public function provide_data_to_test_delete_users() {
		return array(
			// (-ve Case) Delete Users with no specified role.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
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
					'available_users' => 16, // Including 1 default admin user.
				),
			),
			// (+ve Case) Delete Users with a specified role.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
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
					'available_users' => 6, // Including 1 default admin user.
				),
			),
			// (-ve Case) Delete Users with a specified role.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
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
					'available_users' => 16, // Including 1 default admin user.
				),
			),
			// (-ve Case) Delete Users from multiple roles.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'author', 'contributor' ),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 0,
					'available_users' => 16, // Including 1 default admin user.
				),
			),
			// (+ve Case) Delete Users from multiple roles( one role has x users and other role has no users).
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'author', 'editor' ),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 5,
					'available_users' => 11, // Including 1 default admin user.
				),
			),
			// (+ve Case) Delete Users from multiple roles( both roles have x users ).
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'author',
						'no_of_users'     => 3,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'subscriber', 'editor' ),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 15,
					'available_users' => 4, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Data provider to test delete users with user registration filter.
	 */
	public function provide_data_to_test_delete_users_with_registration_filter() {
		return array(
			// (+ve Case) Delete Users with specified role and before filter(old format).
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d', strtotime( '-5 day' ) ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'editor',
						'no_of_users'     => 3,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'editor' ),
					'limit_to'            => 0,
					'registered_restrict' => true,
					'registered_days'     => 2,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 5,
					'available_users' => 14, // Including 1 default admin user.
				),
			),
			// (+ve Case) Delete Users with specified role and after filter.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d', strtotime( '-5 day' ) ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d', strtotime( '-3 day' ) ),
					),
					array(
						'role'            => 'editor',
						'no_of_users'     => 3,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'editor', 'subscriber' ),
					'limit_to'            => 0,
					'registered_restrict' => true,
					'registered_date_op'  => 'after',
					'registered_days'     => 2,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 3,
					'available_users' => 16, // Including 1 default admin user.
				),
			),
			// (+ve Case) Delete Users with specified role and before filter.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d', strtotime( '-5 day' ) ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d', strtotime( '-3 day' ) ),
					),
					array(
						'role'            => 'editor',
						'no_of_users'     => 3,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'editor', 'subscriber' ),
					'limit_to'            => 0,
					'registered_restrict' => true,
					'registered_date_op'  => 'before',
					'registered_days'     => 2,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 15,
					'available_users' => 4, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Data provider to test exclusion of logged in users.
	 *
	 * @see test_delete_users_by_role
	 *
	 * @return array Data array.
	 */
	public function provide_data_to_test_exclusion_of_logged_in_users() {
		return array(
			// (+ve Case) Delete Users with a specified role. User from the admin user role is logged in.
			array(
				array(
					array(
						'role'                            => 'administrator',
						'no_of_users'                     => 5,
						'registered_date'                 => date( 'Y-m-d' ),
						'user_in_this_group_is_logged_in' => true,
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'administrator' ),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 5, // Including 1 default admin user.
					'available_users' => 11,
				),
			),
			// (+ve Case) Delete Users with a specified role. User from the user role that is getting deleted is logged in.
			array(
				array(
					array(
						'role'                            => 'editor',
						'no_of_users'                     => 5,
						'registered_date'                 => date( 'Y-m-d' ),
						'user_in_this_group_is_logged_in' => true,
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'      => array( 'editor' ),
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 4,
					'available_users' => 12, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Provide data for testing deletion of users without any role.
	 *
	 * @see test_delete_users_by_role
	 *
	 * @return array Test data.
	 */
	public function provide_data_for_testing_users_with_no_role() {
		return array(
			// Delete Users with no user role alone.
			array(
				array(
					array(
						'role'            => '',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'            => array(),
					'delete_users_with_no_role' => true,
					'limit_to'                  => 0,
					'registered_restrict'       => false,
					'login_restrict'            => false,
					'no_posts'                  => false,
				),
				array(
					'deleted_users'   => 5,
					'available_users' => 11, // Including 1 default admin user.
				),
			),
			// Delete Users with no user role along with other users.
			array(
				array(
					array(
						'role'            => '',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
					),
				),
				array(
					'selected_roles'            => array( 'subscriber' ),
					'delete_users_with_no_role' => true,
					'limit_to'                  => 0,
					'registered_restrict'       => false,
					'login_restrict'            => false,
					'no_posts'                  => false,
				),
				array(
					'deleted_users'   => 15,
					'available_users' => 1, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Test basic case of delete users by role.
	 *
	 * @dataProvider provide_data_to_test_delete_users
	 * @dataProvider provide_data_to_test_delete_users_with_registration_filter
	 * @dataProvider provide_data_to_test_exclusion_of_logged_in_users
	 * @dataProvider provide_data_for_testing_users_with_no_role
	 *
	 * @param array $setup           Create posts using supplied arguments.
	 * @param array $user_operations User operations.
	 * @param array $expected        Expected output for respective operations.
	 */
	public function test_delete_users_by_role( $setup, $user_operations, $expected ) {
		$size = count( $setup );

		// Create users and assign to specified role.
		for ( $i = 0; $i < $size; $i++ ) {
			$args     = array( 'user_registered' => $setup[ $i ]['registered_date'] );
			$user_ids = $this->factory->user->create_many( $setup[ $i ]['no_of_users'], $args );

			foreach ( $user_ids as $user_id ) {
				$this->assign_role_by_user_id( $user_id, $setup[ $i ]['role'] );
			}

			if ( array_key_exists( 'user_in_this_group_is_logged_in', $setup [ $i ] ) ) {
				$this->login_user( $user_ids[0] );
			}
		}

		// call our method.
		$delete_options = $user_operations;
		$deleted_users  = $this->module->delete( $delete_options );

		$this->assertEquals( $expected['deleted_users'], $deleted_users );

		$available_users = get_users();
		$this->assertEquals( $expected['available_users'], count( $available_users ) );
	}
}
