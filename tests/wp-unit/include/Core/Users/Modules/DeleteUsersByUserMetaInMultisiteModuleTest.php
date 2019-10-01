<?php

namespace BulkWP\BulkDelete\Core\Users\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

if ( ! is_multisite() ) {
	return;
}

/**
 * Test Deletion of users by user meta in multisite.
 *
 * Tests \BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaInMultisiteModule
 *
 * @since 6.1.0
 */
class DeleteUsersByUserMetaInMultisiteModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaInMultisiteModule
	 */
	protected $module;

	/**
	 * Blog IDs array.
	 *
	 * @var array
	 */
	protected $site_ids;

	public function setUp() {
		parent::setUp();

		$this->site_ids = $this->factory->blog->create_many( 2 );
		$this->module   = new DeleteUsersByUserMetaInMultisiteModule();
	}

	/**
	 * Data provider to test exclusion of logged in users.
	 *
	 * @see test_delete_users_by_user_meta_in_multisite
	 *
	 * @return array Data array.
	 */
	public function provide_data_to_test_exclusion_of_logged_in_users_in_multisite() {
		return array(
			// (+ve Case) Delete Users with a specified user meta. User from the admin user role is logged in.
			array(
				array(
					array(
						'role'                            => 'administrator',
						'no_of_users'                     => 5,
						'registered_date'                 => date( 'Y-m-d' ),
						'user_in_this_group_is_logged_in' => true,
						'meta_key'                        => 'fruit',
						'meta_value'                      => 'apple',
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'apple',
					),
				),
				array(
					'meta_key'            => 'fruit',
					'meta_compare'        => '=',
					'meta_value'          => 'apple',
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 14,
					'available_users' => 2, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Tests whether blogs are created.
	 *
	 * @return void
	 */
	public function test_multisite_setup() {
		$blogs = get_sites( array( 'count' => true ) );
		$this->assertEquals( 3, $blogs );
	}

	/**
	 * Test basic case of delete users by meta in mutisite.
	 *
	 * @dataProvider provide_data_to_test_exclusion_of_logged_in_users_in_multisite
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
				add_user_to_blog( $this->site_ids[ $i ], $user_id, $setup[ $i ]['role'] );
				add_user_meta( $user_id, $setup [ $i ] ['meta_key'], $setup [ $i ] ['meta_value'] );
			}

			if ( array_key_exists( 'user_in_this_group_is_logged_in', $setup [ $i ] ) ) {
				$this->login_user( $user_ids[0] );
			}
		}

		// Assert whether users are created in appropriate blogs.
		for ( $i = 0; $i < $size; $i++ ) {
			$users = get_users(
				array(
					'blog_id' => $this->site_ids[ $i ],
					'fields'  => 'ID',
				)
			);
			$this->assertEquals( $setup[ $i ] ['no_of_users'], count( $users ) );
		}

		// call our method.
		$delete_options = $user_operations;
		$deleted_users  = $this->module->delete( $delete_options );

		$this->assertEquals( $expected['deleted_users'], $deleted_users );

		$available_users = get_users(
			array(
				'fields'  => 'ID',
				'blog_id' => 0,
			)
		);
		$this->assertEquals( $expected['available_users'], count( $available_users ) );
	}
}
