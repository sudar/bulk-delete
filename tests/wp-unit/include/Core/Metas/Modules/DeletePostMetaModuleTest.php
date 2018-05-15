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
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		add_post_meta( $post, 'time', '10/10/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 1, count( $post_meta ) );

		$delete_options = array(
			'post_type'    => 'post', 
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
			'use_value'    => 'use_key',
			'meta_key'     => 'time',
			'date_op'      => '',
			'days'         => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 0, count( $post_meta ) );
	}

	public function test_that_more_than_one_post_meta_from_the_default_post_type() {
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		add_post_meta( $post, 'time', '10/10/2018' );
		add_post_meta( $post, 'time', '11/11/2018' );
		add_post_meta( $post, 'time', '12/12/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 3, count( $post_meta ) );

		$delete_options = array(
			'post_type'    => 'post', 
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
			'use_value'    => 'use_key',
			'meta_key'     => 'time',
			'date_op'      => '',
			'days'         => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 0, count( $post_meta ) );
	}
}
