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

	/**
	 * Test to remove post by one valid URL
	 */
	public function test_to_delete_one_valid_url() {
		$post = $this->factory->post->create();

		$url = get_permalink( $post );

		// call our method.
		$delete_options = array(
			'urls'         => array( $url ),
			'restrict'     => false,
			'limit_to'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 1, $posts_deleted );
	}

	/**
	 * Test to remove post by one invalid URL
	 */
	public function test_to_delete_one_invalid_url() {
		$url = 'http://invalidurl.com/';

		// call our method.
		$delete_options = array(
			'urls'         => array( $url ),
			'restrict'     => false,
			'limit_to'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 0, $posts_deleted );
	}

	/**
	 * Test to remove post by one valid URL and one invalid URL
	 */
	public function test_to_delete_one_valid_and_one_invalid_url() {
		$post      = $this->factory->post->create();
		$valid_url = get_permalink( $post );

		$invalid_url = 'http://invalidurl.com/';

		// call our method.
		$delete_options = array(
			'urls'         => array( $valid_url, $invalid_url ),
			'restrict'     => false,
			'limit_to'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 1, $posts_deleted );
	}

	/**
	 * Test to remove post by multiple valid URL.
	 */
	public function test_to_delete_many_valid_urls() {
		$post1 = $this->factory->post->create();
		$url1  = get_permalink( $post1 );

		$post2 = $this->factory->post->create();
		$url2  = get_permalink( $post2 );

		// call our method.
		$delete_options = array(
			'urls'         => array( $url1, $url2 ),
			'restrict'     => false,
			'limit_to'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted posts.
		$this->assertEquals( 2, $posts_deleted );
	}

	/**
	 * Test to force remove post by URL.
	 */
	public function test_to_force_delete_post_by_url() {
		$post1 = $this->factory->post->create();
		$url1  = get_permalink( $post1 );

		$post2 = $this->factory->post->create();
		$url2  = get_permalink( $post2 );

		$url3 = 'http://invalidurl.com/';

		// call our method.
		$delete_options = array(
			'urls'         => array( $url1, $url2, $url3 ),
			'restrict'     => false,
			'limit_to'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 2, $posts_deleted );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );
	}
}
