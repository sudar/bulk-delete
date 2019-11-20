<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Comment Meta.
 *
 * Todo: The tests are not complete. Need to revisit them.
 *
 * Tests \BulkWP\BulkDelete\Core\Metas\Modules\DeleteCommentMetaModule
 *
 * @since 6.0.0
 */
class DeleteCommentMetaModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Metas\Modules\DeleteCommentMetaModule
	 */
	protected $module;

	/**
	 * Setup the test case.
	 *
	 * We need to call the `register` method on the module so that pro hooks are triggered.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeleteCommentMetaModule();
		$this->module->register( 'bulk-wp_page_bulk-delete-metas', 'bulk-delete-metas' );
	}

	/**
	 * Test deletion of comment meta from one comment.
	 */
	public function test_that_comment_meta_can_be_deleted_from_one_comment() {
		$post_type          = 'post';
		$meta_key           = 'test_key';
		$meta_value         = 'Test Value';
		$another_meta_key   = 'another meta key';
		$another_meta_value = 'Another Meta Value';

		// Create a post.
		$post_id = $this->factory->post->create(
			array(
				'post_title' => 'Test Post',
				'post_type'  => $post_type,
			)
		);

		$comment_data = array(
			'comment_post_ID' => $post_id,
			'comment_content' => 'Test Comment',
		);

		// Create a comment.
		$comment_id = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id, $meta_key, $meta_value );
		add_comment_meta( $comment_id, $another_meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => 0,
			'restrict'  => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$this->assertFalse( metadata_exists( 'comment', $comment_id, $meta_key ) );
		$this->assertTrue( metadata_exists( 'comment', $comment_id, $another_meta_key ) );
	}

	/**
	 * Test deletion of comment meta from more than one comment.
	 */
	public function test_that_comment_meta_can_be_deleted_from_multiple_comments() {
		$post_type          = 'post';
		$meta_key           = 'test_key';
		$meta_value         = 'Test Value';
		$another_meta_key   = 'another meta key';
		$another_meta_value = 'Another Meta Value';

		// Create a post.
		$post_id = $this->factory->post->create(
			array(
				'post_title' => 'Test Post',
				'post_type'  => $post_type,
			)
		);

		$comment_data = array(
			'comment_post_ID' => $post_id,
			'comment_content' => 'Test Comment',
		);

		$comment_id_1 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_1, $meta_key, $meta_value );
		add_comment_meta( $comment_id_1, $another_meta_key, $another_meta_value );

		$comment_id_2 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_2, $meta_key, $meta_value );
		add_comment_meta( $comment_id_2, $another_meta_key, $another_meta_value );

		$comment_id_3 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_3, $meta_key, $meta_value );
		add_comment_meta( $comment_id_3, $another_meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => 0,
			'restrict'  => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 3, $meta_deleted );

		$this->assertFalse( metadata_exists( 'comment', $comment_id_1, $meta_key ) );
		$this->assertTrue( metadata_exists( 'comment', $comment_id_1, $another_meta_key ) );

		$this->assertFalse( metadata_exists( 'comment', $comment_id_2, $meta_key ) );
		$this->assertTrue( metadata_exists( 'comment', $comment_id_2, $another_meta_key ) );

		$this->assertFalse( metadata_exists( 'comment', $comment_id_3, $meta_key ) );
		$this->assertTrue( metadata_exists( 'comment', $comment_id_3, $another_meta_key ) );
	}

	/**
	 * Test deletion of comment meta from one comment using meta value as well.
	 */
	public function test_that_comment_meta_can_be_deleted_from_one_comment_using_meta_value() {
		$post_type          = 'post';
		$meta_key           = 'test_key';
		$meta_value         = 'Test Value';
		$another_meta_value = 'Another Meta Value';

		// Create a post.
		$post_id = $this->factory->post->create(
			array(
				'post_title' => 'Test Post',
				'post_type'  => $post_type,
			)
		);

		$comment_data = array(
			'comment_post_ID' => $post_id,
			'comment_content' => 'Test Comment',
		);

		// Create a comment.
		$comment_id = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id, $meta_key, $meta_value );
		add_comment_meta( $comment_id, $meta_key, $another_meta_value );

		// call our method.
		$delete_options = array(
			'post_type'  => $post_type,
			'use_value'  => true,
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
			'meta_op'    => '=',
			'meta_type'  => 'CHAR',
			'limit_to'   => 0,
			'restrict'   => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		// Todo: Don't delete all meta rows if there are duplicate meta keys.
		// See https://github.com/sudar/bulk-delete/issues/515.
		// $this->assertTrue( metadata_exists( 'comment', $comment_id, $meta_key ) );.
	}

	/**
	 * Test deletion of comment meta from more than one comment using meta value as well.
	 */
	public function test_that_comment_meta_can_be_deleted_from_multiple_comments_with_meta_value() {
		$post_type          = 'post';
		$meta_key           = 'test_key';
		$meta_value         = 'Test Value';
		$another_meta_value = 'Another Meta Value';

		// Create a post.
		$post_id = $this->factory->post->create(
			array(
				'post_title' => 'Test Post',
				'post_type'  => $post_type,
			)
		);

		$comment_data = array(
			'comment_post_ID' => $post_id,
			'comment_content' => 'Test Comment',
		);

		// Create a comment.
		$comment_id_1 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_1, $meta_key, $meta_value );
		add_comment_meta( $comment_id_1, $meta_key, $another_meta_value );

		// Create a comment.
		$comment_id_2 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_2, $meta_key, $meta_value );

		// Create a comment.
		$comment_id_3 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_3, $meta_key, $meta_value );

		// call our method.
		$delete_options = array(
			'post_type'  => $post_type,
			'use_value'  => true,
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
			'meta_op'    => '=',
			'meta_type'  => 'CHAR',
			'limit_to'   => 0,
			'restrict'   => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 3, $meta_deleted );

		$this->assertFalse( metadata_exists( 'comment', $comment_id_1, $meta_key ) );
		// Todo: Don't delete all meta rows if there are duplicate meta keys.
		// See https://github.com/sudar/bulk-delete/issues/515.
		// $this->assertTrue( metadata_exists( 'comment', $comment_id_1, $another_meta_value ) );.
		$this->assertFalse( metadata_exists( 'comment', $comment_id_2, $meta_key ) );
		$this->assertFalse( metadata_exists( 'comment', $comment_id_3, $meta_key ) );
	}

	/**
	 * Provide data to test that comment meta from multiple comments can be deleted with equls operator
	 * with numeric, string and date type.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_equals_operator() {
		return array(
			// Numeric meta deletion.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 10,
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 20,
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 10,
						),
						'miss_matched'       => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => '10c',
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'NUMERIC',
					'meta_value' => 10,
					'operator'   => '=',
				),
				array(
					'number_of_comment_metas_deleted' => 8,
				),
			),
			// String meta deletion.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Matched Value',
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 'Matched Value',
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Matched Value',
						),
						'miss_matched'       => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => 'Value',
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value',
					'operator'   => '=',
				),
				array(
					'number_of_comment_metas_deleted' => 8,
				),
			),
			// Date type meta deletion.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-3 day' ) ),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'DATE',
					'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
					'operator'   => '=',
				),
				array(
					'number_of_comment_metas_deleted' => 5,
				),
			),
		);
	}

	/**
	 * Data Provider for IN operator with numeric, string and date types.
	 *
	 * @return array Data
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_in_operator() {
		return array(
			// Numeric meta deletion.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 10,
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 5,
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 5,
						),
						'miss_matched'       => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => 3,
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 2,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => '3',
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'NUMERIC',
					'meta_value' => '10, 5, 3',
					'operator'   => 'IN',
				),
				array(
					'number_of_comment_metas_deleted' => 10,
				),
			),
			// String meta deletion.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Matched Value',
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 'Matched Value',
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Test',
						),
						'miss_matched'       => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => 'Value',
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Matched Values',
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value,Test',
					'operator'   => 'IN',
				),
				array(
					'number_of_comment_metas_deleted' => 8,
				),
			),
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-3 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 2,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-2 day' ) ),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'DATE',
					'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ) . ',' . date( 'Y-m-d', strtotime( '-2 day' ) ),
					'operator'   => 'IN',
				),
				array(
					'number_of_comment_metas_deleted' => 7,
				),
			),
		);
	}

	/**
	 *  Data Provider for BETWEEN operator with numeric and date types.
	 *
	 * @return array Data
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_between_operator() {
		return array(
			// Numeric meta deletion.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 8,
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 5,
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 6,
						),
						'miss_matched'       => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => 7,
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'NUMERIC',
					'meta_value' => '6, 10',
					'operator'   => 'BETWEEN',
				),
				array(
					'number_of_comment_metas_deleted' => 8,
				),
			),
			// Date type meta deletion (BETWEEN OPERATOR with three values to check whether third value is truncated).
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d' ),
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '+2 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 2,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-2 day' ) ),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'DATE',
					'meta_value' => date( 'Y-m-d', strtotime( '-3 day' ) ) . ',' . date( 'Y-m-d', strtotime( '+1 day' ) ) . ',' . date( 'Y-m-d', strtotime( '+5 day' ) ),
					'operator'   => 'BETWEEN',
				),
				array(
					'number_of_comment_metas_deleted' => 7,
				),
			),
			// Date type meta deletion with NOT BETWEEN operator.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-5 day' ) ),
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '+2 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 2,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-2 day' ) ),
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'DATE',
					'meta_value' => date( 'Y-m-d', strtotime( '-3 day' ) ) . ',' . date( 'Y-m-d', strtotime( '+1 day' ) ),
					'operator'   => 'NOT BETWEEN',
				),
				array(
					'number_of_comment_metas_deleted' => 8,
				),
			),
		);
	}

	/**
	 *  Data Provider for EXISTS and NOT EXISTS operator with numeric type.
	 *
	 * @return array Data
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_exists_and_not_exists_operator() {
		return array(
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 8,
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 5,
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 6,
						),
						'miss_matched'       => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => 7,
						),
					),
				),
				array(
					'post_type' => 'post',
					'meta_key'  => 'test_key',
					'meta_type' => 'NUMERIC',
					'operator'  => 'EXISTS',
				),
				array(
					'number_of_comment_metas_deleted' => 8,
				),
			),
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => 20,
						),
						'matched'            => array(
							'meta_key'   => 'another_key',
							'meta_value' => 10,
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 2,
						'matched'            => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => 20,
						),
					),
				),
				array(
					'post_type' => 'post',
					'meta_key'  => 'test_key',
					'operator'  => 'NOT EXISTS',
				),
				array(
					'number_of_comment_metas_deleted' => 2,
				),
			),
		);
	}

	/**
	 * Provide data to test LIKE and NOT LIKE operators with string.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_like_and_not_like_operator() {
		return array(
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Test Value Me',
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 'Matched Value',
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Matched Value',
						),
						'miss_matched'       => array(
							'meta_key'   => 'one_more_key',
							'meta_value' => 'Value',
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Value',
					'operator'   => 'LIKE',
				),
				array(
					'number_of_comment_metas_deleted' => 8,
				),
			),
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Test Me',
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => 'Matched Value',
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => 'Matched value',
						),
					),
				),
				array(
					'post_type'  => 'post',
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Value',
					'operator'   => 'NOT LIKE',
				),
				array(
					'number_of_comment_metas_deleted' => 5,
				),
			),
		);
	}

	/**
	 *  Data Provider for DATE datatype with relative date, custom date and different date formats.
	 *
	 * @return array Data
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_date() {
		return array(
			// Date type with relative date.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
						'miss_matched'       => array(
							'meta_key'   => 'another_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '1 day' ) ),
						),
					),
				),
				array(
					'post_type'     => 'post',
					'meta_key'      => 'test_key',
					'meta_type'     => 'DATE',
					'relative_date' => 'yesterday',
					'operator'      => '=',
				),
				array(
					'number_of_comment_metas_deleted' => 5,
				),
			),
			// Date type with custom date (date unit and type).
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '-1 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'Y-m-d', strtotime( '10 day' ) ),
						),
					),
				),
				array(
					'post_type'     => 'post',
					'meta_key'      => 'test_key',
					'meta_type'     => 'DATE',
					'relative_date' => 'custom',
					'date_unit'     => '9',
					'date_type'     => 'day',
					'operator'      => '>=',
				),
				array(
					'number_of_comment_metas_deleted' => 3,
				),
			),
			// Date type with date unit and type and date format.
			array(
				array(
					array(
						'post_type'          => 'post',
						'number_of_comments' => 5,
						'miss_matched'       => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'd-m-Y', strtotime( '-1 day' ) ),
						),
					),
					array(
						'post_type'          => 'post',
						'number_of_comments' => 3,
						'matched'            => array(
							'meta_key'   => 'test_key',
							'meta_value' => date( 'd-m-Y', strtotime( '1 day' ) ),
						),
					),
				),
				array(
					'post_type'     => 'post',
					'meta_key'      => 'test_key',
					'meta_type'     => 'DATE',
					'relative_date' => 'custom',
					'date_unit'     => '1',
					'date_type'     => 'day',
					'date_format'   => '%d-%m-%Y',
					'operator'      => '>=',
				),
				array(
					'number_of_comment_metas_deleted' => 3,
				),
			),
		);
	}

	/**
	 * Test deletion of comment meta from more than one comment using meta value with different operations.
	 *
	 * TODO: This test is currently skipped because duplicate meta keys is not fully supported yet.
	 *
	 * @see          https://github.com/sudar/bulk-delete/issues/515 for details.
	 *
	 * @param array $setup     create posts, comments and meta params.
	 * @param array $operation Possible operations.
	 * @param array $expected  expected output.
	 *
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_equals_operator
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_in_operator
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_between_operator
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_exists_and_not_exists_operator
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_like_and_not_like_operator
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_date
	 */
	public function test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_different_operations( $setup, $operation, $expected ) {
		$size = count( $setup );
		foreach ( $setup as $element ) {
			$this->register_post_type( $element['post_type'] );

			// Create a post.
			$post_id = $this->factory->post->create(
				array(
					'post_type' => $element['post_type'],
				)
			);

			$comment_data = array(
				'comment_post_ID' => $post_id,
			);

			$comment_ids = array();
			for ( $i = 0; $i < $element['number_of_comments']; $i++ ) {
				$comment_ids[ $i ] = $this->factory->comment->create( $comment_data );

				if ( array_key_exists( 'matched', $element ) ) {
					add_comment_meta( $comment_ids[ $i ], $element['matched']['meta_key'], $element['matched']['meta_value'] );
				}
				if ( array_key_exists( 'miss_matched', $element ) ) {
					add_comment_meta( $comment_ids[ $i ], $element['miss_matched']['meta_key'], $element['miss_matched']['meta_value'] );
				}
			}
			$all_comment_ids[] = $comment_ids;
		}

		$delete_options = array(
			'post_type' => $operation['post_type'],
			'use_value' => true,
			'meta_key'  => $operation['meta_key'],
			'meta_op'   => $operation['operator'],
			'limit_to'  => 0,
			'restrict'  => false,
		);
		if ( array_key_exists( 'meta_type', $operation ) ) {
			$delete_options['meta_type'] = $operation['meta_type'];
		}
		if ( array_key_exists( 'meta_value', $operation ) ) {
			$delete_options['meta_value'] = $operation['meta_value'];
		}
		if ( array_key_exists( 'relative_date', $operation ) ) {
			$delete_options['relative_date'] = $operation['relative_date'];
		}
		if ( array_key_exists( 'date_format', $operation ) ) {
			$delete_options['meta_value_date_format'] = $operation['date_format'];
		}
		if ( array_key_exists( 'date_unit', $operation ) ) {
			$delete_options['date_unit'] = $operation['date_unit'];
		}
		if ( array_key_exists( 'date_type', $operation ) ) {
			$delete_options['date_type'] = $operation['date_type'];
		}

		$comment_metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['number_of_comment_metas_deleted'], $comment_metas_deleted );

		for ( $j = 0; $j < $size; $j++ ) {
			for ( $i = 0; $i < $setup[ $j ]['number_of_comments']; $i++ ) {
				// Todo: Don't delete all meta rows if there are duplicate meta keys.
				// See https://github.com/sudar/bulk-delete/issues/515 for details.
				if ( array_key_exists( 'matched', $setup[ $j ] ) ) {
					$this->assertFalse( metadata_exists( 'comment', $all_comment_ids[ $j ][ $i ], $setup[ $j ]['matched']['meta_key'] ) );
				}
				if ( array_key_exists( 'miss_matched', $setup[ $j ] ) ) {
					$this->assertTrue( metadata_exists( 'comment', $all_comment_ids[ $j ][ $i ], $setup[ $j ]['miss_matched']['meta_key'] ) );
				}
			}
		}
	}

	/**
	 * Test deleting comment meta from comments that are older than x days.
	 */
	public function test_that_comment_meta_can_be_deleted_from_comments_older_than_x_days() {
		$post_type    = 'post';
		$meta_key     = 'test_key';
		$meta_value   = 'Test Value';
		$comment_date = date( 'Y-m-d H:i:s', strtotime( '-2 day' ) );

		$post_id = $this->factory->post->create(
			array(
				'post_type' => $post_type,
			)
		);

		$matched_comment_id = $this->factory->comment->create(
			array(
				'comment_post_ID' => $post_id,
				'comment_date'    => $comment_date,
			)
		);
		add_comment_meta( $matched_comment_id, $meta_key, $meta_value );

		$miss_matched_comment_id = $this->factory->comment->create(
			array(
				'comment_post_ID' => $post_id,
			)
		);
		add_comment_meta( $miss_matched_comment_id, $meta_key, $meta_value );

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => 0,
			'date_op'   => 'before',
			'days'      => 1,
			'restrict'  => true,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$this->assertFalse( metadata_exists( 'comment', $matched_comment_id, $meta_key ) );
		$this->assertTrue( metadata_exists( 'comment', $miss_matched_comment_id, $meta_key ) );
	}

	/**
	 * Test deleting comment meta from comments that are newer than x days.
	 */
	public function test_that_comment_meta_can_be_deleted_from_comments_newer_than_x_days() {
		$post_type    = 'post';
		$meta_key     = 'test_key';
		$meta_value   = 'Test Value';
		$comment_date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		// Create a post.
		$post_id = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		// Create a comment with comment date older than 5 days.
		$comment_1_id = $this->factory->comment->create(
			array(
				'comment_post_ID' => $post_id,
				'comment_content' => 'Test Comment',
				'comment_date'    => $comment_date,
			)
		);
		add_comment_meta( $comment_1_id, $meta_key, $meta_value );

		// Create a comment with current date as comment date.
		$comment_2_id = $this->factory->comment->create(
			array(
				'comment_post_ID' => $post_id,
				'comment_content' => 'Test Comment',
			)
		);
		add_comment_meta( $comment_2_id, $meta_key, $meta_value );

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => 0,
			'date_op'   => 'after',
			'days'      => '3',
			'restrict'  => true,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$this->assertTrue( metadata_exists( 'comment', $comment_1_id, $meta_key ) );
		$this->assertFalse( metadata_exists( 'comment', $comment_2_id, $meta_key ) );
	}

	/**
	 * Test that comment meta can be deleted in batches.
	 *
	 * Todo: Handle cases where the metas to be deleted may not be in the front.
	 */
	public function test_that_comment_meta_can_be_deleted_in_batches() {
		$post_type          = 'post';
		$meta_key           = 'test_key';
		$meta_value         = 'Test Value';
		$another_meta_key   = 'test_key_2';
		$another_meta_value = 'Test Value 2';

		$total_comments = 100;
		$batch_size     = 20;

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
		);

		for ( $i = 0; $i < ( $total_comments / 2 ); $i ++ ) {
			$comment_id = $this->factory->comment->create( $comment_data );

			add_comment_meta( $comment_id, $meta_key, $meta_value );
		}

		for ( $i = 0; $i < ( $total_comments / 2 ); $i ++ ) {
			$comment_id = $this->factory->comment->create( $comment_data );

			add_comment_meta( $comment_id, $another_meta_key, $another_meta_value );
		}

		$metas_deleted = 0;

		// call our method.
		for ( $i = 0; $i < ( $total_comments / $batch_size ); $i ++ ) {
			$delete_options = array(
				'post_type' => $post_type,
				'use_value' => false,
				'meta_key'  => $meta_key,
				'limit_to'  => $batch_size,
				'restrict'  => false,
			);

			$metas_deleted += $this->module->delete( $delete_options );
		}

		$this->assertEquals( $total_comments / 2, $metas_deleted );
	}

	/**
	 * Provide data to test deletion of comment meta with custom post status.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_comment_meta_deletion_with_custom_post_status() {
		return array(
			// (+ve) case: Built-in post type and custom post status.
			array(
				array(
					'post_type'   => 'post',
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
					'post_type' => 'post|wc-completed',
					'use_value' => false,
					'limit_to'  => 0,
					'restrict'  => false,
				),
				array(
					'number_of_comment_metas_deleted' => 5,
					'left_over_comment_meta'          => 'another_key',
				),
			),
			// (+ve) case: Custom post type and custom post status.
			array(
				array(
					'post_type'   => 'product',
					'post_status' => 'wc-completed',
					'no_of_posts' => 3,
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
					'use_value' => false,
					'limit_to'  => 0,
					'restrict'  => false,
				),
				array(
					'number_of_comment_metas_deleted' => 3,
					'left_over_comment_meta'          => 'another_key',
				),
			),
		);
	}

	/**
	 * Test deletion of comment meta with custom post status.
	 *
	 * @param array $setup     create posts, comments and meta params.
	 * @param array $operation Possible operations.
	 * @param array $expected  expected output.
	 *
	 * @dataProvider provide_data_to_test_comment_meta_deletion_with_custom_post_status
	 */
	public function test_comment_meta_deletion_with_custom_post_status( $setup, $operation, $expected ) {
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
			$comment_data = array(
				'comment_post_ID' => $post_id,
				'comment_content' => 'Test Comment',
			);
			$comment_id   = $this->factory->comment->create( $comment_data );
			foreach ( $metas as $meta ) {
				add_comment_meta( $comment_id, $meta['key'], $meta['value'] );
			}
		}

		$delete_options = $operation;

		$comment_metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['number_of_comment_metas_deleted'], $comment_metas_deleted );

		$this->assertTrue( metadata_exists( 'comment', $comment_id, $expected['left_over_comment_meta'] ) );
		$this->assertFalse( metadata_exists( 'comment', $comment_id, $operation['meta_key'] ) );
	}
}
