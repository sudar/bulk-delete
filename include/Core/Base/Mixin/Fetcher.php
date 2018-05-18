<?php

namespace BulkWP\BulkDelete\Core\Base\Mixin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Container of all Fetch related methods.
 *
 * Ideally this should be a Trait. Since Bulk Delete still supports PHP 5.3, this is implemented as a class.
 * Once the minimum requirement is increased to PHP 5.3, this will be changed into a Trait.
 *
 * @since 6.0.0
 */
abstract class Fetcher {
	/**
	 * Get the list of public post types registered in WordPress.
	 *
	 * @return \WP_Post_Type[]
	 */
	protected function get_post_types() {
		return bd_get_post_types();
	}

	/**
	 * Get the list of post statuses.
	 *
	 * This includes all custom post status, but excludes built-in private posts.
	 *
	 * @return array List of post status objects.
	 */
	protected function get_post_statuses() {
		return bd_get_post_statuses();
	}

	/**
	 * Get the list of post types by post status and count.
	 *
	 * @return array Post types by post status.
	 */
	protected function get_post_types_by_status() {
		$post_types_by_status = array();

		$post_types    = $this->get_post_types();
		$post_statuses = $this->get_post_statuses();

		foreach ( $post_types as $post_type ) {
			$post_type_name = $post_type->name;
			$count_posts    = wp_count_posts( $post_type_name );

			foreach ( $post_statuses as $post_status ) {
				$post_status_name = $post_status->name;

				if ( ! property_exists( $count_posts, $post_status_name ) ) {
					continue;
				}

				if ( 0 === $count_posts->{$post_status_name} ) {
					continue;
				}

				$post_types_by_status[$post_type->labels->singular_name][ "$post_type_name-$post_status_name" ] = $post_status->label . ' (' . $count_posts->{$post_status_name} . ' ' . __( 'Posts', 'bulk-delete' ) . ')';
			}
		}

		return $post_types_by_status;
	}
}
