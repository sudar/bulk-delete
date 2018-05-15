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
		
	}

	
}
