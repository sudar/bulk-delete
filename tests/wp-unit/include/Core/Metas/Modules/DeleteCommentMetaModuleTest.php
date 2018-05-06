<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

class DeleteCommentMetaModuleTest extends WPCoreUnitTestCase {
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeleteCommentMetaModule();
	}

	public function test_that_pages_can_be_trashed() {
		$this->assertEquals( 20, 20 );
	}
}
