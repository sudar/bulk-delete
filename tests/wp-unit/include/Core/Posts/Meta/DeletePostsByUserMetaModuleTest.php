<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of posts by user meta.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByUserMetaModule
 *
 * @since 6.0.0
 */
class DeletePostsByUserMetaModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByUserMetaModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByUserMetaModule();
	}

	public function test_that_posts_from_all_categories_in_default_post_type_can_be_trashed() {
		
	}

	
}
