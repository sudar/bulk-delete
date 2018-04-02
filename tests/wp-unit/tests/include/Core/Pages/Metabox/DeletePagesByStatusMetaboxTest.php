<?php

namespace BulkWP\BulkDelete\Core\Pages\Metabox;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Pages by status.
 *
 * Tests \BulkWP\BulkDelete\Core\Pages\Metabox\DeletePagesByStatusMetabox
 *
 * @since 6.0.0
 */
class DeletePagesByStatusMetaboxTest extends WPCoreUnitTestCase {

	/**
	 * The metabox that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Pages\Metabox\DeletePagesByStatusMetabox
	 */
	protected $metabox;

	public function setUp() {
		parent::setUp();

		$this->metabox = new DeletePagesByStatusMetabox();
	}

	/**
	 * Test that pages from a single post status (published pages) can be trashed.
	 */
	public function test_that_published_pages_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'page',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_pages ) );
	}

	/**
	 * Test that pages from a single post status (draft pages) can be trashed.
	 */
	public function test_that_draft_pages_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
		) );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => '',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_pages ) );
	}

	/**
	 * Test that pages from a single post status (published pages) can be permanently deleted.
	 */
	public function test_that_published_pages_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );
	}

	/**
	 * Test that pages from a single post status (draft pages) can be permanently deleted.
	 */
	public function test_that_draft_pages_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
		) );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => '',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );
	}

	/**
	 * Test date filter (older than x days) with a single post status (published pages).
	 */
	public function test_that_published_pages_that_are_older_than_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$published_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_pages ) );
	}

	/**
	 * Test date filter (older than x days) with a single post status (draft pages).
	 */
	public function test_that_draft_pages_that_are_older_than_x_days_post_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$draft_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => '',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_pages ) );
	}

	/**
	 * Test date filter (within the last x days) with a single post status (published pages).
	 */
	public function test_that_published_pages_that_are_posted_within_the_last_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$published_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'publish',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_pages ) );
	}

	/**
	 * Test date filter (within the last x days) with a single post status (draft pages).
	 */
	public function test_that_draft_pages_that_are_posted_within_the_last_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$draft_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => '',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'draft',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_pages ) );
	}

	/**
	 * Test batch deletion with a single post status (published pages).
	 */
	public function test_that_published_pages_can_be_deleted_in_batches() {
		$published_pages = $this->factory->post->create_many( 100, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$this->assertEquals( 100, count( $published_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'publish',
			'limit_to'     => 50,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 50, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_pages ) );
	}

	/**
	 * Test batch deletion with a single post status (draft pages).
	 */
	public function test_draft_pages_that_delete_in_batches_can_be_deleted() {
		$draft_pages = $this->factory->post->create_many( 100, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
		) );

		$this->assertEquals( 100, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => '',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'post_status'  => 'draft',
			'limit_to'     => 50,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 50, count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_pages ) );
	}
}
