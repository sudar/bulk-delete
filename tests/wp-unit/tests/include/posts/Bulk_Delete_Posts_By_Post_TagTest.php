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

	/**
	 * Test case of delete posts by tag all option filters are private posts and delete permanently.
	 */
	public function test_delete_posts_by_tag_with_all_option_and_delete_permanently_private_posts_filters() {
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish' ) );
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'private' ) );
		wp_set_post_tags( $post2, 'tag2' );

		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$args = array(
			'posts_per_page'   => -1,
			'post_status'      => 'private',
			'tag'              => 'tag2',
		);
		$post2_array = get_posts( $args );
		$posts_in_tag2 = wp_list_pluck( $post2_array, 'ID' );

		// Assert that each tag1 has one post and tag2 has one post.
		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( array( $post2 ), $posts_in_tag2 );

		// call our method.
		$delete_options = array(
			'selected_tags'  => array( 'all' ),
			'restrict'       => false,
			'private'        => true,
			'limit_to'       => false,
			'force_delete'   => true,
			'date_op'        => false,
			'days'           => false,
		);
		$this->delete_by_post_tag->delete_posts_by_tag( $delete_options );

		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$args = array(
			'posts_per_page'   => -1,
			'post_status'      => 'private',
			'tag'              => 'tag2',
		);
		$post2_array = get_posts( $args );
		$posts_in_tag2 = wp_list_pluck( $post2_array, 'ID' );

		// Assert that each tag1 has one post and tag2 has 0 post.
		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( array( ), $posts_in_tag2 );
	}

	/**
	 * Test case of delete posts by a tag with default filters.
	 */
	public function test_delete_posts_by_tag_with_default_filters() {
		//Create a tag
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );

		// Assign the tag1 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish' ) );
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'private' ) );
		wp_set_post_tags( $post2, 'tag1' );

		$args = array(
			'posts_per_page'   => -1,
			'post_status'      => array( 'private', 'publish' ),
			'tag'              => 'tag1',
		);
		$posts_array = get_posts( $args );
		$posts_in_tag1 = wp_list_pluck( $posts_array, 'ID' );

		// Assert that tag1 has two posts.
		$this->assertEquals( array( $post1, $post2 ), $posts_in_tag1 );

		// Assert that post1 status is publish and post 2 is private.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'private', $post2_status );

		// call our method.
		$delete_options = array(
			'selected_tags'  => array( $tag1 ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->delete_by_post_tag->delete_posts_by_tag( $delete_options );

		$args = array(
			'posts_per_page'   => -1,
			'post_status'      => array( 'private', 'publish' ),
			'tag'              => 'tag1',
		);
		$posts_array = get_posts( $args );
		$posts_in_tag1 = wp_list_pluck( $posts_array, 'ID' );

		// Assert that tag1 has one posts.
		$this->assertEquals( array( $post2 ), $posts_in_tag1 );

		// Assert that post1 status is trash and post 2 is private.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'private', $post2_status );
	}
}