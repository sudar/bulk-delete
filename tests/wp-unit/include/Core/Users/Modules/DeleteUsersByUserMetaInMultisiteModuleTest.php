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

		$this->site_ids = $this->factory->blog->create_many( 3 );
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
	 * Data provider to test registration filter.
	 *
	 * @see test_delete_users_by_user_meta_in_multisite
	 *
	 * @return array Data array.
	 */
	public function provide_data_to_test_registration_filter_in_multisite() {
		return array(
			// (+ve Case) Delete Users with a specified user meta and after filter.
			array(
				array(
					array(
						'role'            => 'administrator',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'apple',
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d', strtotime( '-2 day' ) ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'apple',
					),
					array(
						'role'            => 'author',
						'no_of_users'     => 3,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'mango',
					),
				),
				array(
					'meta_key'            => 'fruit',
					'meta_compare'        => '=',
					'meta_value'          => 'apple',
					'limit_to'            => 0,
					'registered_restrict' => true,
					'registered_date_op'  => 'after',
					'registered_days'     => 1,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 5,
					'available_users' => 14, // Including 1 default admin user.
				),
			),
			// (+ve Case) Delete Users with not equal to operator and after filter.
			array(
				array(
					array(
						'role'            => 'administrator',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'apple',
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d', strtotime( '-2 day' ) ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'apple',
					),
					array(
						'role'            => 'author',
						'no_of_users'     => 3,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'mango',
					),
				),
				array(
					'meta_key'            => 'fruit',
					'meta_compare'        => '!=',
					'meta_value'          => 'orange',
					'limit_to'            => 0,
					'registered_restrict' => true,
					'registered_date_op'  => 'before',
					'registered_days'     => 1,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 10,
					'available_users' => 9, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Data provider to test users deletion with ENDS WITH operator and limit filter.
	 *
	 * @see test_delete_users_by_user_meta_in_multisite
	 *
	 * @return array Data array.
	 */
	public function provide_data_to_test_limit_filter_in_multisite() {
		return array(
			// (+ve Case) Delete Users with ENDS WITH operator and limit filter.
			array(
				array(
					array(
						'role'            => 'administrator',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'apple',
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'pineapple',
					),
				),
				array(
					'meta_key'            => 'fruit',
					'meta_compare'        => 'REGEXP',
					'meta_value'          => 'apple$',
					'limit_to'            => 10,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 10,
					'available_users' => 6, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Data provider to test users deletion with no posts filter.
	 *
	 * @see test_delete_users_by_user_meta_in_multisite
	 *
	 * @return array Data array.
	 */
	public function provide_data_to_test_no_posts_filter_in_multisite() {
		return array(
			// (+ve Case) Delete Users with a specified user meta and no posts filter.
			array(
				array(
					array(
						'role'            => 'administrator',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'apple',
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
						'meta_key'        => 'fruit',
						'meta_value'      => 'pineapple',
						'has_posts'       => true,
					),
				),
				array(
					'meta_key'            => 'fruit',
					'meta_compare'        => 'LIKE',
					'meta_value'          => 'apple',
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => true,
					'no_posts_post_types' => array( 'post' ),
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
		$this->assertEquals( 4, $blogs );
	}

	/**
	 * Test basic case of delete users by meta in mutisite.
	 *
	 * @dataProvider provide_data_to_test_exclusion_of_logged_in_users_in_multisite
	 * @dataProvider provide_data_to_test_registration_filter_in_multisite
	 * @dataProvider provide_data_to_test_limit_filter_in_multisite
	 * @dataProvider provide_data_to_test_no_posts_filter_in_multisite
	 *
	 * @param array $setup           Create posts using supplied arguments.
	 * @param array $user_operations User operations.
	 * @param array $expected        Expected output for respective operations.
	 */
	public function test_delete_users_by_role( $setup, $user_operations, $expected ) {
		$size = count( $setup );

		// Create users and assign to specific blog and role.
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
			if ( array_key_exists( 'has_posts', $setup [ $i ] ) ) {
				$this->factory->post->create( array( 'post_author' => $user_ids[0] ) );
			}
		}

		// Assert users are created in appropriate blogs.
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
