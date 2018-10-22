<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Posts by Comments Count.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByCommentsModule
 *
 * @since 6.0.0
 */
class DeletePostsByCommentsModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByCommentsModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByCommentsModule();
	}

	/**
	 * Data provider to test posts can be deleted based on number of comments with equals operator.
	 */
	public function provide_data_to_test_posts_deletion_by_comments_count_with_equals_operator() {
		return array(
			// (+ve Case) Deletes all/few posts for the given comment count with equals operator.
			array(
				array(
					array(
						'posts'       => 15,
						'comments'    => 5,
						'post_status' => 'private',
					),
					array(
						'posts'       => 10,
						'comments'    => 6,
						'post_status' => 'publish',
					),
				),
				array(
					'operator'      => '=',
					'comment_count' => 5,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 15,
					'trashed_posts'   => 0,
					'available_posts' => 10,
				),
			),
			// (-ve Case) Deletes no posts for the given comment count with equals operator.
			array(
				array(
					array(
						'posts'       => 15,
						'comments'    => 6,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 10,
						'comments'    => 5,
						'post_status' => 'private',
					),
				),
				array(
					'operator'      => '=',
					'comment_count' => 3,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 0,
					'trashed_posts'   => 0,
					'available_posts' => 25,
				),
			),
		);
	}

	/**
	 * Data provider to test posts can be deleted based on number of comments with not equals operator.
	 */
	public function provide_data_to_test_posts_deletion_by_comments_count_with_not_equals_operator() {
		return array(
			// (+ve Case) Deletes all/few posts for the given comment count with not equals operator.
			array(
				array(
					array(
						'posts'       => 5,
						'comments'    => 5,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 10,
						'comments'    => 4,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 5,
						'comments'    => 3,
						'post_status' => 'private',
					),
				),
				array(
					'operator'      => '!=',
					'comment_count' => 5,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 15,
					'trashed_posts'   => 0,
					'available_posts' => 5,
				),
			),
			// (-ve Case) Deletes no posts for the given comment count with not equals operator.
			array(
				array(
					array(
						'posts'       => 15,
						'comments'    => 5,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 10,
						'comments'    => 5,
						'post_status' => 'draft',
					),
				),
				array(
					'operator'      => '!=',
					'comment_count' => 5,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 0,
					'trashed_posts'   => 0,
					'available_posts' => 25,
				),
			),
		);
	}

	/**
	 * Data provider to test posts can be deleted based on number of comments with greater than operator.
	 */
	public function provide_data_to_test_posts_deletion_by_comments_count_with_greater_than_operator() {
		return array(
			// (+ve Case) Deletes all/few posts for the given comment count with greater than operator.
			array(
				array(
					array(
						'posts'       => 15,
						'comments'    => 10,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 10,
						'comments'    => 3,
						'post_status' => 'publish',
					),
				),
				array(
					'operator'      => '>',
					'comment_count' => 3,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 15,
					'trashed_posts'   => 0,
					'available_posts' => 10,
				),
			),
			// (-ve Case) Deletes no posts for the given comment count with greater than operator.
			array(
				array(
					array(
						'posts'       => 15,
						'comments'    => 5,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 10,
						'comments'    => 3,
						'post_status' => 'publish',
					),
				),
				array(
					'operator'      => '>',
					'comment_count' => 5,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 0,
					'trashed_posts'   => 0,
					'available_posts' => 25,
				),
			),
		);
	}

	/**
	 * Data provider to test posts can be deleted based on number of comments with less than operator.
	 */
	public function provide_data_to_test_posts_deletion_by_comments_count_with_less_than_operator() {
		return array(
			// (+ve Case) Deletes all/few posts for the given comment count with less than operator.
			array(
				array(
					array(
						'posts'       => 15,
						'comments'    => 10,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 10,
						'comments'    => 4,
						'post_status' => 'publish',
					),
				),
				array(
					'operator'      => '<',
					'comment_count' => 5,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 10,
					'trashed_posts'   => 0,
					'available_posts' => 15,
				),
			),
			// (-ve Case) Deletes no posts for the given comment count with less than operator.
			array(
				array(
					array(
						'posts'       => 15,
						'comments'    => 5,
						'post_status' => 'publish',
					),
					array(
						'posts'       => 10,
						'comments'    => 7,
						'post_status' => 'publish',
					),
				),
				array(
					'operator'      => '<',
					'comment_count' => 5,
					'limit_to'      => 0,
					'restrict'      => false,
					'force_delete'  => true,
				),
				array(
					'deleted_posts'   => 0,
					'trashed_posts'   => 0,
					'available_posts' => 25,
				),
			),
		);
	}

	/**
	 * Test various test cases for deleting posts by comments count.
	 *
	 * @dataProvider provide_data_to_test_posts_deletion_by_comments_count_with_equals_operator
	 * @dataProvider provide_data_to_test_posts_deletion_by_comments_count_with_not_equals_operator
	 * @dataProvider provide_data_to_test_posts_deletion_by_comments_count_with_greater_than_operator
	 * @dataProvider provide_data_to_test_posts_deletion_by_comments_count_with_less_than_operator
	 *
	 * @param array $setup      Create posts using supplied arguments.
	 * @param array $operations User operations.
	 * @param array $expected   Expected output for respective operations.
	 */
	public function test_posts_can_be_deleted_by_posts_count( $setup, $operations, $expected ) {
		foreach ( $setup as $element ) {
			$post_ids = $this->factory->post->create_many( $element['posts'], array(
				'post_status' => $element['post_status'],
			) );
			foreach ( $post_ids as $post_id ) {
				$this->factory->comment->create_post_comments( $post_id, $element['comments'] );
			}
		}

		$delete_options = $operations;

		$deleted_posts = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['deleted_posts'], $deleted_posts );

		$trashed_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( $expected['trashed_posts'], count( $trashed_posts ) );

		$published_posts = $this->get_posts_by_status();
		$private_posts   = $this->get_posts_by_status( 'private' );
		$draft_posts     = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( $expected['available_posts'], count( $published_posts ) + count( $private_posts ) + count( $draft_posts ) );
	}
}
