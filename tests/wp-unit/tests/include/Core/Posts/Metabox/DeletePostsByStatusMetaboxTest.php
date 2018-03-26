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

	public function test_that_published_posts_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'page',
		) );

		$published_page = $this->get_posts_by_status();
		$this->assertEquals( 10, count( $published_page ) );

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

		$published_page = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_page ) );

		$trash_page = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_page ) );
	}

	public function test_that_published_posts_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'page',
		) );

		$published_page = $this->get_posts_by_status();
		$this->assertEquals( 10, count( $published_page ) );

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

		$published_page = $this->get_posts_by_status();
		$this->assertEquals( 0, count( $published_page ) );

		$trash_page = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_page ) );
	}
}
