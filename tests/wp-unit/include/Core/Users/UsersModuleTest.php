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
}
