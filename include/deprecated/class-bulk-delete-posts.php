<?php

use BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByCategoryMetabox;
use BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByCustomTaxonomyMetabox;
use BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByPostTypeMetabox;
use BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByStatusMetabox;
use BulkWP\BulkDelete\Core\Posts\Metabox\DeletePostsByTagMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Utility class for deleting posts.
 *
 * All the methods from this class has been migrated to individual metabox module classes.
 * This class is still present for backward compatibility purpose, since some of the old add-ons still depend on this class.
 *
 * @since 6.0.0 Deprecated.
 */
class Bulk_Delete_Posts {
	/**
	 * Delete posts by post status - drafts, pending posts, scheduled posts etc.
	 *
	 * @since  5.0
	 * @since 6.0.0 Deprecated.
	 * @static
	 *
	 * @param array $delete_options Options for deleting posts.
	 *
	 * @return int $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_status( $delete_options ) {
		$metabox = new DeletePostsByStatusMetabox();

		return $metabox->delete( $delete_options );
	}

	/**
	 * Delete posts by category.
	 *
	 * @since  5.0
	 * @since 6.0.0 Deprecated.
	 * @static
	 *
	 * @param array $delete_options Options for deleting posts.
	 *
	 * @return int $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_category( $delete_options ) {
		$metabox = new DeletePostsByCategoryMetabox();

		return $metabox->delete( $delete_options );
	}

	/**
	 * Delete posts by tag.
	 *
	 * @since  5.0
	 * @since 6.0.0 Deprecated.
	 * @static
	 *
	 * @param array $delete_options Options for deleting posts.
	 *
	 * @return int $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_tag( $delete_options ) {
		$metabox = new DeletePostsByTagMetabox();

		return $metabox->delete( $delete_options );
	}

	/**
	 * Delete posts by taxonomy.
	 *
	 * @since  5.0
	 * @since 6.0.0 Deprecated.
	 * @static
	 *
	 * @param array $delete_options Options for deleting posts.
	 *
	 * @return int $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_taxonomy( $delete_options ) {
		$metabox = new DeletePostsByCustomTaxonomyMetabox();

		return $metabox->delete( $delete_options );
	}

	/**
	 * Delete posts by post type.
	 *
	 * @since  5.0
	 * @since 6.0.0 Deprecated.
	 * @static
	 *
	 * @param array $delete_options Options for deleting posts.
	 *
	 * @return int $posts_deleted  Number of posts that were deleted
	 */
	public static function delete_posts_by_post_type( $delete_options ) {
		$metabox = new DeletePostsByPostTypeMetabox();

		return $metabox->delete( $delete_options );
	}
}
