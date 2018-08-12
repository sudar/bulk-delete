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

	/**
	 * Setup the DeletePostsByTaxonomyModule Module.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByTaxonomyModule();
	}

	/**
	 * Data provider for test_test_post_by_taxonomy
	 */
	public function provide_data_test_test_post_by_taxonomy() {
		return array(
			// Deleting posts from a single taxonomy term default post type and default taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),

			// Deleting posts from a multiple taxonomy term default post type and default taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
						'another-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 15,
					'trashed'       => 15,
					'published'     => 3,
				),
			),

			// Deleting posts from a single taxonomy term custom post type and custom taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),

			// Deleting posts from a multiple taxonomy term custom post type and custom taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
						'another-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 15,
					'trashed'       => 15,
					'published'     => 3,
				),
			),

			// Deleting posts from a single taxonomy term default post type and custom taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),

			// Deleting posts from a multiple taxonomy term default post type and custom taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
						'another-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 15,
					'trashed'       => 15,
					'published'     => 3,
				),
			),
			// Deleting posts from a single taxonomy term custom post type and default taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),

			// Deleting posts from a multiple taxonomy term custom post type and default taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
						'another-term',
					),
					'filters'    => array(),
				),
				array(
					'posts_deleted' => 15,
					'trashed'       => 15,
					'published'     => 3,
				),
			),

			// Deleting posts that are older than x days.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(
						'force_delete' => false,
						'limit_to'     => 0,
						'restrict'     => false,
						'force_delete' => false,
						'date_op'      => 'before',
						'days'         => '3',
					),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),

			// Deleting posts that are posted within the last x days.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_array'      => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(
						'force_delete' => false,
						'limit_to'     => 0,
						'restrict'     => false,
						'force_delete' => false,
						'date_op'      => 'after',
						'days'         => '5',
					),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),

			// Deleting more posts delete them in batches.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 100,
							'post_array'      => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 50,
							'post_array'      => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(
						'limit_to' => 50,
					),
				),
				array(
					'posts_deleted' => 50,
					'trashed'       => 50,
					'published'     => 100,
				),
			),
		);
	}

	/**
	 * Test various test cases for delete posts by taxonomy module.
	 *
	 * @dataProvider provide_data_test_test_post_by_taxonomy
	 *
	 * @param array $creater Create posts and taxonomies arguments.
	 * @param array $inputs User inputs.
	 * @param array $expected Expected output for respective inputs.
	 */
	public function test_test_post_by_taxonomy( $creater, $inputs, $expected ) {

		$post_type = $creater['post_type'];
		$taxonomy  = $creater['taxonomy'];
		$terms     = $creater['terms'];

		$this->register_post_type_and_taxonomy( $post_type, $taxonomy );

		foreach ( $terms as $term ) {
			$matched_term_array = wp_insert_term( $term['term'], $taxonomy );
			for ( $i = 0; $i < $term['number_of_posts']; $i++ ) {
				$post_args = array(
					'post_type' => $post_type,
				);
				$post_args = array_merge( $post_args, $term['post_array'] );
				$post      = $this->factory->post->create( $post_args );
				wp_set_object_terms( $post, $matched_term_array, $taxonomy );
			}
		}

		$delete_options = array(
			'post_type'          => $inputs['post_type'],
			'selected_taxs'      => $inputs['taxonomy'],
			'selected_tax_terms' => $inputs['term_slugs'],
		);

		$delete_options = array_merge( $delete_options, $inputs['filters'] );

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['posts_deleted'], $posts_deleted );

		$posts_in_trash = $this->get_posts_by_status( 'trash', $post_type );
		$this->assertEquals( $expected['trashed'], count( $posts_in_trash ) );

		$posts_in_published = $this->get_posts_by_status( 'publish', $post_type );
		$this->assertEquals( $expected['published'], count( $posts_in_published ) );
	}
}
