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
	public function test_that_meta_fields_from_a_single_user_in_admin_role_can_be_delete() {
		// Create a user with admin role.
		$user               = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		add_user_meta( $user, 'time', '10/10/2018' );

		add_user_meta( $user, $another_meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'administrator' ),
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, 'time', true );
		$this->assertEquals( '', $user_meta );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );

	}

	/**
	 * Add to test single user meta from subscriber user role.
	 */
	public function test_that_meta_fields_from_a_single_user_in_subscriber_role_can_be_delete() {
		// Create a user with subscriber role.
		$user               = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		add_user_meta( $user, 'time', '10/10/2018' );

		add_user_meta( $user, $another_meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, 'time', true );
		$this->assertEquals( '', $user_meta );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );
	}

	/**
	 * Add to test multiple user meta from admin user role.
	 */
	public function test_that_meta_fields_from_multiple_users_in_admin_role_can_be_delete() {
		// Create a user with admin role.
		$user               = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		add_user_meta( $user, 'time', '10/10/2018' );
		add_user_meta( $user, 'time', '11/10/2018' );
		add_user_meta( $user, 'time', '12/10/2018' );

		add_user_meta( $user, $another_meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'administrator' ),
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, 'time' );
		$this->assertEquals( 0, count( $user_meta ) );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );
	}

	/**
	 * Add to test multiple user meta from subscriber user role.
	 */
	public function test_that_meta_fields_from_multiple_users_in_subscriber_role_can_be_delete() {
		// Create a user with subscriber role.
		$user               = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		add_user_meta( $user, 'time', '10/10/2018' );
		add_user_meta( $user, 'time', '11/10/2018' );
		add_user_meta( $user, 'time', '12/10/2018' );

		add_user_meta( $user, $another_meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, 'time' );
		$this->assertEquals( 0, count( $user_meta ) );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );
	}

	/**
	 * Add to test delete user meta in batches.
	 */
	public function test_that_delete_usermeta_in_batches() {
		// Create a user with subscriber role.
		$users              = $this->factory->user->create_many( 20, array( 'role' => 'subscriber' ) );
		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		foreach ( $users as $user ) {
			add_user_meta( $user, 'time', '10/10/2018' );
			add_user_meta( $user, $another_meta_key, $another_meta_value );
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 10,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $meta_deleted );

		foreach ( $users as $user ) {
			$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );
			$this->assertEquals( true, $exist_user );
		}
	}

	/**
	 * Test deletion of user metas from more than one users in a single role.
	 */
	public function test_that_meta_fields_from_multiple_users_in_single_role_can_be_delete() {

		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		// Create a users with meta value in admin role.
		for ( $i = 0; $i < 10; $i++ ) {
			$user = $this->factory->user->create( array( 'role' => 'administrator' ) );
			add_user_meta( $user, 'time', '10/10/2018' );
			add_user_meta( $user, $another_meta_key, $another_meta_value );
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'administrator' ),
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $meta_deleted );

		$user_meta = get_user_meta( $user, 'time', true );
		$this->assertEquals( '', $user_meta );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );
		$this->assertEquals( true, $exist_user );
	}

	/**
	 * Test deletion of user metas from more than role, with easy role having one user.
	 */
	public function test_that_meta_fields_from_multiple_users_in_multiple_role_can_be_delete() {

		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		$role_array = array();
		// Create a users with meta value in dynamic role.
		for ( $i = 0; $i < 10; $i++ ) {
			$role         = 'user_type_' . $i;
			$role_array[] = $role;
			$user         = $this->factory->user->create( array( 'role' => $role ) );
			add_user_meta( $user, 'time', '10/10/2018' );
			add_user_meta( $user, $another_meta_key, $another_meta_value );
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => $role_array,
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $meta_deleted );

		$user_meta = get_user_meta( $user, 'time', true );
		$this->assertEquals( '', $user_meta );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );

	}

	/**
	 * Test deletion of user metas from more than role, with easy role having more than one user.
	 */
	public function test_that_meta_fields_from_multiple_users_in_each_multiple_role_can_be_delete() {

		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		$role_array = array();
		// Create a users with meta value in dynamic role.
		for ( $i = 0; $i < 10; $i++ ) {
			$role         = 'user_type_' . $i;
			$role_array[] = $role;
			for ( $j = 0; $j < 10; $j++ ) {
				$user = $this->factory->user->create( array( 'role' => $role ) );
				add_user_meta( $user, 'time', '10/10/2018' );
				add_user_meta( $user, $another_meta_key, $another_meta_value );
			}
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => $role_array,
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 100, $meta_deleted );

		$user_meta = get_user_meta( $user, 'time', true );
		$this->assertEquals( '', $user_meta );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );
	}

	/**
	 * Test deletion of user metas from one role, which has no users. Nothing should be deleted.
	 */
	public function test_that_meta_fields_from_user_role_does_not_have_users() {

		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		// Create a users with meta value in dynamic role.
		for ( $i = 0; $i < 10; $i++ ) {
			$role = 'subscriber';
			$user = $this->factory->user->create( array( 'role' => $role ) );
			add_user_meta( $user, 'time', '10/10/2018' );
			add_user_meta( $user, $another_meta_key, $another_meta_value );
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'administrator' ),
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );

	}

	/**
	 * Test deletion of user metas from more than one role, where each role has no users. Nothing should be deleted.
	 */
	public function test_that_meta_fields_from_multiple_user_role_does_not_have_users() {

		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		$role_array = array();
		for ( $i = 0; $i < 10; $i++ ) {
			$role         = 'user_type_' . $i;
			$role_array[] = $role;
		}

		// Create a users with meta value in subscriber role.
		for ( $j = 0; $j < 10; $j++ ) {
			$user = $this->factory->user->create( array( 'role' => 'subscriber' ) );
			add_user_meta( $user, 'time', '10/10/2018' );
			add_user_meta( $user, $another_meta_key, $another_meta_value );
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => $role_array,
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );

	}

	/**
	 * Test deletion of user metas from more than one role, where one role doesn't have any users and other role has users. Nothing should be deleted.
	 */
	public function test_that_delete_multiple_users_metas_fields_from_multiple_user_role_no_users_and_have_users() {

		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		$role_array_without_users = array();
		for ( $i = 0; $i < 10; $i++ ) {
			$role                       = 'user_type_no_user_' . $i;
			$role_array_without_users[] = $role;
		}

		$role_array_with_users = array();
		for ( $i = 0; $i < 10; $i++ ) {
			$role                    = 'user_type_' . $i;
			$role_array_with_users[] = $role;
			// Create a users with meta value in dynamic role.
			for ( $j = 0; $j < 10; $j++ ) {
				$user = $this->factory->user->create( array( 'role' => $role ) );
				add_user_meta( $user, 'time', '10/10/2018' );
				add_user_meta( $user, $another_meta_key, $another_meta_value );
			}
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => $role_array_without_users,
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		// call our method.
		$delete_options = array(
			'selected_roles' => $role_array_with_users,
			'meta_key'       => 'time',
			'use_value'      => false,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 100, $meta_deleted );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );

	}

	/**
	 * Test deletion of user metas with both meta key and value.
	 */
	public function test_that_meta_fields_from_meta_keys_and_meta_values() {

		$another_meta_key   = 'another';
		$another_meta_value = 'Another';

		// Create a user with subscriber role.
		$user = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		add_user_meta( $user, 'test_key', 'Test Value' );
		add_user_meta( $user, $another_meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( 'subscriber' ),
			'meta_key'       => 'test_key',
			'meta_value'     => 'Test Value',
			'use_value'      => true,
			'limit_to'       => 0,
			'delete_options' => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$user_meta = get_user_meta( $user, 'test_key', true );
		$this->assertEquals( '', $user_meta );

		$exist_user = $this->is_user_meta_exists( $user, $another_meta_key );

		$this->assertEquals( true, $exist_user );
	}

	/**
	 * Check is user id is exist.
	 *
	 * @param int    $user_id User id.
	 * @param string $meta_key Meta key.
	 */
	public function is_user_meta_exists( $user_id, $meta_key ) {

		$meta = get_user_meta( $user_id, $meta_key );

		return ( count( $meta ) > 0 );

	}
}
