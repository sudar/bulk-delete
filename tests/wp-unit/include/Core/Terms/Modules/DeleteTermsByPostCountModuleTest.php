<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test deletion of Taxonomy terms based on post count.
 *
 * Tests \BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByPostCountModule
 *
 * @since 6.0.0
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
	 * Data provider to test `test_that_terms_can_be_deleted_by_post_count_using_various_filters` method.
	 *
	 * @see DeleteTermsByPostCountModule::test_that_terms_can_be_deleted_by_post_count_using_various_filters() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_terms_deletion_by_post_count_with_equals_operator() {
		return array(
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'equal_to',
				),
				1,
				array(
					'Term B',
					'Term C',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 1,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'equal_to',
				),
				0,
				array(
					'Term A',
					'Term B',
					'Term C',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 2,
						),
					),
				),
				array(
					'post_count' => 2,
					'operator'   => 'equal_to',
				),
				3,
				array(),
			),
		);
	}

	/**
	 * Data provider to test `test_that_terms_can_be_deleted_by_post_count_using_various_filters` method.
	 *
	 * @see DeleteTermsByPostCountModule::test_that_terms_can_be_deleted_by_post_count_using_various_filters() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_terms_deletion_by_post_count_with_not_equals_operator() {
		return array(
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'not_equal_to',
				),
				2,
				array(
					'Term A',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 1,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'not_equal_to',
				),
				3,
				array(),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 2,
						),
					),
				),
				array(
					'post_count' => 2,
					'operator'   => 'not_equal_to',
				),
				0,
				array(
					'Term A',
					'Term B',
					'Term C',
				),
			),
		);
	}

	/**
	 * Data provider to test `test_that_terms_can_be_deleted_by_post_count_using_various_filters` method.
	 *
	 * @see DeleteTermsByPostCountModule::test_that_terms_can_be_deleted_by_post_count_using_various_filters() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_terms_deletion_by_post_count_with_less_than_operator() {
		return array(
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'less_than',
				),
				2,
				array(
					'Term A',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 3,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 5,
						),
					),
				),
				array(
					'post_count' => 1,
					'operator'   => 'less_than',
				),
				0,
				array(
					'Term A',
					'Term B',
					'Term C',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 3,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 4,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'less_than',
				),
				3,
				array(),
			),
		);
	}

	/**
	 * Data provider to test `test_that_terms_can_be_deleted_by_post_count_using_various_filters` method.
	 *
	 * @see DeleteTermsByPostCountModule::test_that_terms_can_be_deleted_by_post_count_using_various_filters() To see how the data is used.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_terms_deletion_by_post_count_with_greater_than_operator() {
		return array(
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 1,
					'operator'   => 'greater_than',
				),
				2,
				array(
					'Term C',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 2,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 3,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 5,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'greater_than',
				),
				0,
				array(
					'Term A',
					'Term B',
					'Term C',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						array(
							'term'            => 'Term A',
							'number_of_posts' => 3,
						),
						array(
							'term'            => 'Term B',
							'number_of_posts' => 5,
						),
						array(
							'term'            => 'Term C',
							'number_of_posts' => 2,
						),
					),
				),
				array(
					'post_count' => 2,
					'operator'   => 'greater_than',
				),
				2,
				array(
					'Term C',
				),
			),
		);
	}

	/**
	 * Add tests to delete term based on post count using various filters.
	 *
	 * @dataProvider provide_data_to_test_terms_deletion_by_post_count_with_equals_operator
	 * @dataProvider provide_data_to_test_terms_deletion_by_post_count_with_not_equals_operator
	 * @dataProvider provide_data_to_test_terms_deletion_by_post_count_with_less_than_operator
	 * @dataProvider provide_data_to_test_terms_deletion_by_post_count_with_greater_than_operator
	 *
	 * @param array $inputs                           Inputs for test cases.
	 * @param array $user_input                       Options selected by user.
	 * @param int   $no_of_terms_to_be_deleted        Number of terms to be deleted.
	 * @param array $terms_that_should_not_be_deleted Terms that should not be deleted.
	 */
	public function test_that_terms_can_be_deleted_by_post_count_using_various_filters( $inputs, $user_input, $no_of_terms_to_be_deleted, $terms_that_should_not_be_deleted ) {
		$post_type = $inputs['post_type'];
		$taxonomy  = $inputs['taxonomy'];
		$terms     = $inputs['terms'];

		$this->register_post_type_and_taxonomy( $post_type, $taxonomy );

		$post_ids = array();
		foreach ( $terms as $term ) {
			wp_insert_term( $term['term'], $taxonomy );

			for ( $i = 0; $i < $term['number_of_posts']; $i ++ ) {
				$post_id = $this->factory->post->create(
					array(
						'post_type' => $post_type,
					)
				);

				wp_set_object_terms( $post_id, $term['term'], $taxonomy );

				$post_ids[] = $post_id;
			}
		}

		$delete_options = array(
			'taxonomy'   => $taxonomy,
			'operator'   => $user_input['operator'],
			'post_count' => $user_input['post_count'],
		);

		$terms_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $no_of_terms_to_be_deleted, $terms_deleted );

		foreach ( $terms_that_should_not_be_deleted as $term ) {
			$does_term_exists = term_exists( $term, $taxonomy );

			$this->assertNotNull( $does_term_exists );
			$this->assertNotEquals( 0, $does_term_exists );
			$this->assertArrayHasKey( 'term_taxonomy_id', $does_term_exists );
		}

		// Assert that the posts to which the terms were associated were not deleted.
		foreach ( $post_ids as $post_id ) {
			$this->assertEquals( 'publish', get_post_status( $post_id ) );
		}
	}
}
