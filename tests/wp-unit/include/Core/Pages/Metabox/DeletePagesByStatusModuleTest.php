<?php

namespace BulkWP\BulkDelete\Core\Pages\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Pages by status.
 *
 * Tests \BulkWP\BulkDelete\Core\Pages\Modules\DeletePagesByStatusModule
 *
 * @since 6.0.0
 */
class DeletePagesByStatusModuleTest extends WPCoreUnitTestCase {

	/**
	 * The metabox that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Pages\Modules\DeletePagesByStatusModule
	 */
	protected $metabox;

	public function setUp() {
		parent::setUp();

		$this->metabox = new DeletePagesByStatusModule();
	}

	/**
	 * Test that pages from two post status can be trashed.
	 */
	public function test_that_pages_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'page',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
		) );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 20, count( $trash_pages ) );
	}

	/**
	 * Test that pages from two post status can be permanently deleted.
	 */
	public function test_that_pages_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
		) );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );
	}

	/**
	 * Test date filter (older than x days) with two post status.
	 */
	public function test_that_pages_that_are_older_than_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-5 day' ) );

		$published_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_pages ) );

		$draft_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 20, count( $trash_pages ) );
	}

	/**
	 * Test date filter (within the last x days) with two post status.
	 */
	public function test_that_pages_that_are_posted_within_the_last_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-3 day' ) );

		$published_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $published_pages ) );

		$draft_pages = $this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
			'post_date'   => $date,
		) );

		$this->assertEquals( 10, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 20, count( $trash_pages ) );
	}

	/**
	 * Test batch deletion with two post status.
	 */
	public function test_that_pages_can_be_deleted_in_batches() {
		$published_pages = $this->factory->post->create_many( 50, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$this->assertEquals( 50, count( $published_pages ) );

		$draft_pages = $this->factory->post->create_many( 50, array(
			'post_type'   => 'page',
			'post_status' => 'draft',
		) );

		$this->assertEquals( 50, count( $draft_pages ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => 'draft_pages',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => 80,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 80, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$draft_pages     = $this->get_pages_by_status( 'draft' );

		$this->assertEquals( 20, count( $published_pages ) + count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 80, count( $trash_pages ) );
	}

	/**
	 * Test that private pages can be permanently deleted.
	 */
	public function test_that_private_pages_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'private',
		) );

		$private_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 10, count( $private_pages ) );

		$delete_options = array(
			'publish'      => '',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => 'private_pages',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );
	}

}
