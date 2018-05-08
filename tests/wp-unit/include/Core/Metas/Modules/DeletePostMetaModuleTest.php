<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

class DeletePostMetaModuleTest extends WPCoreUnitTestCase {
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostMetaModule();
	}

	public function test_that_single_post_meta_from_the_default_post_type() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$delete_options = array(
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
			'use_value'    => 'use_key',
			'meta_key'     => 'time',
		);

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 10, count( $published_posts ) );
	}
}
