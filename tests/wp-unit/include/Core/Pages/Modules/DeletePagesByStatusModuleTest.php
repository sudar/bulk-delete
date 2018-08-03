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
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Pages\Modules\DeletePagesByStatusModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePagesByStatusModule();
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
			'post_status'  => array( 'publish', 'draft' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->module->delete( $delete_options );
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
			'post_status'  => array( 'publish', 'draft' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );
	}

	/**
	 * Test date filter (older than x days) with two post status.
	 */
	public function test_that_pages_that_are_older_than_x_days_can_be_trashed() {
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
			'post_status'  => array( 'publish', 'draft' ),
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 20, count( $trash_pages ) );
	}

	/**
	 * Test date filter (within the last x days) with two post status.
	 */
	public function test_that_pages_that_are_posted_within_the_last_x_days_can_be_trashed() {
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
			'post_status'  => array( 'publish', 'draft' ),
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 20, count( $trash_pages ) );
	}

	/**
	 * Test date filter (within the last x days) with two post status
	 * can be permanently deleted.
	 */
	public function test_that_pages_that_are_posted_within_the_last_x_days_can_be_deleted() {
		$date = date( 'Y-m-d H:i:s', strtotime( '-4 day' ) );

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
			'post_status'  => array( 'publish', 'draft' ),
			'limit_to'     => 0,
			'restrict'     => true,
			'force_delete' => true,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 20, $pages_deleted );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$draft_pages = $this->get_pages_by_status( 'draft' );
		$this->assertEquals( 0, count( $draft_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );
	}

	/**
	 * Test batch deletion with two post status.
	 */
	public function test_that_pages_can_be_trashed_in_batches() {
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
			'post_status'  => array( 'publish', 'draft' ),
			'limit_to'     => 80,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->module->delete( $delete_options );
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
			'post_status'  => array( 'private' ),
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );
	}

	/**
	 * Test private pages can be trashed
	 */
	public function test_that_private_pages_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'private',
		) );

		$private_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 10, count( $private_pages ) );

		$delete_options = array(
			'post_status'  => array( 'private' ),
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_pages ) );
	}

	/**
	 * Test delete pages of single custom post status
	 */
	public function test_that_pages_from_single_custom_post_status_can_be_deleted() {
		register_post_status( 'custom' );
		$this->factory->post->create_many( 50, array(
			'post_type'   => 'page',
			'post_status' => 'custom',
		) );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 50, count( $custom_pages ) );

		register_post_status( 'keep_me' );
		$this->factory->post->create_many( 20, array(
			'post_type'   => 'page',
			'post_status' => 'keep_me',
		) );

		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'post_status'  => array( 'custom' ),
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 0, count( $custom_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );

		// Make sure other custom post status pages are not deleted.
		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		// Make sure other built-in post status pages are not deleted.
		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

	}

	/**
	 * Test pages of single custom post status can be trashed
	 */
	public function test_that_pages_from_single_custom_post_status_can_be_trashed() {
		register_post_status( 'custom' );
		$this->factory->post->create_many( 50, array(
			'post_type'   => 'page',
			'post_status' => 'custom',
		) );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 50, count( $custom_pages ) );

		register_post_status( 'keep_me' );
		$this->factory->post->create_many( 20, array(
			'post_type'   => 'page',
			'post_status' => 'keep_me',
		) );

		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'post_status'  => array( 'custom' ),
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 0, count( $custom_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_pages ) );

		// Make sure other custom post status pages are not deleted/moved to trash.
		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		// Make sure other built-in post status pages are not deleted/moved to trash.
		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );
	}

	/**
	 * Test delete pages of two custom post statuses
	 */
	public function test_that_pages_from_two_custom_post_statuses_can_be_deleted() {
		register_post_status( 'custom_1' );
		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'custom_1',
		) );

		$custom_1_pages = $this->get_pages_by_status( 'custom_1' );
		$this->assertEquals( 25, count( $custom_1_pages ) );

		register_post_status( 'custom_2' );
		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'custom_2',
		) );

		$custom_2_pages = $this->get_pages_by_status( 'custom_2' );
		$this->assertEquals( 25, count( $custom_2_pages ) );

		register_post_status( 'keep_me' );
		$this->factory->post->create_many( 20, array(
			'post_type'   => 'page',
			'post_status' => 'keep_me',
		) );

		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'post_status'  => array( 'custom_1', 'custom_2' ),
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$custom_1_pages = $this->get_pages_by_status( 'custom_1' );
		$this->assertEquals( 0, count( $custom_1_pages ) );

		$custom_2_pages = $this->get_pages_by_status( 'custom_2' );
		$this->assertEquals( 0, count( $custom_2_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );

		// Make sure other custom post status pages are not deleted/moved to trash.
		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		// Make sure other built-in post status pages are not deleted/moved to trash.
		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

	}

	/**
	 * Test pages of two custom post statuses can be trashed
	 */
	public function test_that_pages_from_two_custom_post_statuses_can_be_trashed() {
		register_post_status( 'custom_1' );
		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'custom_1',
		) );

		$custom_1_pages = $this->get_pages_by_status( 'custom_1' );
		$this->assertEquals( 25, count( $custom_1_pages ) );

		register_post_status( 'custom_2' );
		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'custom_2',
		) );

		$custom_2_pages = $this->get_pages_by_status( 'custom_2' );
		$this->assertEquals( 25, count( $custom_2_pages ) );

		register_post_status( 'keep_me' );
		$this->factory->post->create_many( 20, array(
			'post_type'   => 'page',
			'post_status' => 'keep_me',
		) );

		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

		$delete_options = array(
			'post_status'  => array( 'custom_1', 'custom_2' ),
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$custom_1_pages = $this->get_pages_by_status( 'custom_1' );
		$this->assertEquals( 0, count( $custom_1_pages ) );

		$custom_2_pages = $this->get_pages_by_status( 'custom_2' );
		$this->assertEquals( 0, count( $custom_2_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_pages ) );

		// Make sure other custom post status pages are not deleted/moved to trash.
		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		// Make sure other built-in post status pages are not deleted/moved to trash.
		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_pages ) );

	}

	/**
	 * Test delete pages of one custom post status and one built-in post status
	 */
	public function test_pages_from_custom_and_built_in_post_status_can_be_deleted_together() {
		register_post_status( 'custom' );
		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'custom',
		) );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 25, count( $custom_pages ) );

		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status( 'publish' );
		$this->assertEquals( 25, count( $published_pages ) );

		register_post_status( 'keep_me' );
		$this->factory->post->create_many( 20, array(
			'post_type'   => 'page',
			'post_status' => 'keep_me',
		) );

		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'private',
		) );

		$private_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 10, count( $private_pages ) );

		$delete_options = array(
			'post_status'  => array( 'custom', 'publish' ),
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 0, count( $custom_pages ) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_pages ) );

		// Make sure other custom post status pages are not deleted/moved to trash.
		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		// Make sure other built-in post status pages are not deleted/moved to trash.
		$private_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 10, count( $private_pages ) );
	}

	/**
	 * Test pages of one custom post status and one built-in post status can be trashed
	 */
	public function test_pages_from_custom_and_built_in_post_status_can_be_trashed_together() {
		register_post_status( 'custom' );
		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'custom',
		) );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 25, count( $custom_pages ) );

		$this->factory->post->create_many( 25, array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 25, count( $published_pages ) );

		register_post_status( 'keep_me' );
		$this->factory->post->create_many( 20, array(
			'post_type'   => 'page',
			'post_status' => 'keep_me',
		) );

		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		$this->factory->post->create_many( 10, array(
			'post_type'   => 'page',
			'post_status' => 'private',
		) );

		$private_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 10, count( $private_pages ) );

		$delete_options = array(
			'post_status'  => array( 'custom', 'publish' ),
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 50, $pages_deleted );

		$custom_pages = $this->get_pages_by_status( 'custom' );
		$this->assertEquals( 0, count( $custom_pages ) );

		$published_pages = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_pages ) );

		$trash_pages = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_pages ) );

		// Make sure other custom post status pages are not deleted/moved to trash.
		$keep_me_pages = $this->get_pages_by_status( 'keep_me' );
		$this->assertEquals( 20, count( $keep_me_pages ) );

		// Make sure other built-in post status pages are not deleted/moved to trash.
		$private_pages = $this->get_pages_by_status( 'private' );
		$this->assertEquals( 10, count( $private_pages ) );
	}
}
