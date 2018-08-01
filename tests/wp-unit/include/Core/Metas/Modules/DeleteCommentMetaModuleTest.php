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
	}

	/**
	 * Add to test deleting comment meta from one comment.
	 */
	public function test_that_comment_meta_from_one_comment_can_be_deleted() {

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

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => -1,
			'date_op'   => '',
			'days'      => '',
			'restrict'  => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );
	}

	/**
	 * Add to test deleting comment meta from more than one comment.
	 */
	public function test_that_comment_meta_from_multiple_comments_can_be_deleted() {

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
			'limit_to'  => -1,
			'date_op'   => '',
			'days'      => '',
			'restrict'  => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 3, $meta_deleted );

	}

	/**
	 * Add to test deleting comment meta from one comment using meta value as well.
	 */
	public function test_that_comment_meta_from_one_comment_can_be_deleted_using_meta_values() {

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

		// call our method.
		$delete_options = array(
			'post_type'  => $post_type,
			'use_value'  => true,
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
			'meta_op'    => '=',
			'meta_type'  => 'CHAR',
			'limit_to'   => -1,
			'date_op'    => '',
			'days'       => '',
			'restrict'   => false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );
	}

	/**
	 * Add to test deleting comment meta from more than one comment using meta value as well.
	 */
	public function test_that_comment_meta_from_multiple_comments_can_be_deleted_using_meta_values() {

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

	}

	/**
	 * Data provider to test `meta_value_with_different_operations` method.
	 *
	 * @return array Data.
	 */
	public function different_operations() {
		$meta_key = 'test_key';

		return array(
			array(
				$meta_key,
				array(
					'meta_type'  => 'CHAR',
					'meta_value' => 'This value should match',
					'operator'   => '=',
				),
				5,
			),
		);
	}

	/**
	 * Add to test deleting comment meta from more than one comment using meta value as well with different operations.
	 *
	 * @param string $meta_key Meta key.
	 * @param        $operators
	 * @param        $comment_metas_to_be_deleted
	 *
	 * @dataProvider different_operations
	 */
	public function test_that_comment_meta_from_multiple_comments_can_be_deleted_using_different_operators( $meta_key, $operators, $comment_metas_to_be_deleted ) {
		$post_type               = 'post';
		$matching_meta_value     = 'This value should match';
		$non_matching_meta_value = 'This value should not match';

		// Create a post.
		$post = $this->factory->post->create(
			array(
				'post_title' => 'Test Post',
				'post_type'  => $post_type,
			)
		);

		for ( $i = 0; $i < 5; $i++ ) {
			$comment_data = array(
				'comment_post_ID' => $post,
			);

			$comment_id = $this->factory->comment->create( $comment_data );
			add_comment_meta( $comment_id, $meta_key, $matching_meta_value );
		}

		for ( $i = 0; $i < 5; $i++ ) {
			$comment_data = array(
				'comment_post_ID' => $post,
			);

			$comment_id = $this->factory->comment->create( $comment_data );
			add_comment_meta( $comment_id, $meta_key, $non_matching_meta_value );
		}

		$delete_options = array(
			'post_type'  => $post_type,
			'use_value'  => true,
			'meta_key'   => $meta_key,
			'meta_value' => $operators['meta_value'],
			'meta_op'    => $operators['operator'],
			'meta_type'  => $operators['meta_type'],
			'limit_to'   => 0,
			'date_op'    => '',
			'days'       => '',
			'restrict'   => false,
		);

		$comment_metas_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( $comment_metas_to_be_deleted, $comment_metas_deleted );
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

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => -1,
			'date_op'   => 'before',
			'days'      => 1,
			'restrict'  => true,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );
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

		// call our method.
		$delete_options = array(
			'post_type' => $post_type,
			'use_value' => false,
			'meta_key'  => $meta_key,
			'limit_to'  => -1,
			'date_op'   => 'after',
			'days'      => '5',
			'restrict'  => true,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );
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
