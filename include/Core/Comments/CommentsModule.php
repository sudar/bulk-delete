<?php

namespace BulkWP\BulkDelete\Core\Comments;

use BulkWP\BulkDelete\Core\Base\BaseModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Module for deleting comments.
 *
 * @since 6.1.0
 */
abstract class CommentsModule extends BaseModule {
	protected $item_type = 'comments';

	/**
	 * Build query params for WP_Comment_Query by using delete options.
	 *
	 * Return an empty query array to short-circuit deletion.
	 *
	 * @param array $options Delete options.
	 *
	 * @return array Query.
	 */
	abstract protected function build_query( $options );

	protected function render_restrict_settings( $item = 'comments' ) {
		bd_render_restrict_settings( $this->field_slug, $item );
	}

	/**
	 * Handle common filters.
	 *
	 * @param array $request Request array.
	 *
	 * @return array User options.
	 */
	protected function parse_common_filters( $request ) {
		$options = array();

		$options['restrict']     = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_restrict', false );
		$options['limit_to']     = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_limit_to', 0 ) );
		$options['force_delete'] = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_force_delete', false );

		if ( $options['restrict'] ) {
			$options['date_op'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_op' );
			$options['days']    = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_days' ) );
		}

		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function do_delete( $options ) {
		$query = $this->build_query( $options );

		if ( empty( $query ) ) {
			// Short circuit deletion, if nothing needs to be deleted.
			return 0;
		}

		return $this->delete_comments_from_query( $query, $options );
	}

	/**
	 * Query and Delete comments.
	 *
	 * @access protected
	 *
	 * @param array $query   Options to query comments.
	 * @param array $options Delete options.
	 *
	 * @return int Number of comments deleted.
	 */
	protected function delete_comments_from_query( $query, $options ) {
		$comments = $this->query_comments( $query );

		$deleted_comments_count = $this->delete_comments_by_id( $comments, $options['force_delete'] );

		return $deleted_comments_count;
	}

	/**
	 * Query comments using options.
	 *
	 * @param array $options Query options.
	 *
	 * @return array List of comment IDs.
	 */
	protected function query_comments( $options ) {
		$defaults = array(
			'update_comment_meta_cache' => false,
			'fields'                    => 'ids',
		);

		$options = wp_parse_args( $options, $defaults );

		$wp_comment_query = new \WP_Comment_Query();

		/**
		 * This action before the query happens.
		 *
		 * @since 6.1.0
		 *
		 * @param \WP_Comment_Query $wp_comment_query Query object.
		 */
		do_action( 'bd_before_query', $wp_comment_query );

		$comments = (array) $wp_comment_query->query( $options );

		/**
		 * This action runs after the query happens.
		 *
		 * @since 6.1.0
		 *
		 * @param \WP_Comment_Query $wp_comment_query Query object.
		 */
		do_action( 'bd_after_query', $wp_comment_query );

		return $comments;
	}

	/**
	 * Delete comments by ids.
	 *
	 * @param int[] $comment_ids  List of comment ids to delete.
	 * @param bool  $force_delete True to force delete comments, False otherwise.
	 *
	 * @return int Number of comments deleted.
	 */
	protected function delete_comments_by_id( $comment_ids, $force_delete ) {
		$count = 0;

		if ( ! function_exists( 'wp_delete_comment' ) ) {
			require_once ABSPATH . 'wp-admin/includes/comment.php';
		}

		foreach ( $comment_ids as $comment_id ) {
			$deleted = wp_delete_comment( $comment_id, $force_delete );

			if ( $deleted ) {
				$count ++;
			}
		}

		return $count;
	}

	/**
	 * Get the date query part for WP_Comment_Query.
	 *
	 * Date query corresponds to comment date.
	 *
	 * @param array $options Delete options.
	 *
	 * @return array Date Query.
	 */
	protected function get_date_query( $options ) {
		if ( ! $options['restrict'] ) {
			return array();
		}

		if ( $options['days'] <= 0 ) {
			return array();
		}

		if ( 'before' === $options['date_op'] || 'after' === $options['date_op'] ) {
			return array(
				$options['date_op'] => $options['days'] . ' days ago',
			);
		}

		return array();
	}
}
