<?php

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Sample test case.
 */
class Bulk_Delete_Posts_By_Post_TagTest extends WPCoreUnitTestCase {

	/**
	 * A single example test.
	 */
	protected $delete_by_post_tag;

	public function setUp() {
		parent::setUp();

		$this->delete_by_post_tag = new \Bulk_Delete_Posts();
	}

	/**
	 * Test basic case of delete posts by tag.
	 */
	public function test_delete_posts_by_tag_with_all_option_and_default_filters() {
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish' ) );
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish' ) );
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each post status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags'  => array( 'all' ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->delete_by_post_tag->delete_posts_by_tag( $delete_options );

		// Assert that each post status moved to trash.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'trash', $post2_status );

		// Assert that each tag has no post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 0, count( $posts_in_tag1 ) );
		$this->assertEquals( 0, count( $posts_in_tag2 ) );
	}
}