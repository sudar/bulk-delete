<?php
namespace BulkWP\BulkDelete\Core\Terms;

use BulkWP\BulkDelete\Core\Base\BaseModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Module for deleting terms.
 *
 * @since 6.0.0
 */
abstract class TermsModule extends BaseModule {
	/**
	 * Build query params for WP_Term_Query by using delete options.
	 *
	 * Return an empty query array to short-circuit deletion.
	 *
	 * @param array $options Delete options.
	 *
	 * @return array Query.
	 */
	abstract protected function build_query( $options );

	/**
	 * Item type.
	 *
	 * @var string Item Type. Possible values 'posts', 'pages', 'users', 'terms' etc.
	 */
	protected $item_type = 'terms';

	/**
	 * Handle common filters.
	 *
	 * @param array $request Request array.
	 *
	 * @return array User options.
	 */
	protected function parse_common_filters( $request ) {
		$options = array();

		$options['taxonomy'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_taxonomy' );

		return $options;
	}

	/**
	 * Perform the deletion.
	 *
	 * @param array $options Array of Delete options.
	 *
	 * @return int Number of items that were deleted.
	 */
	protected function do_delete( $options ) {
		$query = $this->build_query( $options );

		if ( empty( $query ) ) {
			// Short circuit deletion, if nothing needs to be deleted.
			return 0;
		}

		$query['taxonomy'] = $options['taxonomy'];

		return $this->delete_terms_from_query( $query, $options );
	}

	/**
	 * Build the query using query params and then Delete terms.
	 *
	 * @param array $query   Params for WP Term Query.
	 * @param array $options Delete Options.
	 *
	 * @return int Number of terms deleted.
	 */
	protected function delete_terms_from_query( $query, $options ) {
		$term_ids = $this->query_terms( $query );

		return $this->delete_terms_by_id( $term_ids, $options );
	}

	/**
	 * Delete terms by ids.
	 *
	 * @param int[] $term_ids List of term ids to delete.
	 * @param array $options  User options.
	 *
	 * @return int Number of terms deleted.
	 */
	protected function delete_terms_by_id( $term_ids, $options ) {
		$count = 0;

		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id, $options['taxonomy'] );

			if ( ! $term instanceof \WP_Term ) {
				continue;
			}

			if ( isset( $options['no_posts'] ) && $term->count > 0 ) {
				continue;
			}

			$deleted = wp_delete_term( $term_id, $options['taxonomy'] );

			if ( $deleted ) {
				$count ++;
			}
		}

		return $count;
	}

	/**
	 * Custom string function use to get is string start with specified string.
	 *
	 * @param string $haystack search string.
	 * @param string $needle   find string.
	 *
	 * @return boolean.
	 */
	protected function bd_starts_with( $haystack, $needle ) {
		$length = strlen( $needle );

		return ( substr( $haystack, 0, $length ) === $needle );
	}

	/**
	 * Custom string function use to get is string ends with specified string.
	 *
	 * @param string $haystack search string.
	 * @param string $needle   find string.
	 *
	 * @return boolean.
	 */
	protected function bd_ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		$substr = substr( $haystack, -$length );
		$zero   = 0;

		return $length === $zero ||
		( $substr === $needle );
	}

	/**
	 * Get term ids which is have the sepcified post count .
	 *
	 * @param array $options user options.
	 *
	 * @return array term ids.
	 */
	protected function term_count_query( $options ) {
		$term_ids = array();
		$terms    = get_terms(
			$options['taxonomy'], array(
				'hide_empty' => false,
			)
		);
		foreach ( $terms as $term ) {
			$args = array(
				'post_type' => $options['post_type'],
				'tax_query' => array(
					array(
						'taxonomy' => $options['taxonomy'],
						'field'    => 'slug',
						'terms'    => $term->slug,
					),
				),
			);

			$posts = get_posts( $args );

			$term_id = $this->get_term_id_by_name( $options['term_text'], $options['term_opt'], $term->term_id, count( $posts ) );
			if ( ! empty( $term_id ) ) {
				$term_ids['include'][] = $term->term_id;

				continue;
			}

			$term_ids['exclude'][] = $term->term_id;
		}

		return $term_ids;
	}

	/**
	 * Get term id by name.
	 *
	 * @param string $term_text  user text input.
	 * @param array  $term_opt   user options.
	 * @param int    $term_id    term id.
	 * @param int    $post_count post count.
	 *
	 * @return int term id.
	 */
	protected function get_term_id_by_name( $term_text, $term_opt, $term_id, $post_count ) {
		switch ( $term_opt ) {
			case 'equal_to':
				if ( $post_count == $term_text ) {
					return $term_id;
				}
				break;

			case 'not_equal_to':
				if ( $post_count != $term_text ) {
					return $term_id;
				}
				break;

			case 'less_than':
				if ( $post_count < $term_text ) {
					return $term_id;
				}
				break;

			case 'greater_than':
				if ( $post_count > $term_text ) {
					return $term_id;
				}
				break;
		}
	}

	/**
	 * Query terms using WP_Term_Query.
	 *
	 * @param array $query   Query args.
	 *
	 * @return array List of term ids.
	 */
	protected function query_terms( $query ) {
		$defaults = array(
			'fields'     => 'ids', // retrieve only ids.
			'hide_empty' => 0,
			'count'      => false,
		);

		$query = wp_parse_args( $query, $defaults );

		$term_query = new \WP_Term_Query();

		/**
		 * This action runs before the query happens.
		 *
		 * @since 6.0.0
		 *
		 * @param \WP_Term_Query $term_query Query object.
		 */
		do_action( 'bd_before_query', $term_query );

		$terms = $term_query->query( $query );

		/**
		 * This action runs after the query happens.
		 *
		 * @since 6.0.0
		 *
		 * @param \WP_Term_Query $term_query Query object.
		 */
		do_action( 'bd_after_query', $term_query );

		return $terms;
	}
}
