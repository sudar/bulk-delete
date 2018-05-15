<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of posts by user meta.
 *
 * Tests \BulkWP\BulkDelete\Core\Metas\Modules\DeletePostsByUserMetaModule
 *
 * @since 6.0.0
 */
class DeletePostsByUserMetaModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Metas\Modules\DeletePostsByUserMetaModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeleteUserMetaModule();
	}

	public function test_deleting_single_user_meta_fields_from_admin_user_role() {
		
		$user = $this->factory->user->create( array( 'role' => 'administrator' ));

		add_user_meta( $user, 'time', '10/10/2018' );

		$delete_options = array(
			'user_role'    => 'administrator', 
			'meta_key'     => 'time',
			'use_value'    => false,
			'limit_to'     => -1,
			'delete_options' => '',
		);
		$meta_deleted = $this->module->delete( $delete_options );

		$user_meta = get_user_meta( $user, 'time' );
		$this->assertEquals( 0, count( $user_meta ) );

	}

	public function test_deleting_single_user_meta_fields_from_subscriber_user_role() {
		
		$user = $this->factory->user->create( array( 'role' => 'subscriber' ));

		add_user_meta( $user, 'time', '10/10/2018' );

		$delete_options = array(
			'user_role'    => 'subscriber', 
			'meta_key'     => 'time',
			'use_value'    => false,
			'limit_to'     => -1,
			'delete_options' => '',
		);
		$meta_deleted = $this->module->delete( $delete_options );

		$user_meta = get_user_meta( $user, 'time' );
		$this->assertEquals( 0, count( $user_meta ) );

	}

	public function test_deleting_multiple_user_meta_fields_from_admin_user_role() {
		
		$user = $this->factory->user->create( array( 'role' => 'administrator' ));

		add_user_meta( $user, 'time', '10/10/2018' );
		add_user_meta( $user, 'time', '11/10/2018' );
		add_user_meta( $user, 'time', '12/10/2018' );

		$delete_options = array(
			'user_role'    => 'administrator', 
			'meta_key'     => 'time',
			'use_value'    => false,
			'limit_to'     => -1,
			'delete_options' => '',
		);
		$meta_deleted = $this->module->delete( $delete_options );

		$user_meta = get_user_meta( $user, 'time' );
		$this->assertEquals( 0, count( $user_meta ) );

	}

	public function test_deleting_multiple_user_meta_fields_from_subscriber_user_role() {
		
		$user = $this->factory->user->create( array( 'role' => 'subscriber' ));

		add_user_meta( $user, 'time', '10/10/2018' );
		add_user_meta( $user, 'time', '11/10/2018' );
		add_user_meta( $user, 'time', '12/10/2018' );

		$delete_options = array(
			'user_role'    => 'subscriber', 
			'meta_key'     => 'time',
			'use_value'    => false,
			'limit_to'     => -1,
			'delete_options' => '',
		);
		$meta_deleted = $this->module->delete( $delete_options );

		$user_meta = get_user_meta( $user, 'time' );
		$this->assertEquals( 0, count( $user_meta ) );

	}

	public function test_delete_usermeta_in_batches() {
		
		$users = $this->factory->user->create_many( 20, array( 'role' => 'subscriber' ));

		foreach($users as $user ){
			add_user_meta( $user, 'time', '10/10/2018' );
		}

		$delete_options = array(
			'user_role'    => 'subscriber', 
			'meta_key'     => 'time',
			'use_value'    => false,
			'limit_to'     => 10,
			'delete_options' => '',
		);
		$meta_deleted = $this->module->delete( $delete_options );

		$this->assertEquals( 10, $meta_deleted );

	}
}