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

	/**
	 * Setup the module.
	 */
	public function setUp() {
		parent::setUp();

		$this->module = new DeletePostsByRevisionModule();
	}

	/**
	 * Test trash revisions for a single post
	 */
	public function test_trash_revisions_for_single_post() {

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
	}

	/**
	 * Test delete revisions for a single post
	 */
	public function test_delete_revisions_for_single_post() {

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
			'revisions'    => 'revisions',
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 2, $posts_deleted );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );
	}

	/**
	 * Test trash revisions for a multiple post
	 */
	public function test_trash_revisions_for_multiple_post() {

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

	}

	/**
	 * Test delete revisions for a multiple post
	 */
	public function test_delete_revisions_for_multiple_post() {

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
			'revisions'    => 'revisions',
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		$trash_posts = $this->get_posts_by_status( 'trash' );
		$this->assertEquals( 0, count( $trash_posts ) );

	}

}
