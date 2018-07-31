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
	public function test_that_deleting_comment_meta_from_one_comment() {

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
	public function test_that_deleting_comment_meta_from_more_than_one_comment() {

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
	public function test_that_deleting_comment_meta_from_one_comment_using_meta_value() {

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
			'post_type' 	=> $post_type,
			'use_value' 	=> true,
			'meta_key'  	=> $meta_key,
			'meta_value'  	=> $meta_value,
			'meta_op'  		=> '=',
			'meta_type'  	=> 'CHAR',
			'limit_to'  	=> -1,
			'date_op'   	=> '',
			'days'      	=> '',
			'restrict'  	=> false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 1, $meta_deleted );

		$comment_meta = get_comment_meta( $comment_id, $meta_key );
		$this->assertEquals( 0, count( $comment_meta ) );
	}

	/**
	 * Add to test deleting comment meta from more than one comment using meta value as well.
	 */
	public function test_that_deleting_comment_meta_from_more_than_one_comment_using_meta_value() {

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
			'post_type' 	=> $post_type,
			'use_value' 	=> true,
			'meta_key'  	=> $meta_key,
			'meta_value'  	=> $meta_value,
			'meta_op'  		=> '=',
			'meta_type'  	=> 'CHAR',
			'limit_to'  	=> -1,
			'date_op'   	=> '',
			'days'      	=> '',
			'restrict'  	=> false,
		);

		$meta_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 3, $meta_deleted );

	}

	/**
	 * Add to test deleting comment meta from more than one comment using meta value as well with different operations.
	 */
	public function test_that_deleting_comment_meta_from_more_than_one_comment_using_meta_value_as_well_with_different_operations() {

		$post_type  = 'post';
		$number_of_comments = 10;
		$values = array(
			'CHAR' => array(
				'meta_key'   => 'test_key',
				'meta_value' => 'Test Value',
				'valid_op'	 => array(
					'=',
					'!=',
					'LIKE',
					'NOT LIKE',
				),
			),
			'NUMERIC' => array(
				'meta_key'   => 'test_key',
				'meta_value' => '10',
				'valid_op'	 => array(
					'=',
					'!=',
					'<',
					'<=',
					'>',
					'>=',
				),
			),
		);

		// Create a post.
		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		for( $i = 1; $i <= $number_of_comments; $i++ ){

			foreach( $values as $value ){
				
				$comment_data = array(
					'comment_post_ID' => $post,
					'comment_content' => 'Test Comment',
				);

				$comment_id = $this->factory->comment->create( $comment_data );
				add_comment_meta( $comment_id, $value['meta_key'], $value['meta_value'] );
			}

		}

		foreach( $values as $key => $value ){

			foreach( $value['valid_op'] as $valid_op_value ){

				$delete_options = array(
					'post_type' 	=> $post_type,
					'use_value' 	=> true,
					'meta_key'  	=> $value['meta_key'],
					'meta_value'  	=> $value['meta_value'],
					'meta_op'  		=> $valid_op_value,
					'meta_type'  	=> $key,
					'limit_to'  	=> -1,
					'date_op'   	=> '',
					'days'      	=> '',
					'restrict'  	=> false,
				);

				$meta_deleted = $this->module->delete( $delete_options );
				$this->assertEquals( $number_of_comments, $meta_deleted );

			}
			
		}

	}

}
