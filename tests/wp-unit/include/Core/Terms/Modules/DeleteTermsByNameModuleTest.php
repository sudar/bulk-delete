<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Delete Taxonomy terms by name.
 *
 * Tests \BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByNameModule
 *
 * @since 6.0.0
 */
class DeleteTermsByNameModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Terms\Modules\DeleteTermsByNameModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeleteTermsByNameModule();
	}

	/**
	 * Provide data to test deletion of terms by name.
	 */
	public function provide_data_to_test_that_terms_can_be_deleted_by_name() {
		return array(
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'search_term' => 'Term A',
					'operator'    => 'equal_to',
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
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'search_term' => 'Term A',
					'operator'    => 'not_equal_to',
				),
				2,
				array(
					'Term A',
				),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						'Term A',
						'Term sample B',
						'Term Sample C',
					),
				),
				array(
					'search_term' => 'Term sample C',
					'operator'    => 'not_equal_to',
				),
				3,
				array(),
			),

			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term A',
						'Term B',
						'Another Term C',
					),
				),
				array(
					'search_term' => 'Term',
					'operator'    => 'starts_with',
				),
				2,
				array(
					'Another Term C',
				),
			),
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'search_term' => 'Another',
					'operator'    => 'starts_with',
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
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'A Term',
						'B Term',
						'Term C',
					),
				),
				array(
					'search_term' => 'Term',
					'operator'    => 'ends_with',
				),
				2,
				array(
					'Term C',
				),
			),
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'search_term' => 'Term',
					'operator'    => 'ends_with',
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
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term sample A',
						'Term sample B',
						'Term C',
					),
				),
				array(
					'search_term' => 'sample',
					'operator'    => 'contains',
				),
				2,
				array(
					'Term C',
				),
			),
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'search_term' => 'sample',
					'operator'    => 'contains',
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
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term sample A',
						'Term sample B',
						'Term C',
					),
				),
				array(
					'search_term' => 'sample',
					'operator'    => 'not_contains',
				),
				1,
				array(
					'Term sample A',
					'Term sample B',
				),
			),
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'post_tag',
					'terms'     => array(
						'Term A',
						'Term B',
						'Term C',
					),
				),
				array(
					'search_term' => 'sample',
					'operator'    => 'not_contains',
				),
				3,
				array(),
			),
			array(
				array(
					'post_type' => 'post',
					'taxonomy'  => 'custom_taxonomy',
					'terms'     => array(
						'Term A',
						'Term sample B',
						'Term C',
					),
				),
				array(
					'search_term' => 'sample',
					'operator'    => 'not_contains',
				),
				2,
				array(
					'term_not_deleted' => 'Term sample B',
				),
			),
		);
	}

	/**
	 * Test deletion of terms by name.
	 *
	 * @dataProvider provide_data_to_test_that_terms_can_be_deleted_by_name
	 *
	 * @param array $inputs                           Inputs for test cases.
	 * @param array $user_input                       Options selected by user.
	 * @param int   $no_of_terms_to_be_deleted        Number of terms to be deleted.
	 * @param array $terms_that_should_not_be_deleted Terms that should not be deleted.
	 */
	public function test_that_terms_can_be_deleted_by_name_using_various_filters( $inputs, $user_input, $no_of_terms_to_be_deleted, $terms_that_should_not_be_deleted ) {
		$post_type = $inputs['post_type'];
		$taxonomy  = $inputs['taxonomy'];
		$terms     = $inputs['terms'];

		$this->register_post_type_and_taxonomy( $post_type, $taxonomy );

		$post_ids = array();
		foreach ( $terms as $term ) {
			wp_insert_term( $term, $taxonomy );

			$post_id = $this->factory->post->create( array( 'post_type' => $post_type ) );
			wp_set_post_terms( $post_id, $term, $taxonomy );
			$post_ids[] = $post_id;
		}

		$delete_options = array(
			'taxonomy' => $taxonomy,
			'operator' => $user_input['operator'],
			'value'    => $user_input['search_term'],
		);
		$deleted_terms  = $this->module->delete( $delete_options );

		$this->assertEquals( $no_of_terms_to_be_deleted, $deleted_terms );

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
