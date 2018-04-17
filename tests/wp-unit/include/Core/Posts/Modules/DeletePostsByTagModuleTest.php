<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of posts by tag.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTagModule
 *
 * @since 6.0.0
 */
class DeletePostsByTagModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTagModule
	 */
	protected $module;

	/**
	 * Base move class setup
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByTagModule();
	}

	/**
	 * Add tests to test deleting posts from All tags.
	 */
	public function test_for_deleting_posts_from_all_tags() {
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2.
		$post1 = $this->factory->post->create(
			array(
				'post_title'  => 'post1',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create(
			array(
				'post_title'  => 'post2',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags' => array( 'all' ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted all (2) posts.
		$this->assertEquals( 2, $posts_deleted );

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
	 * Add tests to test deleting posts from one tag.
	 */
	public function test_for_deleting_posts_from_one_tag() {
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2.
		$post1 = $this->factory->post->create(
			array(
				'post_title'  => 'post1',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create(
			array(
				'post_title'  => 'post2',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags' => array( $tag1 ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 1 post.
		$this->assertEquals( 1, $posts_deleted );

		// Assert that post1 moved to trash and post2 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that tag1 has no post and tag2 has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 0, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );
	}

	/**
	 * Add tests to test deleting posts from more than one tag.
	 */
	public function test_for_deleting_posts_from_more_than_one_tag() {
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2.
		$post1 = $this->factory->post->create(
			array(
				'post_title'  => 'post1',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create(
			array(
				'post_title'  => 'post2',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags' => array( $tag1, $tag2 ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that post1 and post2 status moved to trash.
		$this->assertEquals( 2, $posts_deleted );

		// Assert that each posts moved to trash.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'trash', $post2_status );

		// Assert that each tags has no post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 0, count( $posts_in_tag1 ) );
		$this->assertEquals( 0, count( $posts_in_tag2 ) );
	}

	/**
	 * Add tests to test deleting posts permanently with all option.
	 */
	public function test_for_deleting_posts_permanently_with_all_option() {
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2.
		$post1 = $this->factory->post->create(
			array(
				'post_title'  => 'post1',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create(
			array(
				'post_title'  => 'post2',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags' => array( 'all' ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => true,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted all (2) posts.
		$this->assertEquals( 2, $posts_deleted );

		// Assert that each tags has no post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 0, count( $posts_in_tag1 ) );
		$this->assertEquals( 0, count( $posts_in_tag2 ) );
	}

	/**
	 * Add tests to test deleting posts permanently with all one tag.
	 */
	public function test_for_deleting_posts_permanently_with_one_tag() {
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2.
		$post1 = $this->factory->post->create(
			array(
				'post_title'  => 'post1',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create(
			array(
				'post_title'  => 'post2',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags' => array( $tag1 ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => true,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 1 post.
		$this->assertEquals( 1, $posts_deleted );

		// Assert that each tag1 has no post and tag2 has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 0, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );
	}

	/**
	 * Add tests to test deleting posts that are older than x days.
	 */
	public function test_for_delete_posts_older_than_x_days() {
		// Set post publish date.
		$day_post = date( 'Y-m-d H:i:s', strtotime( '-2 day' ) );
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2.
		$post1 = $this->factory->post->create(
			array(
				'post_title'  => 'post1',
				'post_status' => 'publish',
				'post_date'   => $day_post,
			)
		);
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create(
			array(
				'post_title'  => 'post2',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags' => array( 'all' ),
			'restrict'      => true,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => 'before',
			'days'          => 1,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 1 post.
		$this->assertEquals( 1, $posts_deleted );

		// Assert that post1 status moved to trash post2 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that each tag1 has no post and tag2 has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 0, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );
	}

	/**
	 * Add tests to test deleting posts that are posted within the last x days.
	 */
	public function test_for_delete_posts_last_x_days() {
		// Set post publish date.
		$day_post = date( 'Y-m-d H:i:s', strtotime( '-2 day' ) );
		// Create two tags.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		$tag2 = $this->factory->tag->create( array( 'name' => 'tag2' ) );

		// Assign the tags tag1 and tag2 to post1 and post2.
		$post1 = $this->factory->post->create(
			array(
				'post_title'  => 'post1',
				'post_status' => 'publish',
				'post_date'   => $day_post,
			)
		);
		wp_set_post_tags( $post1, 'tag1' );

		$post2 = $this->factory->post->create(
			array(
				'post_title'  => 'post2',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $post2, 'tag2' );

		// Assert that each tag has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 1, count( $posts_in_tag2 ) );

		// call our method.
		$delete_options = array(
			'selected_tags' => array( 'all' ),
			'restrict'      => true,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => 'after',
			'days'          => 1,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 1 post.
		$this->assertEquals( 1, $posts_deleted );

		// Assert that post2 status moved to trash post1 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'trash', $post2_status );

		// Assert that each tag2 has no post and tag1 has one post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$posts_in_tag2 = $this->get_posts_by_tag( $tag2 );

		$this->assertEquals( 1, count( $posts_in_tag1 ) );
		$this->assertEquals( 0, count( $posts_in_tag2 ) );
	}

	/**
	 * Create more than 100 posts and try to delete them in batches. Test at least 2 batches.
	 */
	public function test_for_delete_posts_as_batches() {
		// Create a tag.
		$tag1 = $this->factory->tag->create( array( 'name' => 'tag1' ) );
		// Create 100 posts and assign the tag1 to all 100 posts.
		$post_ids = $this->factory->post->create_many(
			100, array(
				'post_status' => 'publish',
			)
		);

		foreach ( $post_ids as $post_id ) {
			wp_set_post_tags( $post_id, 'tag1' );
		}

		// Assert that each tag1 has 100 post.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$this->assertEquals( 100, count( $posts_in_tag1 ) );

		// call our method. First batch limit_to 20 posts.
		$delete_options = array(
			'selected_tags' => array( $tag1 ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => 20,
			'force_delete'  => true,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 20 posts.
		$this->assertEquals( 20, $posts_deleted );

		// Assert that tag1 has 80 posts.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$this->assertEquals( 80, count( $posts_in_tag1 ) );

		// call our method. Second batch limit_to 30 posts.
		$delete_options = array(
			'selected_tags' => array( $tag1 ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => 30,
			'force_delete'  => true,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 30 posts.
		$this->assertEquals( 30, $posts_deleted );

		// Assert that tag1 has 50 posts.
		$posts_in_tag1 = $this->get_posts_by_tag( $tag1 );
		$this->assertEquals( 50, count( $posts_in_tag1 ) );
	}
}
