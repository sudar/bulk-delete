<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of posts by category.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByCategoryModule
 *
 * @since 6.0.0
 */
class DeletePostsByCategoryModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByCategoryModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByCategoryModule();
	}

	/**
	 * Add tests to test deleting posts from All categories by default post type.
	 */
	public function test_that_posts_from_all_categories_in_default_post_type_can_be_trashed() {
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( 'all' ),
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

		// Assert that each category has no post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts from one category in the default post type.
	 */
	public function test_that_posts_from_one_category_in_default_post_type_can_be_trashed() {
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( $cat1 ),
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
	 * Add tests to test deleting posts from more than one category in default post type.
	 */
	public function test_that_posts_from_more_than_one_category_in_default_post_type_can_be_trashed() {
		// Create three categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );
		$cat3 = $this->factory->category->create( array( 'name' => 'cat3' ) );

		// Assign the cat1, cat2 and cat3 to post1, post2 and post3.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );
		$post3 = $this->factory->post->create( array( 'post_title' => 'post3', 'post_status' => 'publish', 'post_category' => array( $cat3 ) ) );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );
		$posts_in_cat3 = $this->get_posts_by_category( $cat3 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
		$this->assertEquals( 1, count( $posts_in_cat3 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( $cat1, $cat2 ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that post1 and post2 status moved to trash post3 status is publish.
		$this->assertEquals( 2, $posts_deleted );

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
	 * Add tests to test deleting posts permanently from one category in default post type.
	 */
	public function test_that_posts_from_one_category_in_default_post_type_can_be_permanently_deleted() {
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( $cat1 ),
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

		// Assert that post2 status is publish.
		$post2_status = get_post_status( $post2 );

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
	public function test_that_posts_older_than_x_days_can_be_trashed() {
		// Set post publish date.
		$day_post = date( 'Y-m-d H:i:s', strtotime( '-2 day' ) );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ), 'post_date' => $day_post ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( $cat1, $cat2 ),
			'restrict'      => true,
			'date_op'       => 'before',
			'days'          => 1,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 1 post.
		$this->assertEquals( 1, $posts_deleted );

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
	public function test_that_posts_published_in_last_x_days_can_be_trashed() {
		// Set post publish date.
		$day_post = date( 'Y-m-d H:i:s', strtotime( '-2 day' ) );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ), 'post_date' => $day_post ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( $cat1, $cat2 ),
			'restrict'      => true,
			'date_op'       => 'after',
			'days'          => 1,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 1 post.
		$this->assertEquals( 1, $posts_deleted );

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

	/**
	 * Test that posts can be deleted in batches.
	 */
	public function test_that_posts_can_be_deleted_in_batches() {
		// Create a category.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );

		// Create 100 posts and assign all of them to cat1.
		$this->factory->post->create_many( 100, array(
			'post_status'   => 'publish',
			'post_category' => array( $cat1 ),
		) );

		// Assert that cat1 has 100 posts.
		$posts = $this->get_posts_by_category( $cat1 );
		$this->assertEquals( 100, count( $posts ) );

		// call our method. First batch limit_to 20 posts.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( $cat1 ),
			'restrict'      => false,
			'date_op'       => false,
			'days'          => false,
			'private'       => false,
			'limit_to'      => 20,
			'force_delete'  => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 20 posts.
		$this->assertEquals( 20, $posts_deleted );

		// Assert that cat1 has 80 posts.
		$posts = $this->get_posts_by_category( $cat1 );
		$this->assertEquals( 80, count( $posts ) );

		// call our method. Second batch limit_to 30 posts.
		$delete_options = array(
			'post_type'     => array( 'post' ),
			'selected_cats' => array( $cat1 ),
			'restrict'      => false,
			'date_op'       => false,
			'days'          => false,
			'private'       => false,
			'limit_to'      => 30,
			'force_delete'  => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 30 posts.
		$this->assertEquals( 30, $posts_deleted );

		// Assert that cat1 has 50 posts.
		$posts = $this->get_posts_by_category( $cat1 );
		$this->assertEquals( 50, count( $posts ) );
	}

	/**
	 * Add tests to test deleting posts from All categories in a custom post type.
	 */
	public function test_that_posts_from_all_categories_in_custom_post_type_can_be_trashed() {
		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Create two posts and assign them to categories just created.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat2, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( $post_type ),
			'selected_cats' => array( 'all' ),
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

		// Assert that each post is moved to trash.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'trash', $post2_status );

		// Assert that each terms has no post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts from one categories by custom post type.
	 */
	public function test_that_posts_from_one_category_in_custom_post_type_can_be_trashed() {
		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Create two posts and assign them to categories just created.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat2, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( $post_type ),
			'selected_cats' => array( $cat1 ),
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

		// Assert that post2 status is publish and post1 status is trash.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that cat1 has no post and cat2 has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts from more than one category by custom post type.
	 */
	public function test_that_posts_from_more_than_one_category_in_custom_post_type_can_be_trashed() {
		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create three categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );
		$cat3 = $this->factory->category->create( array( 'name' => 'cat3' ) );

		// Create three posts and assign each of them to one category.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );
		$post3 = $this->factory->post->create( array( 'post_title' => 'post3', 'post_type' => $post_type ) );

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat2, 'category' );
		wp_set_object_terms( $post3, $cat3, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );
		$posts_in_cat3 = $this->get_posts_by_custom_term( $cat3, 'category', $post_type );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
		$this->assertEquals( 1, count( $posts_in_cat3 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( $post_type ),
			'selected_cats' => array( $cat1, $cat2 ),
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted 2 posts.
		$this->assertEquals( 2, $posts_deleted );

		// Assert that post1 and post2 are in trash and post3 status is publish.
		$post1_status = get_post_status( $post1 );
		$post2_status = get_post_status( $post2 );
		$post3_status = get_post_status( $post3 );

		$this->assertEquals( 'trash', $post1_status );
		$this->assertEquals( 'trash', $post2_status );
		$this->assertEquals( 'publish', $post3_status );

		// Assert that cat1, cat2 has no post and cat1 has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );
		$posts_in_cat3 = $this->get_posts_by_custom_term( $cat3, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
		$this->assertEquals( 1, count( $posts_in_cat3 ) );
	}

	/**
	 * Add tests to test deleting posts permanently by custom post type.
	 */
	public function test_that_posts_from_one_category_in_custom_post_type_can_be_deleted() {
		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Create two posts and assign them to categories just created.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat2, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );

		// call our method.
		$delete_options = array(
			'post_type'     => array( $post_type ),
			'selected_cats' => array( $cat1 ),
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

		// Assert that post that belong to cat2 are still published.
		$post2_status = get_post_status( $post2 );
		$this->assertEquals( 'publish', $post2_status );

		// Assert that cat1 has 0 posts and cat2 has 1 post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );
		$posts_in_cat2 = $this->get_posts_by_custom_term( $cat2, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 1, count( $posts_in_cat2 ) );
	}

	/**
	 * Register a custom post type and then associate `category` taxonomy to it.
	 *
	 * @param string $post_type Post type name.
	 */
	protected function register_post_type_and_associate_category( $post_type ) {
		register_post_type( $post_type );
		register_taxonomy_for_object_type( 'category', $post_type );
	}
}
