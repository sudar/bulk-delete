<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Delete Post Meta Module.
 *
 * Tests \BulkWP\BulkDelete\Core\Metas\Modules\DeletePostMetaModule
 *
 * @since 6.0.0
 */
class DeletePostMetaModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Metas\Modules\DeletePostMetaModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostMetaModule();
	}

	/**
	 * Add to test single post meta from the default post type.
	 */
	public function test_that_single_post_meta_can_be_deleted_from_default_post_type() {
		// Create a post
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		// Assign meta value to the post
		add_post_meta( $post, 'time', '10/10/2018' );

		$post_meta = get_post_meta( $post, 'time' );

		// Assert that post meta have posts
		$this->assertEquals( 1, count( $post_meta ) );

		// call our method.
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

		// Assert that post meta does not have posts
		$this->assertEquals( 0, count( $post_meta ) );
	}

	/**
	 * Add to test more than one post meta from the default post type.
	 */
	public function test_that_more_than_one_post_meta_can_be_deleted_from_default_post_type() {
		// Create a post
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		// Assign meta value to the post
		add_post_meta( $post, 'time', '10/10/2018' );
		add_post_meta( $post, 'time', '11/11/2018' );
		add_post_meta( $post, 'time', '12/12/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 3, count( $post_meta ) );

		// call our method.
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

	/**
	 * Add to test single post meta from the custom post type.
	 */
	public function test_that_single_post_meta_can_be_deleted_from_custom_post_type() {
		// Create a post with custom post type
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_type' => 'custom' ) );

		// Assign meta value to the post
		add_post_meta( $post, 'time', '10/10/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 1, count( $post_meta ) );

		// call our method.
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

	/**
	 * Add to test more than one post meta from the custom post type.
	 */
	public function test_that_more_than_one_post_meta_can_de_deleted_from_custom_post_type() {
		// Create a post with custom post type
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_type' => 'custom' ) );

		// Assign meta value to the post
		add_post_meta( $post, 'time', '10/10/2018' );
		add_post_meta( $post, 'time', '11/11/2018' );
		add_post_meta( $post, 'time', '12/12/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 3, count( $post_meta ) );

		// call our method.
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

	/**
	 * Add to test post meta delete from post published date.
	 */
	public function test_that_post_meta_deletion_be_restricted_by_post_older_than() {
		// Create a post with past date
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_date' => $date ) );

		// Assign meta value to the post
		add_post_meta( $post, 'time', '10/10/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 1, count( $post_meta ) );

		// call our method.
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

	/**
	 * Add to test post meta delete from previous published with in x date.
	 */
	public function test_that_post_meta_deletion_be_restricted_by_posts_within_the_last_x_days() {
		// Create a post with past date
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post', 'post_date' => $date ) );

		// Assign meta value to the post
		add_post_meta( $post, 'time', '10/10/2018' );

		$post_meta = get_post_meta( $post, 'time' );
		$this->assertEquals( 1, count( $post_meta ) );

		// call our method.
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

	/**
	 * Add to test delete post meta in batches.
	 */
	public function test_that_post_meta_can_be_deleted_in_batches() {
		$posts = $this->factory->post->create_many( 20, array(
			'post_type'   => 'post',
		) );
		foreach($posts as $post ){
			// Assign meta value to the post
			add_post_meta( $post, 'time', '10/10/2018' );
		}

		// call our method.
		$delete_options = array(
			'post_type'    => 'post',
			'limit_to'     => 10,
			'restrict'     => false,
			'force_delete' => false,
			'use_value'    => 'use_key',
			'meta_key'     => 'time',
			'date_op'      => '',
			'days'         => '',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		$this->assertEquals( 10, $meta_deleted );
	}
}
