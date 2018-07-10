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

	public function test_deleting_posts_from_built_in_taxonomy_terms() {
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
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => false,
			'days'          => false,
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

	public function test_deleting_posts_from_built_in_taxonomy_terms_in_a_custom_post_type() {
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
			'restrict'      => false,
			'private'       => false,
			'limit_to'      => false,
			'force_delete'  => false,
			'date_op'       => false,
			'days'          => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 1, $posts_deleted );

		// Assert that category has no post.
		$posts_in_cat1 = $this->get_posts_by_category( $cat1, 'custom' );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );

	}

	public function test_that_trash_posts_from_single_taxonomy_term() {
		
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term', 10 );
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'restrict'      => false,
			'limit_to'      => false,
			'force_delete'  => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term" );
		$this->assertEquals( 0, count( $posts ) );

	}

	public function test_that_delete_posts_from_single_taxonomy_term() {
		
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term', 10 );
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'restrict'      => false,
			'force_delete'  => true,
			'limit_to'      => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term" );
		$this->assertEquals( 0, count( $posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	public function test_that_trash_posts_from_multiple_taxonomy_term() {
		
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term 1', 10 );
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term 2', 10 );
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term 1', 'Custom Term 2' ),
			'restrict'      => false,
			'limit_to'      => false,
			'force_delete'  => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 20, $posts_deleted );

		// Assert that category has no post.
		$posts1 = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term 1" );
		$this->assertEquals( 0, count( $posts1 ) );

		$posts2 = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term 2" );
		$this->assertEquals( 0, count( $posts2 ) );

	}

	public function test_that_delete_posts_from_multiple_taxonomy_term() {
		
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term 1', 10 );
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term 2', 10 );
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'post',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term 1', 'Custom Term 2' ),
			'restrict'      => false,
			'force_delete'  => true,
			'limit_to'      => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 20, $posts_deleted );

		// Assert that category has no post.
		$posts1 = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term 1" );
		$this->assertEquals( 0, count( $posts1 ) );

		$posts2 = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term 2" );
		$this->assertEquals( 0, count( $posts2 ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	public function test_that_trash_custom_posts_from_single_taxonomy_term() {
		
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term', 10, 'book' );
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'book',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'restrict'      => false,
			'limit_to'      => false,
			'force_delete'  => false,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term", 'book' );
		$this->assertEquals( 0, count( $posts ) );

	}

	public function test_that_delete_custom_posts_from_single_taxonomy_term() {
		
		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term', 10, 'book' );
		
		// call our method.
		$delete_options = array(
			'post_type'     => 'book',
			'selected_taxs' => 'custom',
			'selected_tax_terms' => array( 'Custom Term' ),
			'restrict'      => false,
			'limit_to'      => false,
			'force_delete'  => true,
		);
		$posts_deleted = $this->module->delete( $delete_options );

		// Assert that delete method has deleted post.
		$this->assertEquals( 10, $posts_deleted );

		// Assert that category has no post.
		$posts = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term", 'book' );
		$this->assertEquals( 0, count( $posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

	/**
	 * Helper function to create posts with custom taxonomy
	 */
	public function create_posts_with_custom_taxonomy( $taxonomy = "custom", $term = "Custom Term", $count = 10, $post_type = "post" ) {

		register_taxonomy( $taxonomy, 'post' );
		$term_opt = wp_insert_term( $term, $taxonomy );

		$post_data = array(
			'post_type'     => $post_type,
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			wp_set_object_terms($post_id, array( $term_opt['term_id'] ), $taxonomy);
		}

	}

	/**
	 * Helper function to get posts by custom taxonomy
	 */
	public function get_posts_by_custom_taxonomy( $taxonomy = "custom", $term = "Custom Term", $post_type = "post" ) {

		$args = array(
			'post_type' => $post_type,
			'numberposts' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'name',
					'terms' => $term
				)
			)
		);

		$posts = get_posts( $args );

		return $posts;
	}

}
