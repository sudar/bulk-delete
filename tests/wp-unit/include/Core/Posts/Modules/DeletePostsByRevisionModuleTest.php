<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\Tests\WPCore\WPCoreUnitTestCase;

/**
 * Test Deletion of Posts by revisions.
 *
 * Tests \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByRevisionModule
 *
 * @since 6.0.0
 */
class DeletePostsByRevisionModuleTest extends WPCoreUnitTestCase {
	/**
	 * The module that is getting tested.
	 *
	 * @var \BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByRevisionModule
	 */
	protected $module;

	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByRevisionModule();
	}

	/**
	 * Test thet post revisions can be deleted form a single post.
	 */
	public function test_that_post_revisions_can_be_deleted_from_single_post() {
		$post_id = $this->factory->post->create(
			array(
				'post_type' => 'post',
			)
		);

		$revision_post_1 = array(
			'ID'           => $post_id,
			'post_title'   => rand(),
			'post_content' => md5( rand() ),
		);

		wp_update_post( $revision_post_1 );

		$revision_post_2 = array(
			'ID'           => $post_id,
			'post_title'   => rand(),
			'post_content' => md5( rand() ),
		);

		wp_update_post( $revision_post_2 );

		$delete_options = array(
			'revisions' => 'revisions',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 2, $posts_deleted );

		$post = get_post( $post_id );
		$this->assertEquals( $post->ID, $post_id );
	}

	/**
	 * Test thet post revisions can be deleted form multiple post.
	 */
	public function test_that_post_revisions_can_be_deleted_from_multiple_post() {
		$post_ids = $this->factory->post->create_many(
			10, array(
				'post_type' => 'post',
			)
		);

		foreach ( $post_ids as $post_id ) {
			$revision_post = array(
				'ID'           => $post_id,
				'post_title'   => rand(),
				'post_content' => md5( rand() ),
			);

			wp_update_post( $revision_post );
		}

		$delete_options = array(
			'revisions' => 'revisions',
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			$this->assertEquals( $post->ID, $post_id );
		}
	}
}
