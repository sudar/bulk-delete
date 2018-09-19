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
	 * Data provider to test posts by built-in taxonomy without filter can be deleted/trashed.
	 */
	public function provide_data_to_test_variations_by_built_in_taxonomy_without_filters() {
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
			// (+ve) Case: Deleting posts from multiple taxonomy terms, built-in post type and built-in taxonomy.
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
			// (-ve) Case: Deleting posts from multiple taxonomy terms, built-in post type and built-in taxonomy.
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
			// (+ve) Case: Deleting posts from multiple taxonomy terms, custom post type and built-in taxonomy.
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
			// (-ve) Case: Deleting posts from multiple taxonomy terms, custom post type and built-in taxonomy.
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
	 * Data provider to test posts by built-in taxonomy with date filter can be trashed/deleted.
	 */
	public function provide_data_to_test_variations_by_built_in_taxonomy_with_date_filter() {
		return array(
			/**
			 * (+ve Case) Deleting posts that are older than x days from a single taxonomy term,
			 * with custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
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
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),
			/**
			 * (-ve Case) Deleting posts that are older than x days from a single taxonomy term,
			 * with custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
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
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '6',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 15,
				),
			),
			/**
			 * (+ve Case) Deleting posts that are posted within last x days from a single taxonomy term,
			 * with custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
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
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 5,
					'trashed'       => 5,
					'published'     => 10,
				),
			),
			/**
			 * (-ve Case) Deleting posts that are posted within last x days from a single taxonomy term,
			 * with custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
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
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 18,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are older than x days from multiple taxonomy
			 * terms, custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-4 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 13,
					'trashed'       => 13,
					'published'     => 5,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are older than x days from multiple taxonomy terms,
			 * custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-4 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '6',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 18,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are posted within last x days from multiple taxonomy terms,
			 * custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-11 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s' ),
							),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '10',
					),
				),
				array(
					'posts_deleted' => 8,
					'trashed'       => 8,
					'published'     => 10,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are posted within last x days from multiple taxonomy terms,
			 * custom post type and built-in taxonomy.
			 */
			array(
				array(
					'post_type' => 'custom_post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-11 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
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
	 * Data provider to test posts by built-in taxonomy with date and batch filter
	 * can be deleted/trashed.
	 */
	public function provide_data_to_test_variations_by_built_in_taxonomy_with_date_filter_and_in_batches() {
		return array(
			/**
			 * (+ve) Case: Deleting posts that are older than x days from a single taxonomy term,
			 * built-in post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'batch_size' => 3,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 75,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 25,
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
						'limit_to' => 30,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 75,
					'trashed'       => 75,
					'published'     => 75,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are older than x days from a single taxonomy term,
			 * built-in post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 75,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 25,
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '6',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 150,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are posted within x days from multiple taxonomy terms,
			 * built-in post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'batch_size' => 3,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 10,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '6',
					),
				),
				array(
					'posts_deleted' => 30,
					'trashed'       => 30,
					'published'     => 40,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are posted within x days from multiple taxonomy terms,
			 * built-in post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'category',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 70,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are posted within x days from a single taxonomy term,
			 * custom post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'batch_size' => 3,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s' ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 20,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '6',
					),
				),
				array(
					'posts_deleted' => 60,
					'trashed'       => 60,
					'published'     => 10,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are posted within x days from a single taxonomy term,
			 * custom post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 70,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are older than x days from multiple taxonomy terms,
			 * custom post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'batch_size' => 3,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 20,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 60,
					'trashed'       => 60,
					'published'     => 10,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are older than x days from multiple taxonomy terms,
			 * custom post type and built-in taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'category',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '11',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 70,
				),
			),
		);
	}

	/**
	 * Data provider to test posts by custom taxonomy without filters can be deleted/trashed.
	 */
	public function provide_data_to_test_variations_by_custom_taxonomy_without_filters() {
		return array(
			// (+ve Case) Deleting posts from a single taxonomy term built-in post type and custom taxonomy.
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
			// (-ve) Case: Deleting posts from a single taxonomy term built-in post type and custom taxonomy.
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
					'post_type'  => 'page',
					'taxonomy'   => 'custom_taxonomy',
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
			// (+ve) Case: Deleting posts from multiple taxonomy terms, built-in post type and custom taxonomy.
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
			// (-ve) Case: Deleting posts from multiple taxonomy terms, built-in post type and custom taxonomy.
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
					'taxonomy'   => 'custom',
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
			// (+ve) Case: Deleting posts from a single taxonomy term, custom post type and custom taxonomy.
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
			// (-ve) Case: Deleting posts from a single taxonomy term, custom post type and custom taxonomy.
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
			// (+ve) Case: Deleting posts from multiple taxonomy terms, custom post type and custom taxonomy.
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
			// (-ve) Case: Deleting posts from multiple taxonomy terms, custom post type and custom taxonomy.
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
	 * Data provider to test posts by custom taxonomy with date filter can be deleted or trashed.
	 */
	public function provide_data_to_test_variations_by_custom_taxonomy_with_date_filter() {
		return array(
			/**
			 * (+ve Case) Deleting posts that are older than x days from a single taxonomy term,
			 * with default post type and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
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
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 10,
					'trashed'       => 10,
					'published'     => 5,
				),
			),
			/**
			 * (-ve Case) Deleting posts that are older than x days from a single taxonomy term,
			 * with default post type and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
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
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '6',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 15,
				),
			),
			/**
			 * (+ve Case) Deleting posts that are posted within last x days from a single taxonomy term,
			 * with default post type and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s' ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
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
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 5,
					'trashed'       => 5,
					'published'     => 20,
				),
			),
			/**
			 * (-ve Case) Deleting posts that are posted within last x days from a single taxonomy term,
			 * with default post type and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
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
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 15,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are older than x days from multiple taxonomy terms, default post type
			 * and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-4 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 13,
					'trashed'       => 13,
					'published'     => 5,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are older than x days from multiple taxonomy terms,
			 * default post type and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
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
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-4 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '6',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 18,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are posted within last x days from multiple taxonomy terms,
			 * default post type and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-11 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s' ),
							),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '10',
					),
				),
				array(
					'posts_deleted' => 8,
					'trashed'       => 8,
					'published'     => 10,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are posted within last x days from multiple taxonomy terms,
			 * default post type and custom taxonomy.
			 */
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-11 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 5,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term 2',
							'term_slug'       => 'another-term-2',
							'number_of_posts' => 3,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_slugs' => array(
						'test-term',
						'another-term-2',
					),
					'filters'    => array(
						'limit_to' => 0,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
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
	 * Data provider to test posts by custom taxonomy with date and batch filter
	 * can be deleted/trashed.
	 */
	public function provide_data_to_test_variations_by_custom_taxonomy_with_date_filter_and_in_batches() {
		return array(
			/**
			 * (+ve) Case: Deleting posts that are posted within last x days from a single taxonomy term,
			 * built-in post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 3,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 25,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 75,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 20,
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
						'limit_to' => 25,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '3',
					),
				),
				array(
					'posts_deleted' => 75,
					'trashed'       => 75,
					'published'     => 45,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are posted within x days from a single taxonomy term,
			 * built-in post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 75,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 25,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 120,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are older than x days from multiple taxonomy terms,
			 * built-in post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 2,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 30,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 20,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 40,
					'trashed'       => 40,
					'published'     => 20,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are older than x days from multiple taxonomy terms,
			 * built-in post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-8 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '10',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 70,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are older than x days from a single taxonomy term,
			 * custom post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 2,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s' ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 20,
						'restrict' => true,
						'date_op'  => 'before',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 40,
					'trashed'       => 40,
					'published'     => 30,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are older than x days from a single taxonomy term,
			 * custom post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '20',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 70,
				),
			),
			/**
			 * (+ve) Case: Deleting posts that are posted within last x days from multiple taxonomy terms,
			 * custom post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 2,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 20,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '4',
					),
				),
				array(
					'posts_deleted' => 30,
					'trashed'       => 30,
					'published'     => 40,
				),
			),
			/**
			 * (-ve) Case: Deleting posts that are posted within last x days from multiple taxonomy terms,
			 * custom post type and custom taxonomy in batches.
			 */
			array(
				array(
					'post_type'  => 'custom_post',
					'taxonomy'   => 'custom_taxonomy',
					'batch_size' => 1,
					'terms'      => array(
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 40,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-5 day' ) ),
							),
						),
						array(
							'term'            => 'Test Term',
							'term_slug'       => 'test-term',
							'number_of_posts' => 20,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-10 day' ) ),
							),
						),
						array(
							'term'            => 'Another Term',
							'term_slug'       => 'another-term',
							'number_of_posts' => 10,
							'post_args'       => array(
								'post_date' => date( 'Y-m-d H:i:s', strtotime( '-3 day' ) ),
							),
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
						'limit_to' => 50,
						'restrict' => true,
						'date_op'  => 'after',
						'days'     => '2',
					),
				),
				array(
					'posts_deleted' => 0,
					'trashed'       => 0,
					'published'     => 70,
				),
			),
		);
	}

	/**
	 * Test various test cases for deleting/moving posts to trash by taxonomy.
	 *
	 * @param array $setup        Create posts and taxonomies arguments.
	 * @param array $operations   User operations.
	 * @param array $expected     Expected output for respective operations.
	 * @param bool  $force_delete Flag for delete/trash.
	 *
	 * @return void
	 */
	protected function assert_post_deletion( $setup, $operations, $expected, $force_delete ) {
		$post_type     = $setup['post_type'];
		$taxonomy      = $setup['taxonomy'];
		$terms         = $setup['terms'];
		$posts_deleted = 0;
		$batch_run     = array_key_exists( 'batch_size', $setup ) ? $setup['batch_size'] : 1;

		$this->register_post_type_and_taxonomy( $post_type, $taxonomy );

		foreach ( $terms as $term ) {
			$matched_term_array = term_exists( $term['term'], $taxonomy );
			if ( ! is_array( $matched_term_array ) ) {
				wp_insert_term( $term['term'], $taxonomy );
			}

			for ( $i = 0; $i < $term['number_of_posts']; $i ++ ) {
				$post_type_args = array(
					'post_type' => $post_type,
				);

				$post_args = array_merge( $post_type_args, $term['post_args'] );
				$post_id   = $this->factory->post->create( $post_args );

				wp_set_object_terms( $post_id, $term['term'], $taxonomy );
			}
		}

		$delete_options = array(
			'post_type'          => $operations['post_type'],
			'selected_taxs'      => $operations['taxonomy'],
			'selected_tax_terms' => $operations['term_slugs'],
			'force_delete'       => $force_delete,
		);

		$delete_options = array_merge( $delete_options, $operations['filters'] );

		for ( $i = 0; $i < $batch_run; $i ++ ) {
			$posts_deleted += $this->module->delete( $delete_options );
		}
		$this->assertEquals( $expected['posts_deleted'], $posts_deleted );
	}

	/**
	 * Test various test cases for deleting posts by taxonomy.
	 *
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy_without_filters
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy_with_date_filter
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy_with_date_filter_and_in_batches
	 * @dataProvider provide_data_to_test_variations_by_custom_taxonomy_without_filters
	 * @dataProvider provide_data_to_test_variations_by_custom_taxonomy_with_date_filter
	 * @dataProvider provide_data_to_test_variations_by_custom_taxonomy_with_date_filter_and_in_batches
	 *
	 * @param array $setup      Create posts and taxonomies arguments.
	 * @param array $operations User operations.
	 * @param array $expected   Expected output for respective operations.
	 */
	public function test_deletion_of_posts_by_taxonomy( $setup, $operations, $expected ) {
		$this->assert_post_deletion( $setup, $operations, $expected, true );

		$published_posts = $this->get_posts_by_status( 'publish', $setup['post_type'] );
		$this->assertEquals( $expected['published'], count( $published_posts ) );
	}

	/**
	 * Test various test cases for moving posts to trash by taxonomy.
	 *
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy_without_filters
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy_with_date_filter
	 * @dataProvider provide_data_to_test_variations_by_built_in_taxonomy_with_date_filter_and_in_batches
	 * @dataProvider provide_data_to_test_variations_by_custom_taxonomy_without_filters
	 * @dataProvider provide_data_to_test_variations_by_custom_taxonomy_with_date_filter
	 * @dataProvider provide_data_to_test_variations_by_custom_taxonomy_with_date_filter_and_in_batches
	 *
	 * @param array $setup      Create posts and taxonomies arguments.
	 * @param array $operations User operations.
	 * @param array $expected   Expected output for respective operations.
	 */
	public function test_move_posts_to_trash_by_taxonomy( $setup, $operations, $expected ) {
		$this->assert_post_deletion( $setup, $operations, $expected, false );

		$posts_in_trash = $this->get_posts_by_status( 'trash', $setup['post_type'] );
		$this->assertEquals( $expected['trashed'], count( $posts_in_trash ) );
	}
}
