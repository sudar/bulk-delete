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

	public function test_that_single_post_meta_from_the_custom_post_type() {
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_type' => 'custom' ) );

		add_post_meta( $post, 'time', '10/10/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 1, count( $post_meta ) );

		$delete_options = array(
			'post_type'    => 'custom', 
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

	public function test_that_more_than_one_post_meta_from_the_custom_post_type() {
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_type' => 'custom' ) );

		add_post_meta( $post, 'time', '10/10/2018' );
		add_post_meta( $post, 'time', '11/11/2018' );
		add_post_meta( $post, 'time', '12/12/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 3, count( $post_meta ) );

		$delete_options = array(
			'post_type'    => 'custom', 
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

	public function test_restrict_post_meta_deletion_based_on_post_published_date_older() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_date' => $date ) );

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
			'date_op'      => 'before',
			'days'         => '3',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 0, count( $post_meta ) );
	}

	public function test_restrict_post_meta_deletion_based_on_post_published_date_within_the_last_x_days() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_date' => $date ) );

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
			'date_op'      => 'after',
			'days'         => '5',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 0, count( $post_meta ) );
	}
}
