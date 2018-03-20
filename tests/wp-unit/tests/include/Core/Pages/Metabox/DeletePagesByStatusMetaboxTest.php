<?php

namespace BulkWP\BulkDelete\Core\Pages\Metabox;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of pages by status.
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

	public function test_that_published_pages_can_be_trashed() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'page',
		) );

		$published_page = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_page ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => false,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_page = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_page ) );

		$trash_page = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 10, count( $trash_page ) );
	}

	public function test_that_published_pages_can_be_deleted() {
		$this->factory->post->create_many( 10, array(
			'post_type' => 'page',
		) );

		$published_page = $this->get_pages_by_status();
		$this->assertEquals( 10, count( $published_page ) );

		$delete_options = array(
			'publish'      => 'published_pages',
			'drafts'       => '',
			'pending'      => '',
			'future'       => '',
			'private'      => '',
			'limit_to'     => -1,
			'restrict'     => false,
			'force_delete' => true,
		);

		$pages_deleted = $this->metabox->delete( $delete_options );
		$this->assertEquals( 10, $pages_deleted );

		$published_page = $this->get_pages_by_status();
		$this->assertEquals( 0, count( $published_page ) );

		$trash_page = $this->get_pages_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_page ) );
	}
}
