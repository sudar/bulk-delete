<?php

namespace BulkWP\BulkDelete\Core\Users;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test common functions that are used for deleting users.
 *
 * Tests UsersModule
 *
 * @since 6.0.0
 */
class UsersModuleTest extends WPCoreUnitTestCase {
	/**
	 * The class that is getting tested.
	 *
	 * @var string
	 */
	protected $class_name = 'BulkWP\\BulkDelete\Core\\Users\\UsersModule';

	/**
	 * Test that posts can be reassigned to a new user.
	 */
	public function test_reassign_user() {
		$stub = $this->getMockForAbstractClass( $this->class_name );

		$deleted_user_id  = $this->factory->user->create();
		$reassign_user_id = $this->factory->user->create();

		$post_ids = $this->factory->post->create_many( 5, array( 'post_author' => $deleted_user_id ) );

		$query = array(
			'include' => array( $deleted_user_id ),
		);

		$options = array(
			'reassign_user'    => true,
			'reassign_user_id' => $reassign_user_id,
			'login_restrict'   => false,
			'no_posts'         => false,
		);

		$deleted_users_count = $this->invoke_protected_method( $stub, 'delete_users_from_query', array( $query, $options ) );
		$this->assertEquals( 1, $deleted_users_count );

		if ( ! is_multisite() ) {
			// TODO: Handle this for multisite.
			$deleted_user_exists = get_user_by( 'id', $deleted_user_id );
			$this->assertEquals( false, $deleted_user_exists, 'Deleted user exists' );
		}

		$reassign_user_exists = get_user_by( 'id', $reassign_user_id );
		$this->assertInstanceOf( '\WP_User', $reassign_user_exists, 'Reassign user got deleted' );

		foreach ( $post_ids as $post_id ) {
			$author_id = get_post_field( 'post_author', $post_id );
			$this->assertEquals( $reassign_user_id, $author_id, 'Posts were not reassigned' );
		}
	}

	/**
	 * Provide data for testing deletion of users with no posts filter.
	 *
	 * @see test_delete_users
	 *
	 * @return array Test data.
	 */
	public function provide_data_for_testing_no_posts_filter() {
		return array(
			// Delete Users with no posts filter.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d' ),
						'post_type'       => 'page',
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
						'post_type'       => 'post',
					),
				),
				array(
					'limit_to'            => 0,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => true,
					'no_posts_post_types' => array( 'post', 'page' ),
				),
				array(
					'deleted_users'   => 13,
					'available_users' => 3, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Provide data for testing deletion of users with registration filter.
	 *
	 * @see test_delete_users
	 *
	 * @return array Test data.
	 */
	public function provide_data_for_testing_registration_filter() {
		return array(
			// Delete Users with 'before' registration filter.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d', strtotime( '-2 day' ) ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d', strtotime( '-5 day' ) ),
						'post_type'       => 'post',
					),
				),
				array(
					'limit_to'            => 0,
					'registered_restrict' => true,
					'registered_date_op'  => 'before',
					'registered_days'     => 3,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 10,
					'available_users' => 6, // Including 1 default admin user.
				),
			),
			// Delete Users with 'after' registration filter.
			array(
				array(
					array(
						'role'            => 'author',
						'no_of_users'     => 5,
						'registered_date' => date( 'Y-m-d', strtotime( '-2 day' ) ),
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d', strtotime( '-5 day' ) ),
						'post_type'       => 'post',
					),
				),
				array(
					'limit_to'            => 0,
					'registered_restrict' => true,
					'registered_date_op'  => 'after',
					'registered_days'     => 3,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 5,
					'available_users' => 11, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Provide data for testing deletion of users with limit filter.
	 *
	 * @see test_delete_users
	 *
	 * @return array Test data.
	 */
	public function provide_data_for_testing_limit_filter() {
		return array(
			// Delete Users with limit filter.
			array(
				array(
					array(
						'role'            => 'editor',
						'no_of_users'     => 20,
						'registered_date' => date( 'Y-m-d' ),
						'post_type'       => 'page',
					),
					array(
						'role'            => 'subscriber',
						'no_of_users'     => 10,
						'registered_date' => date( 'Y-m-d' ),
						'post_type'       => 'post',
					),
				),
				array(
					'limit_to'            => 25,
					'registered_restrict' => false,
					'login_restrict'      => false,
					'no_posts'            => false,
				),
				array(
					'deleted_users'   => 25,
					'available_users' => 6, // Including 1 default admin user.
				),
			),
		);
	}

	/**
	 * Test that users can be deleted using no posts filter.
	 *
	 * @dataProvider provide_data_for_testing_no_posts_filter
	 * @dataProvider provide_data_for_testing_registration_filter
	 * @dataProvider provide_data_for_testing_limit_filter
	 *
	 * @param array $setup           Create users using supplied arguments.
	 * @param array $user_operations User operations.
	 * @param array $expected        Expected output for respective operations.
	 */
	public function test_delete_users( $setup, $user_operations, $expected ) {
		$stub = $this->getMockForAbstractClass( $this->class_name );

		$size         = count( $setup );
		$all_user_ids = array();

		// Create users and assign to specified role.
		for ( $i = 0; $i < $size; $i++ ) {
			$args         = array( 'user_registered' => $setup[ $i ]['registered_date'] );
			$user_ids     = $this->factory->user->create_many( $setup[ $i ]['no_of_users'], $args );
			$all_user_ids = array_merge( $user_ids, $all_user_ids );

			foreach ( $user_ids as $user_id ) {
				$this->assign_role_by_user_id( $user_id, $setup[ $i ]['role'] );
			}

			if ( array_key_exists( 'post_type', $setup [ $i ] ) ) {
				$post_ids = $this->factory->post->create(
					array(
						'post_type'   => $setup[ $i ]['post_type'],
						'post_author' => $user_ids[0],
					)
				);
			}

			if ( array_key_exists( 'user_in_this_group_is_logged_in', $setup [ $i ] ) ) {
				$this->login_user( $user_ids[0] );
			}
		}

		$query = array(
			'include' => $all_user_ids,
		);

		if ( true === $user_operations['registered_restrict'] ) {
			$query['date_query'] = $this->invoke_protected_method( $stub, 'get_date_query', array( $user_operations ) );
		}

		if ( $user_operations['limit_to'] > 0 ) {
			$query['number'] = $user_operations['limit_to'];
		}

		$deleted_users = $this->invoke_protected_method( $stub, 'delete_users_from_query', array( $query, $user_operations ) );

		$this->assertEquals( $expected['deleted_users'], $deleted_users, 'Deleted Users do not match.' );

		$available_users = get_users();
		$this->assertEquals( $expected['available_users'], count( $available_users ), 'Available Users do not match.' );
	}
}
