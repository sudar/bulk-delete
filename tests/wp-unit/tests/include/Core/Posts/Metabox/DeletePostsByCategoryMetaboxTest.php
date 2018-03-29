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
	 * Register Custom Post Type.
	 */
	protected function register_post_type_for_bulk_delete() {
		/**
		 * Post Type: movies.
		 */

		$labels = array(
			"name" => __( "movies", "bulk-delete" ),
			"singular_name" => __( "movie", "bulk-delete" ),
		);

		$args = array(
			"label" => __( "movies", "bulk-delete" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => false,
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "movie", "with_front" => true ),
			"query_var" => true,
			"supports" => array( "title", "editor", "thumbnail" ),
			"taxonomies" => array( "category" ),
		);

		register_post_type( "movie", $args );
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

		// Assert that each category has no post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
	}

	/**
	 * Add tests to test deleting posts from one category by default post type.
	 */
	public function test_for_deleting_posts_from_one_category_default_post_type() {
		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		$cat2 = $this->factory->category->create( array( 'name' => 'cat2' ) );

		// Assign the cat1 and cat2 to post1 and post2
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

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
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ), 'post_date'     => $day_post, 'post_date_gmt' => $day_post ) );
		$time = current_time('mysql');

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

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
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ), 'post_date'     => $day_post, 'post_date_gmt' => $day_post ) );
		$time = current_time('mysql');

		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_status' => 'publish', 'post_category' => array( $cat2 ) ) );

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

		// Assert that cat2 has no post and cat1 has one post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		$posts_in_cat2 = $this->get_posts_by_category( $cat2 );

		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		$this->assertEquals( 0, count( $posts_in_cat2 ) );
	}

	/**
	 * Create more than 100 posts and try to delete them in batches. Test at least 2 batches.
	 */
	public function test_for_delete_posts_as_batches() {
		// Create a category.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );
		// Create 100 posts and assign the cat1 to all 100 posts.
		$this->factory->post->create_many( 100, array(
			'post_status' => 'publish',
			'post_category' => array( $cat1 ),
		) );

		// Assert that cat1 has 100 posts.
		$posts = $this->get_posts_by_category( $cat1 );
		$this->assertEquals( 100, count( $posts ) );

		// call our method. First batch limit_to 20 posts
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( $cat1 ),
			'restrict'       => false,
			'date_op'        => false,
			'days'           => false,
			'private'        => false,
			'limit_to'       => 20,
			'force_delete'   => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that cat1 has 80 posts.
		$posts = $this->get_posts_by_category( $cat1 );
		$this->assertEquals( 80, count( $posts ) );

		// call our method. Second batch limit_to 30 posts
		$delete_options = array(
			'post_type'      => array( 'post' ),
			'selected_cats'  => array( $cat1 ),
			'restrict'       => false,
			'date_op'        => false,
			'days'           => false,
			'private'        => false,
			'limit_to'       => 30,
			'force_delete'   => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that cat1 has 50 posts.
		$posts = $this->get_posts_by_category( $cat1 );
		$this->assertEquals( 50, count( $posts ) );
	}

	/**
	 * Add tests to test deleting posts from All categories by custom post type.
	 */
	public function test_for_deleting_posts_from_all_categories_custom_post_type() {
		$this->register_post_type_for_bulk_delete();
		$taxonomy  = 'category';
		$post_type = 'movie';

		// Create two terms.
		$term1 = $this->factory->term->create( array( 'name' => 'term1', 'taxonomy' => $taxonomy ) );
		$term2 = $this->factory->term->create( array( 'name' => 'term2', 'taxonomy' => $taxonomy ) );

		// Create two posts and assign the term.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );
		wp_set_object_terms( $post1, $term1, $taxonomy );
		wp_set_object_terms( $post2, $term2, $taxonomy );

		// Assert that each terms has one post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );

		$this->assertEquals( 1, count( $posts_in_term1 ) );
		$this->assertEquals( 1, count( $posts_in_term2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( $post_type ),
			'selected_cats'  => array( 'all' ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that each terms has no post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );

		$this->assertEquals( 0, count( $posts_in_term1 ) );
		$this->assertEquals( 0, count( $posts_in_term2 ) );
	}

	/**
	 * Add tests to test deleting posts from one categories by custom post type.
	 */
	public function test_for_deleting_posts_from_one_category_custom_post_type() {
		$this->register_post_type_for_bulk_delete();
		$taxonomy  = 'category';
		$post_type = 'movie';

		// Create two terms.
		$term1 = $this->factory->term->create( array( 'name' => 'term1', 'taxonomy' => $taxonomy ) );
		$term2 = $this->factory->term->create( array( 'name' => 'term2', 'taxonomy' => $taxonomy ) );

		// Create two posts and assign the term.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );
		wp_set_object_terms( $post1, $term1, $taxonomy );
		wp_set_object_terms( $post2, $term2, $taxonomy );

		// Assert that each terms has one post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );

		$this->assertEquals( 1, count( $posts_in_term1 ) );
		$this->assertEquals( 1, count( $posts_in_term2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( $post_type ),
			'selected_cats'  => array( $term1 ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that term1 has no post and term2 has one post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );

		$this->assertEquals( 0, count( $posts_in_term1 ) );
		$this->assertEquals( 1, count( $posts_in_term2 ) );
	}

	/**
	 * Add tests to test deleting posts from more than one category by custom post type.
	 */
	public function test_for_deleting_posts_from_more_than_one_categories_custom_post_type() {
		$this->register_post_type_for_bulk_delete();
		$taxonomy  = 'category';
		$post_type = 'movie';

		// Create three terms.
		$term1 = $this->factory->term->create( array( 'name' => 'term1', 'taxonomy' => $taxonomy ) );
		$term2 = $this->factory->term->create( array( 'name' => 'term2', 'taxonomy' => $taxonomy ) );
		$term3 = $this->factory->term->create( array( 'name' => 'term3', 'taxonomy' => $taxonomy ) );

		// Create three posts and assign the term.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );
		$post3 = $this->factory->post->create( array( 'post_title' => 'post3', 'post_type' => $post_type ) );
		wp_set_object_terms( $post1, $term1, $taxonomy );
		wp_set_object_terms( $post2, $term2, $taxonomy );
		wp_set_object_terms( $post3, $term3, $taxonomy );

		// Assert that each terms has one post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );
		$posts_in_term3 = $this->get_posts_by_custom_term( $term3, $taxonomy, $post_type );

		$this->assertEquals( 1, count( $posts_in_term1 ) );
		$this->assertEquals( 1, count( $posts_in_term2 ) );
		$this->assertEquals( 1, count( $posts_in_term3 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( $post_type ),
			'selected_cats'  => array( $term1, $term2 ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => false,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that term1, term2 has no post and term1 has one post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );
		$posts_in_term3 = $this->get_posts_by_custom_term( $term3, $taxonomy, $post_type );

		$this->assertEquals( 0, count( $posts_in_term1 ) );
		$this->assertEquals( 0, count( $posts_in_term2 ) );
		$this->assertEquals( 1, count( $posts_in_term3 ) );
	}

	/**
	 * Add tests to test deleting posts permanently by custom post type.
	 */
	public function test_for_deleting_posts_permanently_from_one_category_custom_post_type() {
		$this->register_post_type_for_bulk_delete();
		$taxonomy  = 'category';
		$post_type = 'movie';

		// Create two terms.
		$term1 = $this->factory->term->create( array( 'name' => 'term1', 'taxonomy' => $taxonomy ) );
		$term2 = $this->factory->term->create( array( 'name' => 'term2', 'taxonomy' => $taxonomy ) );

		// Create two posts and assign the term.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => $post_type ) );
		$post2 = $this->factory->post->create( array( 'post_title' => 'post2', 'post_type' => $post_type ) );
		wp_set_object_terms( $post1, $term1, $taxonomy );
		wp_set_object_terms( $post2, $term2, $taxonomy );

		// Assert that each terms has one post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );

		$this->assertEquals( 1, count( $posts_in_term1 ) );
		$this->assertEquals( 1, count( $posts_in_term2 ) );

		// call our method.
		$delete_options = array(
			'post_type'      => array( $post_type ),
			'selected_cats'  => array( $term1 ),
			'restrict'       => false,
			'private'        => false,
			'limit_to'       => false,
			'force_delete'   => true,
			'date_op'        => false,
			'days'           => false,
		);
		$this->metabox->delete( $delete_options );

		// Assert that each terms has one post.
		$posts_in_term1 = $this->get_posts_by_custom_term( $term1, $taxonomy, $post_type );
		$posts_in_term2 = $this->get_posts_by_custom_term( $term2, $taxonomy, $post_type );

		$this->assertEquals( 0, count( $posts_in_term1 ) );
		$this->assertEquals( 1, count( $posts_in_term2 ) );
	}
}