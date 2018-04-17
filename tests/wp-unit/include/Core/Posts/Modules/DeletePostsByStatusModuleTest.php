<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Posts by status.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStatusModule
 *
 * @since 6.0.0
 */
class DeletePostsByStatusModuleTest extends WPCoreUnitTestCase {

	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStatusModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByStatusModule();
	}

	/**
	 * Test that posts from a single post status (published posts) can be trashed.
	 */
	public function test_that_published_posts_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_posts ) );
	}

	/**
	 * Test that posts from a single post status (draft posts) can be trashed.
	 */
	public function test_that_draft_posts_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'draft',
		) );

		$draft_posts = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( 10, count( $draft_posts ) );

		$delete_options = array(
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$draft_posts = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_posts ) );
	}

	/**
	 * Test that posts from a single post status (published posts) can be permanently deleted.
	 */
	public function test_that_published_posts_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'publish',
		) );

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );
	}

	/**
	 * Test that posts from a single post status (draft posts) can be permanently deleted.
	 */
	public function test_that_draft_posts_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'draft',
		) );

		$draft_posts = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( 10, count( $draft_posts ) );

		$delete_options = array(
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$draft_posts = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );
	}

	/**
	 * Test that private posts can be trashed.
	 */
	public function test_that_private_posts_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'private',
		) );

		$private_posts = $this->get_posts_by_status( 'private' );
		$this->assertEquals( 10, count( $private_posts ) );

		$delete_options = array(
			'post_status'  => 'private',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$private_posts = $this->get_posts_by_status( 'private' );
		$this->assertEquals( 0, count( $private_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );
	}

	/**
	 * Test date filter (older than x days) with a single post status (published posts).
	 */
	public function test_that_published_posts_that_are_older_than_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$published_posts = $this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_posts ) );
	}

	/**
	 * Test date filter (older than x days) with a single post status (draft posts).
	 */
	public function test_that_draft_posts_that_are_older_than_x_days_post_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$draft_posts = $this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'draft',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $draft_posts ) );

		$delete_options = array(
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$draft_posts = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_posts ) );
	}

	/**
	 * Test date filter (within the last x days) with a single post status (published posts).
	 */
	public function test_that_published_posts_that_are_posted_within_the_last_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$published_posts = $this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_posts ) );

		$delete_options = array(
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_posts ) );
	}

	/**
	 * Test date filter (within the last x days) with a single post status (draft posts).
	 */
	public function test_that_draft_posts_that_are_posted_within_the_last_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$draft_posts = $this->factory->post->create_many( 10, array(
			'post_type'   => 'post',
			'post_status' => 'draft',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $draft_posts ) );

		$delete_options = array(
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$draft_posts = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_posts ) );
	}

	/**
	 * Test batch deletion with a single post status (published posts).
	 */
	public function test_that_published_posts_can_be_deleted_in_batches() {
		$published_posts = $this->factory->post->create_many( 100, array(
			'post_type'   => 'post',
			'post_status' => 'publish',
		) );

		$this->assertEquals( 100, count( $published_posts ) );

		$delete_options = array(
			'post_status'  => 'publish',
			'limit_to'     => 50,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $posts_deleted );

		$published_posts = $this->get_posts_by_status();
		$this->assertEquals( 50, count( $published_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_posts ) );
	}

	/**
	 * Test batch deletion with a single post status (draft posts).
	 */
	public function test_draft_posts_that_delete_in_batches_can_be_deleted() {
		$draft_posts = $this->factory->post->create_many( 100, array(
			'post_type'   => 'post',
			'post_status' => 'draft',
		) );

		$this->assertEquals( 100, count( $draft_posts ) );

		$delete_options = array(
			'post_status'  => 'draft',
			'limit_to'     => 50,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $posts_deleted );

		$draft_posts = $this->get_posts_by_status( 'draft' );
		$this->assertEquals( 50, count( $draft_posts ) );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_posts ) );
	}
}
