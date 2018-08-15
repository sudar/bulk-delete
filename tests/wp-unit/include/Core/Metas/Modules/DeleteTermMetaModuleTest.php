<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Delete Term Meta Module.
 *
 * Tests \BulkWP\BulkDelete\Core\Metas\Modules\DeleteTermMetaModule
 *
 * @since 6.0.0
 */
class DeleteTermMetaModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Metas\Modules\DeleteTermMetaModule
	 */
	protected $module;

	/**
	 * Setup the Module.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeleteTermMetaModule();
	}

	/**
	 * Add to test delete default taxonomy term meta with equal value.
	 */
	public function test_that_term_meta_can_be_deleted_with_default_taxonomy_in_equal_value() {

		$term                   = 'Apple';
		$taxonomy               = 'category';
		$meta_key               = 'grade';
		$matched_meta_value     = 'A1';
		$missmatched_meta_value = 'A2';

		$term_array = wp_insert_term( $term, $taxonomy );

		add_term_meta( $term_array['term_id'], $meta_key, $matched_meta_value );

		add_term_meta( $term_array['term_id'], $meta_key, $missmatched_meta_value );

		// call our method.
		$delete_options = array(
			'term'             => $term_array['term_id'],
			'term_meta'        => $meta_key,
			'term_meta_value'  => $matched_meta_value,
			'term_meta_option' => 'equal',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		// Assert that post meta deleted.
		$this->assertEquals( 1, $meta_deleted );

		$meta_value = get_term_meta( $term_array['term_id'], $meta_key, true );

		// Assert that post meta is not deleted.
		$this->assertEquals( $missmatched_meta_value, $meta_value );

	}

	/**
	 * Add to test delete default taxonomy term meta with not equal value.
	 */
	public function test_that_term_meta_can_be_deleted_with_default_taxonomy_in_not_equal_value() {

		$term                   = 'Apple';
		$taxonomy               = 'category';
		$meta_key               = 'grade';
		$matched_meta_value     = 'A1';
		$missmatched_meta_value = 'A2';

		$term_array = wp_insert_term( $term, $taxonomy );

		add_term_meta( $term_array['term_id'], $meta_key, $matched_meta_value );

		add_term_meta( $term_array['term_id'], $meta_key, $missmatched_meta_value );

		// call our method.
		$delete_options = array(
			'term'             => $term_array['term_id'],
			'term_meta'        => $meta_key,
			'term_meta_value'  => $matched_meta_value,
			'term_meta_option' => 'not_equal',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		// Assert that post meta deleted.
		$this->assertEquals( 1, $meta_deleted );

		$meta_value = get_term_meta( $term_array['term_id'], $meta_key, true );

		// Assert that post meta is not deleted (here matched value should not be deleted).
		$this->assertEquals( $matched_meta_value, $meta_value );

	}

	/**
	 * Add to test delete custom taxonomy term meta with equal value.
	 */
	public function test_that_term_meta_can_be_deleted_with_custom_taxonomy_in_equal_value() {

		$term                   = 'Apple';
		$taxonomy               = 'fruit';
		$meta_key               = 'grade';
		$matched_meta_value     = 'A1';
		$missmatched_meta_value = 'A2';

		$this->register_post_type_and_taxonomy( 'post', $taxonomy );

		$term_array = wp_insert_term( $term, $taxonomy );

		add_term_meta( $term_array['term_id'], $meta_key, $matched_meta_value );

		add_term_meta( $term_array['term_id'], $meta_key, $missmatched_meta_value );

		// call our method.
		$delete_options = array(
			'term'             => $term_array['term_id'],
			'term_meta'        => $meta_key,
			'term_meta_value'  => $matched_meta_value,
			'term_meta_option' => 'equal',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		// Assert that post meta deleted.
		$this->assertEquals( 1, $meta_deleted );

		$meta_value = get_term_meta( $term_array['term_id'], $meta_key, true );

		// Assert that post meta is not deleted.
		$this->assertEquals( $missmatched_meta_value, $meta_value );

	}

	/**
	 * Add to test delete custom taxonomy term meta with not equal value.
	 */
	public function test_that_term_meta_can_be_deleted_with_custom_taxonomy_in_not_equal_value() {

		$term                   = 'Apple';
		$taxonomy               = 'fruit';
		$meta_key               = 'grade';
		$matched_meta_value     = 'A1';
		$missmatched_meta_value = 'A2';

		$this->register_post_type_and_taxonomy( 'post', $taxonomy );

		$term_array = wp_insert_term( $term, $taxonomy );

		add_term_meta( $term_array['term_id'], $meta_key, $matched_meta_value );

		add_term_meta( $term_array['term_id'], $meta_key, $missmatched_meta_value );

		// call our method.
		$delete_options = array(
			'term'             => $term_array['term_id'],
			'term_meta'        => $meta_key,
			'term_meta_value'  => $matched_meta_value,
			'term_meta_option' => 'not_equal',
		);

		$meta_deleted = $this->module->delete( $delete_options );

		// Assert that post meta deleted.
		$this->assertEquals( 1, $meta_deleted );

		$meta_value = get_term_meta( $term_array['term_id'], $meta_key, true );

		// Assert that post meta is not deleted.
		$this->assertEquals( $matched_meta_value, $meta_value );
	}


}
