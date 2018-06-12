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
	 * Add tests to test deleting posts from All categories by default post type.
	 */
	public function test_that_posts_from_all_categories_in_default_post_type_can_be_trashed() {
		$this->create_posts_by_custom_post_type( 'custom', 50 );

		$custom_posts = $this->get_posts_by_post_type( 'custom' );
		$this->assertEquals( 50, count( $custom_posts ) );

	}

	/**
	 * Helper function.
	 * create posts by custom post type
	 */
	public function create_posts_by_custom_post_type( $post_type = 'custom', $count = 10 ) {
		register_post_type( $post_type );

		$this->factory->post->create_many( $count, array(
			'post_type'   => $post_type,
		) );

	}

	/**
	 * Helper function.
	 * create post by custom post type
	 */
	public function create_post_by_custom_post_type( $post_type = 'custom' ) {
		register_post_type( $post_type );

		$this->factory->post->create( array(
			'post_type'   => $post_type,
		) );

	}

	/**
	 * Helper function.
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
