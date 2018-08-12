<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of comment meta.
 *
 * Tests \BulkWP\BulkDelete\Core\Metas\Modules\DeleteUserMetaModule
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
	 * Setup the module.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeleteCommentMetaModule();
		$this->module->register( 'bulk-wp_page_bulk-delete-metas', 'bulk-delete-metas' );
	}

	/**
	 * Add to test deleting comment meta from one comment.
	 */
	public function test_that_comment_meta_can_be_deleted_from_one_comment() {

		$post_type  = 'post';
		$meta_key   = 'test_key';
		$meta_value = 'Test Value';

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
		);

		// Create a comment.
		$comment_id = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id, $meta_key, $meta_value );

		add_comment_meta( $comment_id, 'another', 'Another' );

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

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );

		$another_comment_meta = get_comment_meta( $comment_id, 'another' );
		$this->assertEquals( 1, count( $another_comment_meta ) );
	}

	/**
	 * Add to test deleting comment meta from more than one comment.
	 */
	public function test_that_comment_meta_can_be_deleted_from_multiple_comments() {

		$post_type  = 'post';
		$meta_key   = 'test_key';
		$meta_value = 'Test Value';

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
		);

		// Create a comment.
		$comment_id_1 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_1, $meta_key, $meta_value );

		add_comment_meta( $comment_id_1, 'another', 'Another' );

		// Create a comment.
		$comment_id_2 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_2, $meta_key, $meta_value );

		// Create a comment.
		$comment_id_3 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_3, $meta_key, $meta_value );

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

		$another_comment_meta = get_comment_meta( $comment_id_1, 'another' );
		$this->assertEquals( 1, count( $another_comment_meta ) );

	}

	/**
	 * Add to test deleting comment meta from one comment using meta value as well.
	 */
	public function test_that_comment_meta_can_be_deleted_from_one_comment_with_meta_value() {

		$post_type  = 'post';
		$meta_key   = 'test_key';
		$meta_value = 'Test Value';

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
		);

		// Create a comment.
		$comment_id = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id, $meta_key, $meta_value );

		add_comment_meta( $comment_id, 'another', 'Another' );

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

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );

		$another_comment_meta = get_comment_meta( $comment_id, 'another' );
		$this->assertEquals( 1, count( $another_comment_meta ) );
	}

	/**
	 * Add to test deleting comment meta from more than one comment using meta value as well.
	 */
	public function test_that_comment_meta_can_be_deleted_from_multiple_comment_with_meta_value() {

		$post_type  = 'post';
		$meta_key   = 'test_key';
		$meta_value = 'Test Value';

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
		);

		// Create a comment.
		$comment_id_1 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_1, $meta_key, $meta_value );

		add_comment_meta( $comment_id_1, 'another', 'Another' );

		// Create a comment.
		$comment_id_2 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_2, $meta_key, $meta_value );

		// Create a comment.
		$comment_id_3 = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id_3, $meta_key, $meta_value );

		// call our method.
		$delete_options = array(
			'post_type'    => $post_type,
			'use_value'    => 1,
			'meta_key'     => $meta_key,
			'meta_value'   => $meta_value,
			'meta_op'      => '=',
			'meta_type'    => 'CHAR',
			'limit_to'     => 0,
			'date_op'      => '',
			'days'         => 0,
			'restrict'     => false,
			'force_delete' => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 3, $meta_deleted );

		$another_comment_meta = get_comment_meta( $comment_id_1, 'another' );
		$this->assertEquals( 1, count( $another_comment_meta ) );

	}

	/**
	 * Data provider to test `test_that_comment_meta_from_multiple_comments` method.
	 *
	 * @return array Data.
	 */
	public function provide_data_to_test_that_comment_meta_from_multiple_comments() {
		return array(
			array(
				array(
					'matched'      => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Matched Value',
						'number_of_comments' => 5,
					),
					'miss_matched' => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Miss Matched Value',
						'number_of_comments' => 3,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value',
					'operator'   => '=',
				),
				array(
					'matched'      => '5',
					'miss_matched' => '3',
				),
			),

			array(
				array(
					'matched'      => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Matched Value',
						'number_of_comments' => 5,
					),
					'miss_matched' => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Miss Matched Value',
						'number_of_comments' => 3,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Miss Matched Value',
					'operator'   => '!=',
				),
				array(
					'matched'      => '5',
					'miss_matched' => '3',
				),
			),

			array(
				array(
					'matched'      => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Matched Value',
						'number_of_comments' => 5,
					),
					'miss_matched' => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Another Value',
						'number_of_comments' => 3,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Matched Value',
					'operator'   => 'LIKE',
				),
				array(
					'matched'      => '5',
					'miss_matched' => '3',
				),
			),

			array(
				array(
					'matched'      => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Matched Value',
						'number_of_comments' => 5,
					),
					'miss_matched' => array(
						'meta_key'           => 'test_key',
						'meta_value'         => 'Miss Matched Value',
						'number_of_comments' => 3,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'CHAR',
					'meta_value' => 'Miss Matched Value',
					'operator'   => 'NOT LIKE',
				),
				array(
					'matched'      => '5',
					'miss_matched' => '3',
				),
			),

			array(
				array(
					'matched'      => array(
						'meta_key'           => 'test_key',
						'meta_value'         => '10',
						'number_of_comments' => 5,
					),
					'miss_matched' => array(
						'meta_key'           => 'test_key',
						'meta_value'         => '20',
						'number_of_comments' => 3,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'NUMERIC',
					'meta_value' => '10',
					'operator'   => '=',
				),
				array(
					'matched'      => '5',
					'miss_matched' => '3',
				),
			),

			array(
				array(
					'matched'      => array(
						'meta_key'           => 'test_key',
						'meta_value'         => '10',
						'number_of_comments' => 5,
					),
					'miss_matched' => array(
						'meta_key'           => 'test_key',
						'meta_value'         => '20',
						'number_of_comments' => 3,
					),
				),
				array(
					'meta_key'   => 'test_key',
					'meta_type'  => 'NUMERIC',
					'meta_value' => '10',
					'operator'   => '!=',
				),
				array(
					'matched'      => '3',
					'miss_matched' => '0',
				),
			),

		);
	}

	/**
	 * Add to test deleting comment meta from more than one comment using meta value as well with different operations.
	 *
	 * @param array $input create posts, comments and meta params.
	 * @param array $opetation Possible operations.
	 * @param array $expected expected output.
	 *
	 * @dataProvider provide_data_to_test_that_comment_meta_from_multiple_comments
	 */
	public function test_that_comment_meta_from_multiple_comments( $input, $opetation, $expected ) {
		$post_type = 'post';

		// Create a post.
		$post = $this->factory->post->create(
			array(
				'post_type' => $post_type,
			)
		);

		$comment_data = array(
			'comment_post_ID' => $post,
		);

		foreach ( $input as $meta ) {
			for ( $i = 0; $i < $meta['number_of_comments']; $i++ ) {
				$comment_id = $this->factory->comment->create( $comment_data );
				add_comment_meta( $comment_id, $meta['meta_key'], $meta['meta_value'] );
			}
		}

		$delete_options = array(
			'post_type'  => $post_type,
			'use_value'  => true,
			'meta_key'   => $opetation['meta_key'],
			'meta_value' => $opetation['meta_value'],
			'meta_type'  => $opetation['meta_type'],
			'meta_op'    => $opetation['operator'],
			'limit_to'   => 0,
			'date_op'    => '',
			'days'       => '',
			'restrict'   => false,
		);

		$comment_metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['matched'], $comment_metas_deleted );

		$args = array(
			'post_type'  => $post_type,
			'meta_key'   => $input['miss_matched']['meta_key'],
			'meta_value' => $input['miss_matched']['meta_value'],
		);

		$comments = get_comments( $args );

		$this->assertEquals( $expected['miss_matched'], count( $comments ) );

	}

	/**
	 * Add to test deleting comment meta older than x days.
	 */
	public function test_that_comment_meta_can_be_deleted_olderthan_x_days() {

		$post_type  = 'post';
		$meta_key   = 'test_key';
		$meta_value = 'Test Value';
		$day_post   = date( 'Y-m-d H:i:s', strtotime( '-2 day' ) );

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
			'comment_date'    => $day_post,
		);

		// Create a comment.
		$comment_id = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id, $meta_key, $meta_value );

		add_comment_meta( $comment_id, 'another', 'Another' );

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

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );

		$another_comment_meta = get_comment_meta( $comment_id, 'another' );
		$this->assertEquals( 1, count( $another_comment_meta ) );
	}

	/**
	 * Add to test deleting comment meta older than x days.
	 */
	public function test_that_comment_meta_can_be_deleted_last_x_days() {

		$post_type  = 'post';
		$meta_key   = 'test_key';
		$meta_value = 'Test Value';
		$day_post   = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
			'comment_date'    => $day_post,
		);

		// Create a comment.
		$comment_id = $this->factory->comment->create( $comment_data );

		add_comment_meta( $comment_id, $meta_key, $meta_value );

		add_comment_meta( $comment_id, 'another', 'Another' );

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => 0,
			'date_op'   => 'after',
			'days'      => '5',
			'restrict'  => true,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );

		$another_comment_meta = get_comment_meta( $comment_id, 'another' );
		$this->assertEquals( 1, count( $another_comment_meta ) );

	}

	/**
	 * Add to test deleting comment meta in batches.
	 */
	public function test_that_comment_meta_can_be_deleted_in_batches() {

		$post_type  = 'post';
		$meta_key   = 'test_key';
		$meta_value = 'Test Value';

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$comment_data = array(
			'comment_post_ID' => $post,
			'comment_content' => 'Test Comment',
		);

		// Create 100 comments.
		for ( $i = 1; $i <= 100; $i++ ) {
			$comment_id = $this->factory->comment->create( $comment_data );

			add_comment_meta( $comment_id, $meta_key, $meta_value );
		}

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => 50,
			'date_op'   => '',
			'days'      => '',
			'restrict'  => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $meta_deleted );

	}
}
