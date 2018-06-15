<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Posts by URL.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByURLModule
 *
 * @since 6.0.0
 */
class DeletePostsByURLModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByURLModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByURLModule();
	}

}
