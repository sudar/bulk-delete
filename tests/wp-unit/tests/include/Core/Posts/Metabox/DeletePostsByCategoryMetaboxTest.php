<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of posts by category.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByCategoryMetabox
 *
 * @since 6.0.0
 */
class DeletePostsByCategoryMetaboxTest extends WPCoreUnitTestCase {

	/**
	 * The metabox that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByCategoryMetabox
	 */
	protected $metabox;

	public function setUp() {
		parent::setUp();

		$this->metabox = new DeletePostsByCategoryMetabox();
	}

	/**
	 * Add tests to test deleting posts from All categories by default post type.
	 */
	public function test_for_deleting_posts_from_all_categories_default_post_type() {
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each post status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( 'all' ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that each post status moved to trash.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'trash', $post2_status );

		// Assert that each category has no post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts from one category by default post type.
	 */
	public function test_for_deleting_posts_from_one_categories_default_post_type() {
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each post status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( $cat1 ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that post1 status moved to trash post2 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that cat1 has no post cat2 has 1 post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts from more than one category by default post type.
	 */
	public function test_for_deleting_posts_from_more_than_one_categories_default_post_type() {
		// Create three categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );
		$cat3 = $this->factory->category->create( array( 'name' => 'cat3' ) );

		// Assign the cat1, cat2 and cat3 to post1, post2 and post3
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		$post3 = $this->factory->post->create( array( 'post_title' => 'post3', 'post_status' => 'publish', 'post_category' => array( $cat3 ) ) );

		// Assert that each post status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );
		$post3_status = get_post_status( $post3 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'publish', $post2_status );
		$this->assertEquals( 'publish', $post3_status );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );
		$posts_in_cat3 = $this->get_posts_by_category( $cat3 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
		$this->assertEquals( 1, count( $posts_in_cat3 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( $cat1, $cat2 ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that post1 and post2 status moved to trash post3 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );
		$post3_status = get_post_status( $post3 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'trash', $post2_status );
		$this->assertEquals( 'publish', $post3_status );

		// Assert that cat1 and cat2 has no post cat3 has 1 post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );
		$posts_in_cat3 = $this->get_posts_by_category( $cat3 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
		$this->assertEquals( 1, count( $posts_in_cat3 ) );
	}

	/**
	 * Add tests to test deleting posts permanently by default post type.
	 */
	public function test_for_deleting_posts_permanently_from_one_categories_default_post_type() {
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each post status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( $cat1 ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => true,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that post1 status deleted permanently post2 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( '', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that cat1 has no post cat2 has 1 post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts that are older than x days.
	 */
	public function test_for_delete_posts_older_than_x_days() {
		//Set post publish date
		$day_post = date('Y-m-d H:i:s', strtotime('-2 day'));
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		$time = current_time('mysql');
		// Update post1 publish date.
		wp_update_post(
			array (
			'ID'            => $post1,
			'post_date'     => $day_post,
			'post_date_gmt' => $day_post,
			)
		);

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each post status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( $cat1, $cat2 ),
			'restrict'       => true,
			'date_op'        => 'before',
			'days'           => 1,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that post1 status moved to trash post2 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that cat1 has no post and cat2 has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts that are posted within the last x days.
	 */
	public function test_for_delete_posts_last_x_days() {
		//Set post publish date
		$day_post = date('Y-m-d H:i:s', strtotime('-2 day'));
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		$time = current_time('mysql');
		// Update post1 publish date.
		wp_update_post(
			array (
			'ID'            => $post1,
			'post_date'     => $day_post,
			'post_date_gmt' => $day_post,
			)
		);

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each post status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( $cat1, $cat2 ),
			'restrict'       => true,
			'date_op'        => 'after',
			'days'           => 1,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that post2 status moved to trash post1 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'publish', $post1_status );
		$this->assertEquals( 'trash', $post2_status );

		// Assert that cat2 has no post and cat1 has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
	}
}