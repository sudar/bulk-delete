<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of user meta.
 *
 * Tests \BulkWP\BulkDelete\Core\Metas\Modules\DeleteUserMetaModule
 *
 * @since 6.0.0
 */
class DeleteUserMetaModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Metas\Modules\DeleteUserMetaModule
	 */
	protected $module;

	/**
	 * Setup method
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeleteUserMetaModule();
	}

	/**
	 * Add to test single user meta from admin user role.
	 */
	public function test_that_meta_fields_can_be_delete_from_a_single_user_in_admin_role() {
		// Create a user with admin role.
		$user                   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$matched_meta_key       = 'matched_key';
		$matched_meta_value     = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		add_user_meta( $user, $matched_meta_key, $matched_meta_value );

		add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'administrator' ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, $matched_meta_key, true );
		$this->assertEquals( '', $user_meta );

		$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
		$this->assertEquals( $missmatched_meta_value, $exist_user_meta );

	}

	/**
	 * Add to test single user meta from subscriber user role.
	 */
	public function test_that_meta_fields_can_be_delete_from_a_single_user_in_subscriber_role() {
		// Create a user with subscriber role .
		$user                   = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$matched_meta_key       = 'matched_key';
		$matched_meta_value     = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		add_user_meta( $user, $matched_meta_key, $matched_meta_value );

		add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, $matched_meta_key, true );
		$this->assertEquals( '', $user_meta );

		$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
		$this->assertEquals( $missmatched_meta_value, $exist_user_meta );
	}

	/**
	 * Add to test multiple user meta from admin user role.
	 */
	public function test_that_meta_fields_can_be_delete_from_multiple_users_in_admin_role() {
		// Create a user with admin role.
		$user                   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$matched_meta_key       = 'matched_key';
		$matched_meta_value_1   = 'Matched Value';
		$matched_meta_value_2   = 'Matched Value';
		$matched_meta_value_3   = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		add_user_meta( $user, $matched_meta_key, $matched_meta_value_1 );
		add_user_meta( $user, $matched_meta_key, $matched_meta_value_2 );
		add_user_meta( $user, $matched_meta_key, $matched_meta_value_3 );

		add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );

		// call our method .
		$delete_options = array(
			'selected_roles' => array( 'administrator' ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, $matched_meta_key, true );
		$this->assertEquals( '', $user_meta );

		$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
		$this->assertEquals( $missmatched_meta_value, $exist_user_meta );
	}

	/**
	 * Add to test multiple user meta from subscriber user role.
	 */
	public function test_that_meta_fields_can_be_delete_from_multiple_users_in_subscriber_role() {
		// Create a user with admin role.
		$user                   = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$matched_meta_key       = 'matched_key';
		$matched_meta_value_1   = 'Matched Value';
		$matched_meta_value_2   = 'Matched Value';
		$matched_meta_value_3   = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		add_user_meta( $user, $matched_meta_key, $matched_meta_value_1 );
		add_user_meta( $user, $matched_meta_key, $matched_meta_value_2 );
		add_user_meta( $user, $matched_meta_key, $matched_meta_value_3 );

		add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );

		// call our method .
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, $matched_meta_key, true );
		$this->assertEquals( '', $user_meta );

		$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
		$this->assertEquals( $missmatched_meta_value, $exist_user_meta );
	}

	/**
	 * Add to test delete user meta in batches.
	 */
	public function test_that_delete_usermeta_in_batches() {
		// Create a user with subscriber role .
		$users                  = $this->factory->user->create_many( 20, array( 'role' => 'subscriber' ) );
		$matched_meta_key       = 'matched_key';
		$matched_meta_value     = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		foreach ( $users as $user ) {
			add_user_meta( $user, $matched_meta_key, $matched_meta_value );
			add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );
		}

		// call our method .
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 10,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $meta_deleted );

		foreach ( $users as $user ) {
			$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
			$this->assertEquals( $missmatched_meta_value, $exist_user_meta );
		}
	}

	/**
	 * Test deletion of user metas from more than one users in a single role.
	 */
	public function test_that_meta_fields_can_be_delete_from_multiple_users_in_single_role() {

		// Create a user with subscriber role .
		$users                  = $this->factory->user->create_many( 10, array( 'role' => 'administrator' ) );
		$matched_meta_key       = 'matched_key';
		$matched_meta_value     = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		foreach ( $users as $user ) {
			add_user_meta( $user, $matched_meta_key, $matched_meta_value );
			add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );
		}

		// call our method .
		$delete_options = array(
			'selected_roles' => array( 'administrator' ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $meta_deleted );

		$user_meta = get_user_meta( $user, $matched_meta_key, true );
		$this->assertEquals( '', $user_meta );

		foreach ( $users as $user ) {
			$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
			$this->assertEquals( $missmatched_meta_value, $exist_user_meta );
		}
	}

	/**
	 * Test deletion of user metas from more than role, with easy role having one user.
	 */
	public function test_that_meta_fields_can_be_delete_from_multiple_users_in_multiple_role() {

		$matched_meta_key       = 'matched_key';
		$matched_meta_value     = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		// Create a users in various user roles with meta data.
		$roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
		$users = array();
		foreach ( $roles as $role ) {
			$user = $this->factory->user->create( array( 'role' => $role ) );
			add_user_meta( $user, $matched_meta_key, $matched_meta_value );
			add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );
			$users[] = $user;
		}

		// call our method .
		$delete_options = array(
			'selected_roles' => $roles,
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 5, $meta_deleted );

		foreach ( $users as $user ) {
			$user_meta = get_user_meta( $user, $matched_meta_key, true );
			$this->assertEquals( '', $user_meta );

			$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
			$this->assertEquals( $missmatched_meta_value, $exist_user_meta );
		}

	}

	/**
	 * Test deletion of user metas from more than role, with easy role having more than one user.
	 */
	public function test_that_meta_fields_can_be_delete_from_multiple_users_in_each_multiple_role() {

		$matched_meta_key       = 'matched_key';
		$matched_meta_value     = 'Matched Value';
		$missmatched_meta_key   = 'missmatched_key';
		$missmatched_meta_value = 'Missmatched value';

		// Create a users in various user roles with meta data.
		$roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
		$users = array();
		foreach ( $roles as $role ) {
			for ( $j = 0; $j < 2; $j++ ) {
				$user = $this->factory->user->create( array( 'role' => $role ) );
				add_user_meta( $user, $matched_meta_key, $matched_meta_value );
				add_user_meta( $user, $missmatched_meta_key, $missmatched_meta_value );
				$users[] = $user;
			}
		}

		// call our method .
		$delete_options = array(
			'selected_roles' => $roles,
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $meta_deleted );

		foreach ( $users as $user ) {
			$user_meta = get_user_meta( $user, $matched_meta_key, true );
			$this->assertEquals( '', $user_meta );

			$exist_user_meta = get_user_meta( $user, $missmatched_meta_key, true );
			$this->assertEquals( $missmatched_meta_value, $exist_user_meta );
		}

	}

	/**
	 * Test deletion of user metas from one role, which has no users. Nothing should be deleted.
	 */
	public function test_that_meta_fields_can_be_deleted_from_user_role_does_not_have_users() {

		$matched_meta_key   = 'matched_key';
		$matched_meta_value = 'Matched Value';

		// Create a users with meta value.
		$user = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		add_user_meta( $user, $matched_meta_key, $matched_meta_value );

		// call our method .
		$delete_options = array(
			'selected_roles' => 'administrator',
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		$exist_user_meta = get_user_meta( $user, $matched_meta_key, true );
		$this->assertEquals( $matched_meta_value, $exist_user_meta );
	}

	/**
	 * Test deletion of user metas from more than one role, where each role has no users. Nothing should be deleted.
	 */
	public function test_that_meta_fields_can_be_deleted_from_multiple_user_role_does_not_have_users() {

		$matched_meta_key   = 'matched_key';
		$matched_meta_value = 'Matched Value';

		// Create a users in various user roles with meta data.
		$matched_roles     = array( 'subscriber' );
		$missmatched_roles = array( 'administrator', 'editor', 'author', 'contributor' );
		$users             = array();

		for ( $j = 0; $j < 2; $j++ ) {
			$user = $this->factory->user->create( array( 'role' => 'subscriber' ) );
			add_user_meta( $user, $matched_meta_key, $matched_meta_value );
			$users[] = $user;
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => $missmatched_roles,
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		foreach ( $users as $user ) {
			$user_meta = get_user_meta( $user, $matched_meta_key, true );
			$this->assertEquals( $matched_meta_value, $user_meta );
		}

	}

	/**
	 * Test deletion of user metas from more than one role, where one role doesn't have any users and other role has users. Nothing should be deleted.
	 */
	public function test_that_delete_multiple_users_metas_fields_can_be_deleted_from_multiple_user_role_no_users_and_have_users() {

		$meta_key   = 'matched_key';
		$meta_value = 'Matched Value';

		// Create a users in various user roles with meta data.
		$role_array_with_users    = array( 'subscriber', 'administrator' );
		$role_array_without_users = array( 'editor', 'author', 'contributor' );
		$users                    = array();

		foreach ( $role_array_with_users as $role ) {
			for ( $j = 0; $j < 2; $j++ ) {
				$user = $this->factory->user->create( array( 'role' => $role ) );
				add_user_meta( $user, $meta_key, $meta_value );
				$users[] = $user;
			}
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => $role_array_without_users,
			'meta_key'       => $meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);
		$meta_deleted   = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		// call our method.
		$delete_options = array(
			'selected_roles' => $role_array_with_users,
			'meta_key'       => $meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 4, $meta_deleted );

	}

	/**
	 * Test deletion of user metas with both meta key and value.
	 */
	public function test_that_meta_fields_can_be_deleted_from_meta_keys_and_meta_values() {

		$meta_key               = 'matched_key';
		$matched_meta_value     = 'Matched Value';
		$missmatched_meta_value = 'Missmatched value';

		// Create a user with subscriber role.
		$user = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		add_user_meta( $user, $meta_key, $matched_meta_value );
		add_user_meta( $user, $meta_key, $missmatched_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => $meta_key,
			'meta_value'     => $matched_meta_value,
			'use_value'      => true,
			'limit_to'       => 0,
			'meta_op'        => '=',
			'type'           => 'CHAR',
			'value'          => $matched_meta_value,

		);
		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$exist_user_meta = get_user_meta( $user, $meta_key, true );
		$this->assertEquals( $missmatched_meta_value, $exist_user_meta );

	}

}
