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
	 * Data provider for test_deletion_of_posts_by_taxonomy and test_move_posts_to_trash_by_taxonomy
	 */
	public function provide_data_to_test_variations_by_built_in_taxonomy() {
		return array(
			// (+ve Case) Deleting posts from a single taxonomy term built-in post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
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
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),
			// (-ve) Case: Deleting posts from a single taxonomy term built-in post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
					),
				),
				array(
					'post_type'  => 'page',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 15,
				),
			),
			// (+ve) Case: Deleting posts from a multiple taxonomy terms, built-in post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(),
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
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 15,
					'trashed'       => 15,
					'published'     => 3,
				),
			),
			// (-ve) Case: Deleting posts from a multiple taxonomy terms, built-in post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_slugs' => array(
						'test-term',
						'another-term',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 18,
				),
			),
			// (+ve) Case: Deleting posts from a single taxonomy term, custom post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),
			// (-ve) Case: Deleting posts from a single taxonomy term, custom post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 15,
				),
			),
			// (+ve) Case: Deleting posts from a multiple taxonomy terms, custom post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(),
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
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 15,
					'trashed'       => 15,
					'published'     => 3,
				),
			),
			// (-ve) Case: Deleting posts from a multiple taxonomy terms, custom post type and built-in taxonomy.
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term-1',
						'2-another-term',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => false,
						'date_op'  => '',
						'days'     => '',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 18,
				),
			),
		);
	}

	/**
	 * Data provider for test_deletion_of_posts_by_taxonomy
	 */
	public function provide_data_to_test_deletion_of_posts_by_taxonomy() {
		return array(
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
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(
						'force_delete' => false,
						'limit_to'     => 0,
						'restrict'     => false,
						'date_op'      => '',
						'days'         => '',
					),
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
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(),
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
					'filters'    => array(
						'force_delete' => false,
						'limit_to'     => 0,
						'restrict'     => false,
						'date_op'      => '',
						'days'         => '',
					),
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
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
					),
					'filters'    => array(
						'force_delete' => false,
						'limit_to'     => 0,
						'restrict'     => false,
						'date_op'      => '',
						'days'         => '',
					),
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
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(),
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
					'filters'    => array(
						'force_delete' => false,
						'limit_to'     => 0,
						'restrict'     => false,
						'date_op'      => '',
						'days'         => '',
					),
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
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
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
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
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
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 50,
							'post_args'       => array(),
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
						'limit_to'     => 50,
						'restrict'     => false,
						'date_op'      => '',
						'days'         => '',
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
	 * Test various test cases for deleting posts by taxonomy.
	 *
	 * @param array $setup         Create posts and taxonomies arguments.
	 * @param array $operations    User operations.
	 * @param array $expected      Expected output for respective operations.
	 * @param array $force_delete  Flag for delete/trash.
	 * @return void
	 */
	private function delete_or_trash( $setup, $operations, $expected, $force_delete ) {
		$post_type = $setup['post_type'];
		$taxonomy  = $setup['taxonomy'];
		$terms     = $setup['terms'];

		$this->register_post_type_and_taxonomy( $post_type, $taxonomy );

		foreach ( $terms as $term ) {
			$matched_term_array = wp_insert_term( $term['term'], $taxonomy );

			for ( $i = 0; $i < $term['number_of_posts']; $i ++ ) {
				$post_args = array(
					'post_type' => $post_type,
				);
				$post_args = array_merge( $post_args, $term['post_args'] );
				$post      = $this->factory->post->create( $post_args );
				wp_set_object_terms( $post, $matched_term_array, $taxonomy );
			}
		}

		$delete_options = array(
			'post_type'          => $operations['post_type'],
			'selected_taxs'      => $operations['taxonomy'],
			'selected_tax_terms' => $operations['term_slugs'],
			'force_delete'       => $force_delete,
		);

		$delete_options = array_merge( $delete_options, $operations['filters'] );

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['posts_deleted'], $posts_deleted );

	}

	/**
	 * Test various test cases for deleting posts by taxonomy.
	 *
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy
	 *
	 * @param array $setup      Create posts and taxonomies arguments.
	 * @param array $operations User operations.
	 * @param array $expected   Expected output for respective operations.
	 */
	public function test_deletion_of_posts_by_taxonomy( $setup, $operations, $expected ) {
		$this->delete_or_trash( $setup, $operations, $expected, true );

		$posts_in_published = $this->get_posts_by_status( 'publish', $setup['post_type'] );
		$this->assertEquals( $expected['published'], count( $posts_in_published ) );
	}

	/**
	 * Test various test cases for moving posts to trash by taxonomy.
	 *
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy
	 *
	 * @param array $setup      Create posts and taxonomies arguments.
	 * @param array $operations User operations.
	 * @param array $expected   Expected output for respective operations.
	 */
	public function test_move_posts_to_trash_by_taxonomy( $setup, $operations, $expected ) {
		$this->delete_or_trash( $setup, $operations, $expected, false );

		$posts_in_trash = $this->get_posts_by_status( 'trash', $setup['post_type'] );
		$this->assertEquals( $expected['trashed'], count( $posts_in_trash ) );

	}
}
