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
			'revisions'    => 'revisions',
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => true,
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
			10,
			array(
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
			'limit_to'     => 0,
			'restrict'     => false,
			'force_delete' => true,
		);

		$posts_deleted = $this->module->delete( $delete_options );
		$this->assertEquals( 10, $posts_deleted );

		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			$this->assertEquals( $post->ID, $post_id );
		}
	}

	/**
	 * Data provider to test revisions can be deleted with date filter.
	 */
	public function provide_data_to_test_revisions_deletion_with_date_filter() {
		return array(
			// (+ve Case) Deletes all/few revisions that are older than x days.
			array(
				array(
					array(
						'no_of_posts' => 10,
						'post_args'   => array(
							'post_type'   => 'revision',
							'post_status' => 'inherit',
							'post_date'   => date( 'Y-m-d H:i:s', strtotime( '-4 day' ) ),
						),
					),
					array(
						'no_of_posts' => 5,
						'post_args'   => array(
							'post_type'   => 'revision',
							'post_status' => 'inherit',
							'post_date'   => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
						),
					),
				),
				array(
					'revisions'    => 'revisions',
					'limit_to'     => 0,
					'restrict'     => true,
					'date_op'      => 'before',
					'days'         => 2,
					'force_delete' => true,
				),
				array(
					'deleted_posts'   => 10,
					'available_posts' => 5,
				),
			),
			// (+ve Case) Deletes all/few revisions in the last x days.
			array(
				array(
					array(
						'no_of_posts' => 10,
						'post_args'   => array(
							'post_type'   => 'revision',
							'post_status' => 'inherit',
							'post_date'   => date( 'Y-m-d H:i:s', strtotime( '-4 day' ) ),
						),
					),
					array(
						'no_of_posts' => 5,
						'post_args'   => array(
							'post_type'   => 'revision',
							'post_status' => 'inherit',
							'post_date'   => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
						),
					),
				),
				array(
					'revisions'    => 'revisions',
					'limit_to'     => 0,
					'restrict'     => true,
					'date_op'      => 'after',
					'days'         => 2,
					'force_delete' => true,
				),
				array(
					'deleted_posts'   => 5,
					'available_posts' => 10,
				),
			),
		);
	}

	/**
	 * Data provider to test revisions can be deleted with limit filter.
	 */
	public function provide_data_to_test_revisions_deletion_with_limit_filter() {
		return array(
			// (+ve Case) Deletes all/few revisions with limit filter.
			array(
				array(
					array(
						'no_of_posts' => 10,
						'post_args'   => array(
							'post_type'   => 'revision',
							'post_status' => 'inherit',
							'post_date'   => date( 'Y-m-d H:i:s', strtotime( '-4 day' ) ),
						),
					),
					array(
						'no_of_posts' => 5,
						'post_args'   => array(
							'post_type'   => 'revision',
							'post_status' => 'inherit',
							'post_date'   => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
						),
					),
					array(
						'no_of_posts' => 20,
						'post_args'   => array(
							'post_type'   => 'revision',
							'post_status' => 'inherit',
						),
					),
				),
				array(
					'revisions'    => 'revisions',
					'limit_to'     => 15,
					'restrict'     => false,
					'force_delete' => true,
				),
				array(
					'deleted_posts'   => 15,
					'available_posts' => 20,
				),
			),
		);
	}

	/**
	 * Test deletion of revisions with date and limit filters.
	 *
	 * @param array $setup     Create revisions.
	 * @param array $operation Possible operations.
	 * @param array $expected  Expected output.
	 *
	 * @dataProvider provide_data_to_test_revisions_deletion_with_date_filter
	 * @dataProvider provide_data_to_test_revisions_deletion_with_limit_filter
	 */
	public function test_that_post_revisions_can_be_deleted( $setup, $operation, $expected ) {
		foreach ( $setup as $element ) {
			$this->factory->post->create_many( $element['no_of_posts'], $element['post_args'] );
		}

		$posts_deleted = $this->module->delete( $operation );
		$this->assertEquals( $expected['deleted_posts'], $posts_deleted );

		$available_posts = $this->get_posts_by_status( 'inherit', 'revision' );
		$this->assertEquals( $expected['available_posts'], count( $available_posts ) );
	}

}
