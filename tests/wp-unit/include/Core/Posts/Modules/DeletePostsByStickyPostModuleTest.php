<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Posts by StickyPosts.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStickyPostModule
 *
 * @since 6.0.0
 */
class DeletePostsByStickyPostModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStickyPostModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByStickyPostModule();
	}

	/**
	 * Data provider to test posts can be made non-sticky.
	 */
	public function provide_data_to_test_remove_sticky() {
		return array(
			// (+ve Case) For making all posts non-sticky.
			array(
				array(
					'non_sticky_posts' => 10,
					'sticky_posts'     => 15,
				),
				array(
					'selected_posts' => 'all',
					'sticky_action'  => 'unsticky',

				),
				array(
					'deleted_or_modified_posts' => 15,
					'trashed_posts'             => 0,
					'sticky_posts'              => 0,
					'published_posts'           => 25,
				),
			),
			// (+ve Case) For making selected posts non-sticky.
			array(
				array(
					'non_sticky_posts' => 5,
					'sticky_posts'     => 10,
				),
				array(
					'selected_posts' => 'selected_posts_ids',
					'sticky_action'  => 'unsticky',
				),
				array(
					'deleted_or_modified_posts' => 5,
					'trashed_posts'             => 0,
					'sticky_posts'              => 5,
					'published_posts'           => 15,
				),
			),
		);
	}

	/**
	 * Data provider to test sticky posts can be moved to trash.
	 */
	public function provide_data_to_test_sticky_posts_can_be_moved_to_trash() {
		return array(
			// (+ve Case) All sticky posts can be moved to trash.
			array(
				array(
					'non_sticky_posts' => 10,
					'sticky_posts'     => 15,
				),
				array(
					'selected_posts' => 'all',
					'sticky_action'  => 'delete',
					'limit_to'       => 0,
					'restrict'       => false,
					'force_delete'   => false,
				),
				array(
					'deleted_or_modified_posts' => 15,
					'trashed_posts'             => 15,
					'sticky_posts'              => 0,
					'published_posts'           => 10,
				),
			),
			// (+ve Case) Selected sticky posts can be moved to trash.
			array(
				array(
					'non_sticky_posts' => 5,
					'sticky_posts'     => 10,
				),
				array(
					'selected_posts' => 'selected_posts_ids',
					'sticky_action'  => 'delete',
					'limit_to'       => 0,
					'restrict'       => false,
					'force_delete'   => false,
				),
				array(
					'deleted_or_modified_posts' => 5,
					'trashed_posts'             => 5,
					'sticky_posts'              => 5,
					'published_posts'           => 10,
				),
			),
		);
	}

	/**
	 * Data provider to test sticky posts can be deleted.
	 */
	public function provide_data_to_test_sticky_posts_can_be_deleted() {
		return array(
			// (+ve Case) All sticky posts can be deleted.
			array(
				array(
					'non_sticky_posts' => 10,
					'sticky_posts'     => 15,
				),
				array(
					'selected_posts' => 'all',
					'sticky_action'  => 'delete',
					'limit_to'       => 0,
					'restrict'       => false,
					'force_delete'   => true,
				),
				array(
					'deleted_or_modified_posts' => 15,
					'trashed_posts'             => 0,
					'sticky_posts'              => 0,
					'published_posts'           => 10,
				),
			),
			// (+ve Case) Selected sticky posts can be deleted.
			array(
				array(
					'non_sticky_posts' => 5,
					'sticky_posts'     => 10,
				),
				array(
					'selected_posts' => 'selected_posts_ids',
					'sticky_action'  => 'delete',
					'limit_to'       => 0,
					'restrict'       => false,
					'force_delete'   => true,
				),
				array(
					'deleted_or_modified_posts' => 5,
					'trashed_posts'             => 0,
					'sticky_posts'              => 5,
					'published_posts'           => 10,
				),
			),
		);
	}

	/**
	 * Helper Method to be removed later
	 */
	protected function get_sticky_posts() {
		return get_option( 'sticky_posts' );
	}

	/**
	 * Test various test cases for deleting posts by taxonomy.
	 *
	 * @dataProvider provide_data_to_test_remove_sticky
	 * @dataProvider provide_data_to_test_sticky_posts_can_be_moved_to_trash
	 * @dataProvider provide_data_to_test_sticky_posts_can_be_deleted
	 *
	 * @param array $setup      Create posts using supplied arguments.
	 * @param array $operations User operations.
	 * @param array $expected   Expected output for respective operations.
	 */
	public function test_posts_can_be_deleted_by_sticky_posts( $setup, $operations, $expected ) {
		$sticky_posts_count     = $setup['sticky_posts'];
		$non_sticky_posts_count = $setup['non_sticky_posts'];

		$post_ids = $this->factory->post->create_many( $sticky_posts_count, array(
			'post_status' => 'publish',
		) );
		update_option( 'sticky_posts', $post_ids );

		$sticky_posts = $this->get_sticky_posts();
		$this->assertEquals( $sticky_posts_count, count( $sticky_posts ) );

		$published_posts = $this->factory->post->create_many( $non_sticky_posts_count, array(
			'post_status' => 'publish',
		) );
		$this->assertEquals( $non_sticky_posts_count, count( $published_posts ) );

		$all_posts = $this->get_posts_by_status();
		$this->assertEquals( $non_sticky_posts_count + $sticky_posts_count, count( $all_posts ) );

		$delete_options = $operations;
		if ( 'all' === $operations['selected_posts'] ) {
			$delete_options['selected_posts'] = array( 'all' );
		} else {
			$delete_options['selected_posts'] = array( $post_ids[1], $post_ids[3], $post_ids[5], $post_ids[6], $post_ids[8] );
		}

		$deleted_posts = $this->module->delete( $delete_options );
		$this->assertEquals( $expected['deleted_or_modified_posts'], $deleted_posts );

		$sticky_posts = $this->get_sticky_posts();
		$this->assertEquals( $expected['sticky_posts'], count( $sticky_posts ) );

		$available_posts = $this->get_posts_by_status(); // Includes sticky posts.
		$this->assertEquals( $expected['published_posts'], count( $available_posts ) );

		$trashed_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( $expected['trashed_posts'], count( $trashed_posts ) );
	}

}
