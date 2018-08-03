<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Delete Taxonomy terms by post count.
 *
 * Tests \BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByNameModule
 *
 * @since 6.1.0
 */
class DeleteTermsByNameModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByNameModule
	 */
	protected $module;

	/**
	 * Setup the Module.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeleteTermsByNameModule();
	}

	/**
	 * Dataprovider of test case.
	 */
	public function delete_terms_by_name_various_inputs() {
		return array(
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'term_text' => 'Term A',
					'operator'  => 'equal_to',
				),
				1,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'term_text' => 'Term A',
					'operator'  => 'not_equal_to',
				),
				2,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term A',
						'Term B',
						'Another Term C',
					),
				),
				array(
					'term_text' => 'Term',
					'operator'  => 'starts',
				),
				2,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'term_text' => 'Another',
					'operator'  => 'starts',
				),
				0,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'A Term',
						'B Term',
						'Term C',
					),
				),
				array(
					'term_text' => 'Term',
					'operator'  => 'ends',
				),
				2,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'term_text' => 'Term',
					'operator'  => 'ends',
				),
				0,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term sample A',
						'Term sample B',
						'Term C',
					),
				),
				array(
					'term_text' => 'sample',
					'operator'  => 'contains',
				),
				2,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'term_text' => 'sample',
					'operator'  => 'contains',
				),
				0,
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term sample A',
						'Term sample B',
						'Term C',
					),
				),
				array(
					'term_text' => 'sample',
					'operator'  => 'not_contains',
				),
				1, 
			),
			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'post_tag',
					'term_input' => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'term_text' => 'sample',
					'operator'  => 'not_contains',
				),
				3, 
			),

			array(
				array(
					'post_type'  => 'post',
					'taxonomy'   => 'custom_taxonomy',
					'term_input' => array(
						'Term A',
						'Term sample B',
						'Term C',
					),
				),
				array(
					'term_text' => 'sample',
					'operator'  => 'not_contains',
				),
				2,
			),
		);
	}

	/**
	 * Add tests to delete term with various cases.
	 *
	 * @dataProvider delete_terms_by_name_various_inputs
	 *
	 * @param array $inputs Inputs for test cases.
	 * @param array $operation Operation performed after user inputs.
	 * @param int   $expected Expected value.
	 */
	public function test_that_delete_terms_by_name( $inputs, $operation, $expected ) {

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
			wp_insert_term( $term, $taxonomy );
		}

		// call our method.
		$delete_options = array(
			'taxonomy'  => $taxonomy,
			'term_opt'  => $operation['operator'],
			'term_text' => $operation['term_text'],
		);
		$deleted_term   = $this->module->delete( $delete_options );

		$this->assertEquals( $expected, $deleted_term );

	}


}
