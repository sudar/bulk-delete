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
	 * Dataprovider of test case.
	 */
	public function delete_terms_by_post_count_various_inputs() {
		return array(
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'equal_to',
				),
				1,
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'equal_to',
				),
				2,
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 5,
						),
					),
				),
				array(
					'post_count' => 2,
					'operator'   => 'equal_to',
				),
				0,
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 3,
					'operator'   => 'not_equal_to',
				),
				2, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 3,
					'operator'   => 'not_equal_to',
				),
				1, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 3,
					'operator'   => 'not_equal_to',
				),
				0, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 0,
					'operator'   => 'not_equal_to',
				),
				3, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 4,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 4,
					'operator'   => 'less_than',
				),
				1, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 2,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 4,
					'operator'   => 'less_than',
				),
				3, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 2,
					'operator'   => 'less_than',
				),
				0, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 4,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 4,
					'operator'   => 'greater_than',
				),
				1,
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 6,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 4,
					'operator'   => 'greater_than',
				),
				2,
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 2,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 3,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 3,
						),
					),
				),
				array(
					'post_count' => 4,
					'operator'   => 'greater_than',
				),
				0,
			),

			array(
				array(
					'post_type'  => 'custom_post_type',
					'taxonomy'   => 'custom_taxonomy',
					'term_input' => array(
						array(
							'term'            => 'Term A',
							'number_0f_posts' => 5,
						),
						array(
							'term'            => 'Term B',
							'number_0f_posts' => 2,
						),
						array(
							'term'            => 'Term C',
							'number_0f_posts' => 0,
						),
					),
				),
				array(
					'post_count' => 5,
					'operator'   => 'equal_to',
				),
				1,
			),
		);
	}

	/**
	 * Add tests to delete term with various cases.
	 *
	 * @dataProvider delete_terms_by_post_count_various_inputs
	 *
	 * @param array $inputs Inputs for test cases.
	 * @param array $operation Operation performed after user inputs.
	 * @param int   $expected Expected value.
	 */
	public function test_that_delete_terms_by_post_count( $inputs, $operation, $expected ) {

		$post_type  = $inputs['post_type'];
		$taxonomy   = $inputs['taxonomy'];
		$term_input = $inputs['term_input'];

		$default_taxonomies = array( 'category', 'post_tag', 'link_category', 'post_format' );

		register_post_type( $post_type );
		if ( ! in_array( $taxonomy, $default_taxonomies, true ) ) {
			register_taxonomy( $taxonomy, $post_type );
		}
		register_taxonomy_for_object_type( $taxonomy, $post_type );

		foreach ( $term_input as $term ) {
			$matched_term_array = wp_insert_term( $term['term'], $taxonomy );
			for ( $i = 0; $i < $term['number_0f_posts']; $i++ ) {

				$post = $this->factory->post->create(
					array(
						'post_type' => $post_type,
					)
				);

				wp_set_object_terms( $post, $matched_term_array, $taxonomy );
			}
		}

		// call our method.
		$delete_options = array(
			'post_type' => array( $post_type ),
			'taxonomy'  => $taxonomy,
			'term_opt'  => $operation['operator'],
			'term_text' => $operation['post_count'],
		);
		$deleted_term   = $this->module->delete( $delete_options );

		$this->assertEquals( $expected, $deleted_term );

	}


}
