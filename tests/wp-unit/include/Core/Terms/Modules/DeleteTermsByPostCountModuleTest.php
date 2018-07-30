<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Delete Taxonomy terms by post count.
 *
 * Tests \BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByPostCountModule
 *
 * @since 6.1.0
 */
class DeleteTermsByPostCountModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByPostCountModule
	 */
	protected $module;

	/**
	 * Setup the Module.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeleteTermsByPostCountModule();
	}

	/**
	 * Add tests to delete term with equal state.
	 */
	public function test_that_delete_terms_with_equal_state() {

		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );

		// Create two posts and assign them to categories just created.
		$post1 = $this->factory->post->create(
			array(
				'post_title' => 'post1',
				'post_type'  => $post_type,
			)
		);
		$post2 = $this->factory->post->create(
			array(
				'post_title' => 'post2',
				'post_type'  => $post_type,
			)
		);
		$post3 = $this->factory->post->create(
			array(
				'post_title' => 'post3',
				'post_type'  => $post_type,
			)
		);

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat1, 'category' );
		wp_set_object_terms( $post3, $cat1, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 3, count( $posts_in_cat1 ) );

		// call our method.
		$delete_options = array(
			'post_type' => array( $post_type ),
			'taxonomy'  => 'category',
			'term_opt'  => 'equal_to',
			'term_text' => '3',
		);
		$this->module->delete( $delete_options );

		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );

	}

	/**
	 * Add tests to delete term with not equal state.
	 */
	public function test_that_delete_terms_with_not_equal_state() {

		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );

		// Create two posts and assign them to categories just created.
		$post1 = $this->factory->post->create(
			array(
				'post_title' => 'post1',
				'post_type'  => $post_type,
			)
		);
		$post2 = $this->factory->post->create(
			array(
				'post_title' => 'post2',
				'post_type'  => $post_type,
			)
		);
		$post3 = $this->factory->post->create(
			array(
				'post_title' => 'post3',
				'post_type'  => $post_type,
			)
		);

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat1, 'category' );
		wp_set_object_terms( $post3, $cat1, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 3, count( $posts_in_cat1 ) );

		// call our method.
		$delete_options = array(
			'post_type' => array( $post_type ),
			'taxonomy'  => 'category',
			'term_opt'  => 'not_equal_to',
			'term_text' => '5',
		);
		$this->module->delete( $delete_options );

		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );

	}

	/**
	 * Add tests to delete term with less than state.
	 */
	public function test_that_delete_terms_with_less_than_state() {

		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );

		// Create two posts and assign them to categories just created.
		$post1 = $this->factory->post->create(
			array(
				'post_title' => 'post1',
				'post_type'  => $post_type,
			)
		);
		$post2 = $this->factory->post->create(
			array(
				'post_title' => 'post2',
				'post_type'  => $post_type,
			)
		);
		$post3 = $this->factory->post->create(
			array(
				'post_title' => 'post3',
				'post_type'  => $post_type,
			)
		);

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat1, 'category' );
		wp_set_object_terms( $post3, $cat1, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 3, count( $posts_in_cat1 ) );

		// call our method.
		$delete_options = array(
			'post_type' => array( $post_type ),
			'taxonomy'  => 'category',
			'term_opt'  => 'less_than',
			'term_text' => '5',
		);
		$this->module->delete( $delete_options );

		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );

	}

	/**
	 * Add tests to delete term with greater than state.
	 */
	public function test_that_delete_terms_with_greater_than_state() {

		$post_type = 'movie';

		$this->register_post_type_and_associate_category( $post_type );

		// Create two categories.
		$cat1 = $this->factory->category->create( array( 'name' => 'cat1' ) );

		// Create two posts and assign them to categories just created.
		$post1 = $this->factory->post->create(
			array(
				'post_title' => 'post1',
				'post_type'  => $post_type,
			)
		);
		$post2 = $this->factory->post->create(
			array(
				'post_title' => 'post2',
				'post_type'  => $post_type,
			)
		);
		$post3 = $this->factory->post->create(
			array(
				'post_title' => 'post3',
				'post_type'  => $post_type,
			)
		);

		wp_set_object_terms( $post1, $cat1, 'category' );
		wp_set_object_terms( $post2, $cat1, 'category' );
		wp_set_object_terms( $post3, $cat1, 'category' );

		// Assert that each category has one post.
		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 3, count( $posts_in_cat1 ) );

		// call our method.
		$delete_options = array(
			'post_type' => array( $post_type ),
			'taxonomy'  => 'category',
			'term_opt'  => 'greater_than',
			'term_text' => '2',
		);
		$this->module->delete( $delete_options );

		$posts_in_cat1 = $this->get_posts_by_custom_term( $cat1, 'category', $post_type );

		$this->assertEquals( 0, count( $posts_in_cat1 ) );

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
