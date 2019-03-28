<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Post Meta.
 *
 * Tests \BulkWP\BulkDelete\Core\Metas\Modules\DeletePostMetaModule
 *
 * @since 6.0.1
 */
class DeletePostMetaModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Metas\Modules\DeletePostMetaModule
	 */
	protected $module;

	/**
	 * Setup the test case.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostMetaModule();
	}

	/**
	 * Provide data to test deletion of post meta with custom post status.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_post_meta_deletion_with_custom_post_status() {
		return array(
			// (+ve) case: Built-in post type and custom post status.
			array(
				array(
					'post_type'   => 'post',
					'post_status' => 'wc-completed',
					'no_of_posts' => 10,
					'metas'       => array(
						array(
							'key'   => 'test_key',
							'value' => 'Matched Value',
						),
						array(
							'key'   => 'another_key',
							'value' => 'Another Value',
						),
					),
				),
				array(
					'meta_key'  => 'test_key',
					'post_type' => 'post|wc-completed',
					'use_value' => 'use_key',
					'limit_to'  => 0,
					'restrict'  => false,
					'date_op'   => '',
					'days'      => '',
				),
				array(
					'number_of_post_metas_deleted' => 10,
					'left_over_meta_data'          => 'another_key',
				),
			),
			// (+ve) case: Custom post type and custom post status.
			array(
				array(
					'post_type'   => 'product',
					'post_status' => 'wc-completed',
					'no_of_posts' => 5,
					'metas'       => array(
						array(
							'key'   => 'test_key',
							'value' => 'Matched Value',
						),
						array(
							'key'   => 'another_key',
							'value' => 'Another Value',
						),
					),
				),
				array(
					'meta_key'  => 'test_key',
					'post_type' => 'product|wc-completed',
					'use_value' => 'use_key',
					'limit_to'  => 0,
					'restrict'  => false,
				),
				array(
					'number_of_post_metas_deleted' => 5,
					'left_over_meta_data'          => 'another_key',
				),
			),
		);
	}

	/**
	 * Test deletion of post meta with custom post status.
	 *
	 * TODO: This test is currently skipped because duplicate meta keys is not fully supported yet.
	 *
	 * @see https://github.com/sudar/bulk-delete/issues/515 for details.
	 *
	 * @param array $setup     create posts and meta params.
	 * @param array $operation Possible operations.
	 * @param array $expected  expected output.
	 *
	 * @dataProvider provide_data_to_test_post_meta_deletion_with_custom_post_status
	 */
	public function test_post_meta_deletion_with_custom_post_status( $setup, $operation, $expected ) {
		$this->register_post_type( $setup['post_type'] );
		register_post_status( $setup['post_status'] );
		$metas = $setup['metas'];

		// Create posts.
		$post_ids = $this->factory->post->create_many(
			$setup['no_of_posts'],
			array(
				'post_type'   => $setup['post_type'],
				'post_status' => $setup['post_status'],
			)
		);

		foreach ( $post_ids as $post_id ) {
			foreach ( $metas as $meta ) {
				add_post_meta( $post_id, $meta['key'], $meta['value'] );
			}
		}

		$delete_options = $operation;

		$post_metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['number_of_post_metas_deleted'], $post_metas_deleted );

		foreach ( $post_ids as $post_id ) {
			$this->assertTrue( metadata_exists( 'post', $post_id, $expected['left_over_meta_data'] ) );
			$this->assertFalse( metadata_exists( 'post', $post_id, $operation['meta_key'] ) );
		}
	}
}
