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
			'date_op'   => '',
			'days'      => '',
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
			'date_op'   => '',
			'days'      => '',
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
			'date_op'    => '',
			'days'       => '',
			'restrict'   => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		// Todo: Don't delete all meta rows if there are duplicate meta keys.
		// See https://github.com/sudar/bulk-delete/issues/515
		// $this->assertTrue( metadata_exists( 'comment', $comment_id, $meta_key ) );
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
			'date_op'    => '',
			'days'       => 0,
			'restrict'   => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 3, $meta_deleted );

		$this->assertFalse( metadata_exists( 'comment', $comment_id_1, $meta_key ) );
		// Todo: Don't delete all meta rows if there are duplicate meta keys.
		// See https://github.com/sudar/bulk-delete/issues/515
		// $this->assertTrue( metadata_exists( 'comment', $comment_id_1, $another_meta_value ) );

		$this->assertFalse( metadata_exists( 'comment', $comment_id_2, $meta_key ) );
		$this->assertFalse( metadata_exists( 'comment', $comment_id_3, $meta_key ) );
	}

	/**
	 * Provide data to `test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_different_operations` method.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_different_operations() {
		return array(
			array(
				array(
					'post_type'          => 'post',
					'number_of_comments' => 5,
					'matched'            => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Matched Value',
					),
					'miss_matched'       => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Miss Matched Value',
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value',
					'operator'   => '=',
				),
				array(
					'number_of_comment_metas_deleted' => '5',
					'explicit_meta_data'              => 'Miss Matched Value',
				),
			),

			array(
				array(
					'post_type'          => 'post',
					'number_of_comments' => 5,
					'matched'            => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Matched Value',
					),
					'miss_matched'       => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Miss Matched Value',
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value',
					'operator'   => '!=',
				),
				array(
					'number_of_comment_metas_deleted' => '5',
					'explicit_meta_data'              => 'Matched Value',
				),
			),

			array(
				array(
					'post_type'          => 'post',
					'number_of_comments' => 5,
					'matched'            => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Matched Value',
					),
					'miss_matched'       => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Miss Matched Value',
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value',
					'operator'   => 'LIKE',
				),
				array(
					'number_of_comment_metas_deleted' => '5',
					'explicit_meta_data'              => 'Miss Matched Value',
				),
			),

			array(
				array(
					'post_type'          => 'post',
					'number_of_comments' => 5,
					'matched'            => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Matched Value',
					),
					'miss_matched'       => array(
						'meta_key'   => 'test_key',
						'meta_value' => 'Miss Matched Value',
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value',
					'operator'   => 'NOT LIKE',
				),
				array(
					'number_of_comment_metas_deleted' => '5',
					'explicit_meta_data'              => 'Matched Value',
				),
			),

			array(
				array(
					'post_type'          => 'post',
					'number_of_comments' => 5,
					'matched'            => array(
						'meta_key'   => 'test_key',
						'meta_value' => 10,
					),
					'miss_matched'       => array(
						'meta_key'   => 'test_key',
						'meta_value' => 20,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'NUMERIC',
					'meta_value' => 10,
					'operator'   => '=',
				),
				array(
					'number_of_comment_metas_deleted' => '5',
					'explicit_meta_data'              => 20,
				),
			),

			array(
				array(
					'post_type'          => 'post',
					'number_of_comments' => 5,
					'matched'            => array(
						'meta_key'   => 'test_key',
						'meta_value' => 10,
					),
					'miss_matched'       => array(
						'meta_key'   => 'test_key',
						'meta_value' => 20,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'NUMERIC',
					'meta_value' => 10,
					'operator'   => '!=',
				),
				array(
					'number_of_comment_metas_deleted' => '5',
					'explicit_meta_data'              => 10,
				),
			),
		);
	}

	/**
	 * Test deletion of comment meta from more than one comment using meta value with different operations.
	 *
	 * TODO: This test is currently skipped because duplicate meta keys is not fully supported yet.
	 *
	 * @see https://github.com/sudar/bulk-delete/issues/515 for details.
	 *
	 * @param array $setup create posts, comments and meta params.
	 * @param array $operation Possible operations.
	 * @param array $expected expected output.
	 *
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_different_operations
	 */
	public function test_that_comment_meta_from_multiple_comments_can_be_deleted_using_value_with_different_operations( $setup, $operation, $expected ) {
		$this->markTestSkipped(
			'Comments with the same meta key with multiple values is not fully supported yet'
		);

		$this->register_post_type( $setup['post_type'] );

		// Create a post.
		$post_id = $this->factory->post->create(
			array(
				'post_type' => $setup['post_type'],
			)
		);

		$comment_data = array(
			'comment_post_ID' => $post_id,
		);

		$comment_ids = array();
		for ( $i = 0; $i < $setup['number_of_comments']; $i++ ) {
			$comment_ids[ $i ] = $this->factory->comment->create( $comment_data );

			add_comment_meta( $comment_ids[ $i ], $setup['matched']['meta_key'], $setup['matched']['meta_value'] );
			add_comment_meta( $comment_ids[ $i ], $setup['miss_matched']['meta_key'], $setup['miss_matched']['meta_value'] );
		}

		$delete_options = array(
			'post_type'  => $setup['post_type'],
			'use_value'  => true,
			'meta_key'   => $operation['meta_key'],
			'meta_value' => $operation['meta_value'],
			'meta_type'  => $operation['meta_type'],
			'meta_op'    => $operation['operator'],
			'limit_to'   => 0,
			'date_op'    => '',
			'days'       => '',
			'restrict'   => false,
		);

		$comment_metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['number_of_comment_metas_deleted'], $comment_metas_deleted );

		for ( $i = 0; $i < $setup['number_of_comments']; $i++ ) {
			// Todo: Don't delete all meta rows if there are duplicate meta keys.
			// See https://github.com/sudar/bulk-delete/issues/515 for details.
			$this->assertFalse( metadata_exists( 'comment', $comment_ids[ $i ], $setup['matched']['meta_key'] ) );
			$this->assertTrue( metadata_exists( 'comment', $comment_ids[ $i ], $setup['miss_matched']['meta_key'] ) );
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

		$post_id = $this->factory->post->create( array(
			'post_type' => $post_type,
		) );

		$matched_comment_id = $this->factory->comment->create( array(
			'comment_post_ID' => $post_id,
			'comment_date'    => $comment_date,
		) );
		add_comment_meta( $matched_comment_id, $meta_key, $meta_value );

		$miss_matched_comment_id = $this->factory->comment->create( array(
			'comment_post_ID' => $post_id,
		) );
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
		$comment_1_id = $this->factory->comment->create( array(
			'comment_post_ID' => $post_id,
			'comment_content' => 'Test Comment',
			'comment_date'    => $comment_date,
		) );
		add_comment_meta( $comment_1_id, $meta_key, $meta_value );

		// Create a comment with current date as comment date.
		$comment_2_id = $this->factory->comment->create( array(
			'comment_post_ID' => $post_id,
			'comment_content' => 'Test Comment',
		) );
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
				'date_op'   => '',
				'days'      => '',
				'restrict'  => false,
			);

			$metas_deleted += $this->module->delete( $delete_options );
		}

		$this->assertEquals( $total_comments / 2, $metas_deleted );
	}
}
