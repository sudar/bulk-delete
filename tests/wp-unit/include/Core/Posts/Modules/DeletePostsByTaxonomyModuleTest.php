<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Posts by Taxonomy.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTaxonomyModule
 *
 * @since 6.0.0
 */
class DeletePostsByTaxonomyModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTaxonomyModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByTaxonomyModule();
	}

	/**
	 * Test deleting posts from a single taxonomy term default post type.
	 */
	public function test_that_trash_posts_from_built_in_taxonomy_terms() {
		// Create category.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );

		// Assign the cat1 to post1.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );
		
		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		
		// call our method.
		$delete_options = array(
			'selected_taxs' => 'category',
			'selected_tax_terms' => array( 'cat1' ),
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 1, $posts_deleted );

		// Assert that post status moved to trash.
		$post1_status = get_post_status( $post1 );

		$this->assertEquals( 'trash', $post1_status );

		// Assert that category has no post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1 );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );
	}

	/**
	 * test deleting posts from a single taxonomy term custom post type
	 */
	public function test_that_trash_posts_from_built_in_taxonomy_terms_in_a_custom_post_type() {
		// Create category.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );

		register_post_type( 'custom' );
		register_taxonomy( 'category', array( 'custom' ) );
		// Assign the cat1 to post1.
		$post1 = $this->factory->post->create( array( 'post_title' => 'post1', 'post_type' => 'custom', 'post_status' => 'publish', 'post_category' => array( $cat1 ) ) );
		
		$posts_in_cat1 = $this->get_posts_by_category( $cat1, 'custom' );
		
		$this->assertEquals( 1, count( $posts_in_cat1 ) );
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'custom',
			'selected_taxs' => 'category',
			'selected_tax_terms' => array( 'cat1' ),
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 1, $posts_deleted );

		// Assert that category has no post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1, 'custom' );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );

	}

	/**
	 * Test trash posts from a single taxonomy term
	 */
	public function test_that_trash_posts_from_single_taxonomy_term() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts ) );

	}

	/**
	 * Test deleting posts from a single taxonomy term
	 */
	public function test_that_delete_posts_from_single_taxonomy_term() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'force_delete'  => true,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	/**
	 * Test trash posts from a multiple taxonomy term
	 */
	public function test_that_trash_posts_from_multiple_taxonomy_term() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt_1 = wp_insert_term( 'Custom Term 1', 'custom' );
		$term_opt_2 = wp_insert_term( 'Custom Term 2', 'custom' );
		$count = 20;

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		$term_id = $term_opt_1['term_id'];
		for( $i = 1; $i <= $count; $i++ ){
			if( $i >= 10 ){
				$term_id = $term_opt_2['term_id'];
			}
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_id ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term 1', 'Custom Term 2' ),
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 20, $posts_deleted );

		// Assert that category has no post.
		$posts1 = $this->get_posts_by_custom_term( $term_opt_1['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts1 ) );

		$posts2 = $this->get_posts_by_custom_term( $term_opt_2['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts2 ) );

	}

	/**
	 * Test deleting posts from a multiple taxonomy term
	 */
	public function test_that_delete_posts_from_multiple_taxonomy_term() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt_1 = wp_insert_term( 'Custom Term 1', 'custom' );
		$term_opt_2 = wp_insert_term( 'Custom Term 2', 'custom' );
		$count = 20;

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		$term_id = $term_opt_1['term_id'];
		for( $i = 1; $i <= $count; $i++ ){
			if( $i >= 10 ){
				$term_id = $term_opt_2['term_id'];
			}
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_id ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term 1', 'Custom Term 2' ),
			'force_delete'  => true,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 20, $posts_deleted );

		// Assert that category has no post.
		$posts1 = $this->get_posts_by_custom_term( $term_opt_1['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts1 ) );

		$posts2 = $this->get_posts_by_custom_term( $term_opt_2['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts2 ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	/**
	 * Test trash posts from a single taxonomy term custom post type
	 */
	public function test_that_trash_custom_posts_from_single_taxonomy_term() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;

		$post_data = array(
			'post_type'     => 'book',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'book',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom', 'book' );
		$this->assertEquals( 0, count( $posts ) );

	}

	/**
	 * Test deleting posts from a single taxonomy term custom post type
	 */
	public function test_that_delete_custom_posts_from_single_taxonomy_term() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;

		$post_data = array(
			'post_type'     => 'book',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'book',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'force_delete'  => true,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom', 'book' );
		$this->assertEquals( 0, count( $posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	/**
	 * Test trash posts thar are older than x days
	 */
	public function test_that_trash_posts_that_are_older_than_x_days() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
			'post_date'   => $date,
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'restrict'     => true,
			'date_op'      => 'before',
			'days'         => '3',
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts ) );

	}

	/**
	 * Test deleting posts that are older than x days
	 */
	public function test_that_delete_posts_that_are_older_than_x_days() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
			'post_date'   => $date,
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'force_delete' => true,
			'restrict'     => true,
			'date_op'      => 'before',
			'days'         => '3',
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	/**
	 * Test trash posts that posted with in last x days
	 */
	public function test_that_trash_posts_posted_within_the_last_x_days() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
			'post_date'   => $date,
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'limit_to'     => -1,
			'restrict'     => true,
			'date_op'      => 'after',
			'days'         => '5',
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts ) );

	}

	/**
	 * Test deleting posts that posted with in last x days
	 */
	public function test_that_delete_posts_posted_within_the_last_x_days() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 10;
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
			'post_date'   => $date,
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => true,
			'date_op'      => 'after',
			'days'         => '5',
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom' );
		$this->assertEquals( 0, count( $posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	/**
	 * Test trash posts term in batches
	 */
	public function test_that_trash_posts_them_in_batches() {
		
		register_taxonomy( 'custom' , 'post' );
		$term_opt = wp_insert_term( 'Custom Term', 'custom' );
		$count = 100;

		$post_data = array(
			'post_type'     => 'post',
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms( $post_id, array( $term_opt['term_id'] ), 'custom' );
		}
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'limit_to'     => 50,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 50, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_term( $term_opt['term_id'], 'custom' );
		$this->assertEquals( 50, count( $posts ) );

	}

}
