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
	 * Setup method.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeleteUserMetaModule();
	}

	/**
	 * Provide data to the `test_that_user_meta_can_be_deleted_from_a_single_user_by_role` function.
	 *
	 * @see test_that_user_meta_can_be_deleted_from_a_single_user_by_role
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_user_meta_can_be_deleted_from_a_single_user_by_role() {
		return array(
			array(
				'administrator',
			),
			array(
				'author',
			),
			array(
				'subscriber',
			),
		);
	}

	/**
	 * Test deleting meta from a single user.
	 *
	 * @param string $user_role User role.
	 *
	 * @dataProvider provide_data_to_test_that_user_meta_can_be_deleted_from_a_single_user_by_role
	 */
	public function test_that_user_meta_can_be_deleted_from_a_single_user_by_role( $user_role ) {
		$matched_meta_key      = 'matched_key';
		$matched_meta_value    = 'Matched Value';
		$mismatched_meta_key   = 'Mismatched_key';
		$mismatched_meta_value = 'Mismatched value';

		// Create a user with admin role.
		$user_id = $this->factory->user->create( array( 'role' => $user_role ) );
		add_user_meta( $user_id, $matched_meta_key, $matched_meta_value );
		add_user_meta( $user_id, $mismatched_meta_key, $mismatched_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( $user_role ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $metas_deleted );

		$this->assertFalse( metadata_exists( 'user', $user_id, $matched_meta_key ) );
		$this->assertTrue( metadata_exists( 'user', $user_id, $mismatched_meta_key ) );
	}

	/**
	 * Test deleting meta from a single user with duplicate meta keys.
	 *
	 * Todo: Currently this doesn't work.
	 *
	 * @see          https://github.com/sudar/bulk-delete/issues/515 for details.
	 *
	 * @param string $user_role User role.
	 *
	 * @dataProvider provide_data_to_test_that_user_meta_can_be_deleted_from_a_single_user_by_role
	 */
	public function test_that_user_meta_can_be_deleted_from_a_single_user_by_role_with_duplicate_meta_keys( $user_role ) {
		$this->markTestSkipped(
			'User metas with the same meta key with multiple values is not fully supported yet'
		);

		$matched_meta_key      = 'matched_key';
		$matched_meta_value_1  = 'Matched Value';
		$matched_meta_value_2  = 'Matched Value';
		$matched_meta_value_3  = 'Matched Value';
		$mismatched_meta_key   = 'mismatched_key';
		$mismatched_meta_value = 'Mismatched value';

		$user_id = $this->factory->user->create( array( 'role' => $user_role ) );
		add_user_meta( $user_id, $matched_meta_key, $matched_meta_value_1 );
		add_user_meta( $user_id, $matched_meta_key, $matched_meta_value_2 );
		add_user_meta( $user_id, $matched_meta_key, $matched_meta_value_3 );

		add_user_meta( $user_id, $mismatched_meta_key, $mismatched_meta_value );

		// call our method.
		$delete_options = array(
			'selected_roles' => array( $user_role ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 3, $meta_deleted );

		$this->assertFalse( metadata_exists( 'user', $user_id, $matched_meta_key ) );
		$this->assertTrue( metadata_exists( 'user', $user_id, $mismatched_meta_key ) );
	}

	/**
	 * Add tests to delete user meta in batches.
	 *
	 * Todo: Handle cases where the metas to be deleted may not be in the front.
	 */
	public function test_that_user_meta_can_be_deleted_in_batches() {
		$matched_meta_key      = 'matched_key';
		$matched_meta_value    = 'Matched Value';
		$mismatched_meta_key   = 'Mismatched_key';
		$mismatched_meta_value = 'Mismatched value';

		$total_users = 50;
		$batch_size  = 10;

		$matched_user_ids = $this->factory->user->create_many( $total_users / 2, array( 'role' => 'subscriber' ) );
		foreach ( $matched_user_ids as $user_id ) {
			add_user_meta( $user_id, $matched_meta_key, $matched_meta_value );
		}

		$mismatched_user_ids = $this->factory->user->create_many( $total_users / 2, array( 'role' => 'subscriber' ) );
		foreach ( $mismatched_user_ids as $user_id ) {
			add_user_meta( $user_id, $mismatched_meta_key, $mismatched_meta_value );
		}

		$metas_deleted = 0;
		for ( $i = 0; $i < $total_users / $batch_size; $i ++ ) {
			// call our method .
			$delete_options = array(
				'selected_roles' => array( 'subscriber' ),
				'meta_key'       => $matched_meta_key,
				'use_value'      => false,
				'limit_to'       => $batch_size,
			);

			$metas_deleted += $this->module->delete( $delete_options );
		}

		$this->assertEquals( $total_users / 2, $metas_deleted );

		foreach ( $matched_user_ids as $user_id ) {
			$this->assertFalse( metadata_exists( 'user', $user_id, $matched_meta_key ) );
		}

		foreach ( $mismatched_user_ids as $user_id ) {
			$this->assertTrue( metadata_exists( 'user', $user_id, $mismatched_meta_key ) );
		}
	}

	/**
	 * Provide data to the `test_that_user_meta_can_be_deleted_from_a_single_user_by_role` function.
	 *
	 * @see test_that_meta_fields_can_be_delete_from_multiple_users_in_single_role
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_meta_fields_can_be_delete_from_multiple_users_in_single_role() {
		return array(
			array(
				array(
					'role'  => 'administrator',
					'count' => 10,
				),
			),
			array(
				array(
					'role'  => 'author',
					'count' => 10,
				),
			),
			array(
				array(
					'role'  => 'subscriber',
					'count' => 10,
				),
			),
		);
	}

	/**
	 * Test deleting meta from multiple user in a single user role.
	 *
	 * @param array $setup Setup data.
	 *
	 * @dataProvider provide_data_to_test_that_meta_fields_can_be_delete_from_multiple_users_in_single_role
	 */
	public function test_that_meta_fields_can_be_delete_from_multiple_users_in_single_role( $setup ) {
		$matched_meta_key      = 'matched_key';
		$matched_meta_value    = 'Matched Value';
		$mismatched_meta_key   = 'Mismatched_key';
		$mismatched_meta_value = 'Mismatched value';

		$matched_user_ids = $this->factory->user->create_many( $setup['count'] / 2, array( 'role' => $setup['role'] ) );
		foreach ( $matched_user_ids as $user_id ) {
			add_user_meta( $user_id, $matched_meta_key, $matched_meta_value );
		}

		$mismatched_user_ids = $this->factory->user->create_many( $setup['count'] / 2, array( 'role' => $setup['role'] ) );
		foreach ( $mismatched_user_ids as $user_id ) {
			add_user_meta( $user_id, $mismatched_meta_key, $mismatched_meta_value );
		}

		// call our method .
		$delete_options = array(
			'selected_roles' => array( $setup['role'] ),
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $setup['count'] / 2, $metas_deleted );

		foreach ( $matched_user_ids as $user_id ) {
			$this->assertFalse( metadata_exists( 'user', $user_id, $matched_meta_key ) );
		}

		foreach ( $mismatched_user_ids as $user_id ) {
			$this->assertTrue( metadata_exists( 'user', $user_id, $mismatched_meta_key ) );
		}
	}

	/**
	 * Test deletion of user metas from more than role, with easy role having one user.
	 */
	public function test_that_meta_fields_can_be_delete_from_single_users_in_multiple_role() {
		$matched_meta_key      = 'matched_key';
		$matched_meta_value    = 'Matched Value';
		$mismatched_meta_key   = 'Mismatched_key';
		$mismatched_meta_value = 'Mismatched value';

		$user_ids = array();

		// Create a users in various user roles with meta data.
		$roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
		foreach ( $roles as $role ) {
			$user_id = $this->factory->user->create( array( 'role' => $role ) );
			add_user_meta( $user_id, $matched_meta_key, $matched_meta_value );
			add_user_meta( $user_id, $mismatched_meta_key, $mismatched_meta_value );
			$user_ids[] = $user_id;
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

		foreach ( $user_ids as $user_id ) {
			$this->assertFalse( metadata_exists( 'user', $user_id, $matched_meta_key ) );
			$this->assertTrue( metadata_exists( 'user', $user_id, $mismatched_meta_key ) );
		}
	}

	/**
	 * Test deletion of user metas from more than role, with easy role having more than one user.
	 */
	public function test_that_meta_fields_can_be_delete_from_multiple_users_in_multiple_role() {
		$matched_meta_key      = 'matched_key';
		$matched_meta_value    = 'Matched Value';
		$mismatched_meta_key   = 'Mismatched_key';
		$mismatched_meta_value = 'Mismatched value';

		$user_ids = array();

		// Create two users in various user roles with meta data.
		$roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
		foreach ( $roles as $role ) {
			for ( $j = 0; $j < 2; $j ++ ) {
				$user_id = $this->factory->user->create( array( 'role' => $role ) );
				add_user_meta( $user_id, $matched_meta_key, $matched_meta_value );
				add_user_meta( $user_id, $mismatched_meta_key, $mismatched_meta_value );
				$user_ids[] = $user_id;
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

		foreach ( $user_ids as $user_id ) {
			$this->assertFalse( metadata_exists( 'user', $user_id, $matched_meta_key ) );
			$this->assertTrue( metadata_exists( 'user', $user_id, $mismatched_meta_key ) );
		}
	}

	/**
	 * Test deletion of user metas from one role, which has no users. Nothing should be deleted.
	 */
	public function test_that_nothing_gets_deleted_when_a_role_has_no_users() {
		$matched_meta_key   = 'matched_key';
		$matched_meta_value = 'Matched Value';

		// Create a users with meta value.
		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		add_user_meta( $user_id, $matched_meta_key, $matched_meta_value );

		// call our method .
		$delete_options = array(
			'selected_roles' => 'administrator',
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		$this->assertTrue( metadata_exists( 'user', $user_id, $matched_meta_key ) );
	}

	/**
	 * Test deletion of user metas from more than one role, where each role has no users. Nothing should be deleted.
	 */
	public function test_that_nothing_gets_deleted_when_used_with_multiple_roles_each_having_no_users() {
		$matched_meta_key   = 'matched_key';
		$matched_meta_value = 'Matched Value';

		$matched_roles    = array( 'subscriber' );
		$mismatched_roles = array( 'administrator', 'editor', 'author', 'contributor' );

		$user_ids = array();
		foreach ( $mismatched_roles as $role ) {
			for ( $j = 0; $j < 2; $j ++ ) {
				$user_id = $this->factory->user->create( array( 'role' => $role ) );
				add_user_meta( $user_id, $matched_meta_key, $matched_meta_value );
				$user_ids[] = $user_id;
			}
		}

		// call our method.
		$delete_options = array(
			'selected_roles' => $matched_roles,
			'meta_key'       => $matched_meta_key,
			'use_value'      => false,
			'limit_to'       => 0,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 0, $meta_deleted );

		foreach ( $user_ids as $user_id ) {
			$this->assertTrue( metadata_exists( 'user', $user_id, $matched_meta_key ) );
		}
	}
}
