<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of posts by post type.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByPostTypeModule
 *
 * @since 6.0.0
 */
class DeletePostsByPostTypeModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByPostTypeModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByPostTypeModule();
	}

	/**
	 * Add tests to deleting posts from single post type.
	 */
	public function test_delete_posts_from_single_post_type() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$published_posts = $this->get_posts_by_post_type();
		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'post' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

	}

	/**
	 * Add tests to deleting posts from two post types.
	 */
	public function test_delete_posts_from_two_post_types() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$published_posts = $this->get_posts_by_post_type();
		$this->assertEquals( 10, count( $published_posts ) );

		$this->factory->post->create_many( 10, array(
			'post_type' => 'page',
		) );

		$published_pages = $this->get_posts_by_post_type( 'page' );
		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'selected_types'  => array( 'post', 'page' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $posts_deleted );

	}

	/**
	 * Add tests to deleting posts from single custom post type.
	 */
	public function test_delete_posts_from_single_custom_post_type() {
		register_post_type( 'custom' );
		$this->create_posts_by_custom_post_type( 'custom', 10 );

		$custom_posts = $this->get_posts_by_post_type( 'custom' );
		$this->assertEquals( 10, count( $custom_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'custom' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

	}

	/**
	 * Add tests to deleting posts from two custom post types.
	 */
	public function test_delete_posts_from_two_custom_post_types() {
		register_post_type( 'custom' );
		$this->create_posts_by_custom_post_type( 'customa', 10 );

		$customa_posts = $this->get_posts_by_post_type( 'customa' );
		$this->assertEquals( 10, count( $customa_posts ) );

		register_post_type( 'custom' );
		$this->create_posts_by_custom_post_type( 'customb', 10 );

		$customb_posts = $this->get_posts_by_post_type( 'customb' );
		$this->assertEquals( 10, count( $customb_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'customa', 'customb' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $posts_deleted );

	}

	/**
	 * Add tests to deleting posts from one custom post type and one default post type.
	 */
	public function test_delete_posts_from_two_various_post_types() {
		register_post_type( 'custom' );
		$this->create_posts_by_custom_post_type( 'custom', 10 );

		$custom_posts = $this->get_posts_by_post_type( 'custom' );
		$this->assertEquals( 10, count( $custom_posts ) );

		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$published_posts = $this->get_posts_by_post_type();
		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'custom', 'post' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $posts_deleted );

	}

	/**
	 * Add tests to deleting posts from one custom post type and one default post type.
	 */
	public function test_force_delete_post() {
		register_post_type( 'custom' );
		$this->create_posts_by_custom_post_type( 'custom', 10 );

		$custom_posts = $this->get_posts_by_post_type( 'custom' );
		$this->assertEquals( 10, count( $custom_posts ) );

		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$published_posts = $this->get_posts_by_post_type();
		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'custom', 'post' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $posts_deleted );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	/**
	 * Add tests to deleting posts that are older than x days.
	 */
	public function test_delete_posts_that_are_older_than_x_days() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$published_posts = $this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'post' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_posts = $this->get_posts_by_post_type();
		$this->assertEquals( 0, count( $published_posts ) );

	}

	/**
	 * Add tests to deleting posts that past x days.
	 */
	public function test_delete_posts_that_are_past_x_days() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$published_posts = $this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'post' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_posts = $this->get_posts_by_post_type();
		$this->assertEquals( 0, count( $published_posts ) );

	}

	/**
	 * Add tests to deleting posts by batches.
	 */
	public function test_delete_posts_by_batchs() {

		$published_posts = $this->factory->post->create_many( 100, array(
			'post_type'   => 'post',
		) );

		$this->assertEquals( 100, count( $published_posts ) );

		$delete_options = array(
			'selected_types'  => array( 'post' ),
			'limit_to'     => 50,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $posts_deleted );

		$published_posts = $this->get_posts_by_post_type();
		$this->assertEquals( 50, count( $published_posts ) );

	}

	/**
	 * create posts by custom post type
	 */
	public function create_posts_by_custom_post_type( $post_type = 'custom', $count = 10 ) {
		$this->factory->post->create_many( $count, array(
			'post_type'   => $post_type,
		) );
	}

	/**
	 * get posts by post type
	 */
	public function get_posts_by_post_type( $post_type = 'post' ) {
		$args = array(
			'post_type'   => $post_type,
			'nopaging'    => 'true',
		);

		$wp_query = new \WP_Query();

		return $wp_query->query( $args );
	}

}
