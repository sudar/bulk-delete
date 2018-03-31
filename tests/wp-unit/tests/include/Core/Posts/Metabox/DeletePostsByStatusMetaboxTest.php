<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Posts by status.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByStatusMetabox
 *
 * @since 6.0.0
 */
class DeletePostsByStatusMetaboxTest extends WPCoreUnitTestCase {

	/**
	 * The metabox that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByStatusMetabox
	 */
	protected $metabox;

	public function setUp() {
		parent::setUp();

		$this->metabox = new DeletePostsByStatusMetabox();
	}

	/**
	 * Test Published posts can be trashed.
	 *
	 * @since 6.0.0
	 *
	 */
	public function test_that_published_posts_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 10, count( $published_post ) );

		$delete_options = array(
			'publish'      => 'published_posts',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_post ) );

		$trash_post = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_post ) );
	}

	/**
	 * Test Published posts can be deleted.
	 *
	 * @since 6.0.0
	 *
	 */
	public function test_that_published_posts_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
		) );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 10, count( $published_post ) );

		$delete_options = array(
			'publish'      => 'published_posts',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_post ) );

		$trash_post = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_post ) );
	}

	/**
	 * Test Private posts can be trashed.
	 *
	 * @since 6.0.0
	 *
	 */
	public function test_that_private_post_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'post',
			'post_status' => 'private',
		) );

		$published_post = $this->get_posts_by_status( 'private' );
		$this->assertEquals( 10, count( $published_post ) );

		$delete_options = array(
			'post_status'  => 'private',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_post ) );

		$trash_post = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_post ) );
	}

	/**
	 * Test that are older than x days post can be deleted.
	 *
	 * @since 6.0.0
	 *
	 */
	public function test_that_are_older_than_x_days_post_can_be_deleted() {
		$date = date("Y-m-d H:i:s", strtotime("-5 day") );

		$published_post = $this->factory->post->create_many( 10, array(
			'post_type' => 'post',
			'post_date' => $date,
		) );

		$this->assertEquals( 10, count( $published_post ) );

		$delete_options = array(
			'publish'      => 'published_posts',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'before',
			'days'         => '3',
		);

		$posts_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_post ) );

		$trash_post = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_post ) );
		
	}

	/**
	 * Test that posted within the last x day post can be deleted.
	 *
	 * @since 6.0.0
	 *
	 */
	public function test_that_posted_within_the_last_x_day_post_can_be_deleted() {
		$date = date("Y-m-d H:i:s", strtotime("-3 day") );

		$published_post = $this->factory->post->create_many( 10, array(
			'post_type' => 'post',
			'post_date' => $date,
		) );

		$this->assertEquals( 10, count( $published_post ) );

		$delete_options = array(
			'publish'      => 'published_posts',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => true,
			'force_delete' => false,
			'date_op'      => 'after',
			'days'         => '5',
		);

		$posts_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_post ) );

		$trash_post = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_post ) );
		
	}

	/**
	 * Test that try to delete them in batches can be deleted.
	 *
	 * @since 6.0.0
	 *
	 */
	public function test_that_try_to_delete_them_in_batches_can_be_deleted() {

		$published_post = $this->factory->post->create_many( 100, array(
			'post_type' => 'post',
		) );

		$this->assertEquals( 100, count( $published_post ) );

		$delete_options = array(
			'publish'      => 'published_posts',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => 50,
			'restrict'     => false,
			'force_delete' => false,
		);

		$posts_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 50, $posts_deleted );

		$published_post = $this->get_posts_by_status();
		$this->assertEquals( 50, count( $published_post ) );

		$trash_post = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 50, count( $trash_post ) );
		
	}
}
