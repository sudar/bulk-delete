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

		$this->create_posts_with_custom_taxonomy( 'custom', 'Custom Term', 10 );
		$posts = $this->get_posts_by_custom_taxonomy( "custom", "Custom Term" );
		$this->assertEquals( 10, count( $posts ) );
	}

	/**
	 * Helper function to create posts with custom taxonomy
	 */
	public function create_posts_with_custom_taxonomy( $taxonomy = "custom", $term = "Custom Term", $count = 10) {

		register_taxonomy( $taxonomy, 'post' );
		$t = wp_insert_term( $term, $taxonomy );

		$post_data = array(
			'post_title'    => 'Sample Post',
			'post_status'   => 'publish',
		);

		for( $i = 1; $i <= $count; $i++ ){
			$post_id  = wp_insert_post($post_data);
			$termObj  = get_term_by( 'id', array( $t['term_id'] ), $taxonomy );
			wp_set_object_terms($post_id, $termObj, $taxonomy);
		}

	}

	/**
	 * Helper function to get posts by custom taxonomy
	 */
	public function get_posts_by_custom_taxonomy( $taxonomy = "custom", $term = "Custom Term" ) {

		$args = array(
			'post_type' => 'post',
			'numberposts' => -1,
			// 'tax_query' => array(
			// 	array(
			// 		'taxonomy' => $taxonomy,
			// 		'field' => 'name',
			// 		'terms' => $term
			// 	)
			// )
		);

		$posts = get_posts( $args );

		print_r($posts);

		return $posts;
	}

}
