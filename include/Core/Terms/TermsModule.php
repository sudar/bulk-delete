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
	 * Build query params for WP_Query by using delete options.
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

		$options['restrict']     = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_restrict', false );
		$options['limit_to']     = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_limit_to', 0 ) );
		$options['force_delete'] = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_force_delete', false );

		$options['date_op'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_op' );
		$options['days']    = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_days' ) );

		return $options;
	}

	/**
	 * Filter the js array.
	 * This function will be overridden by the child classes.
	 *
	 * @since 5.5
	 *
	 * @param array $js_array JavaScript Array.
	 *
	 * @return array Modified JavaScript Array.
	 */
	public function filter_js_array( $js_array ) {
		return $js_array;
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

		return $this->delete_terms_from_query( $query, $options );
	}

	/**
	 * Build the query using query params and then Delete posts.
	 *
	 * @param array $query   Params for WP Query.
	 * @param array $options Delete Options.
	 *
	 * @return int Number of posts deleted.
	 */
	protected function delete_terms_from_query( $query, $options ) {
		$term_ids = $this->term_query( $query, $options['taxonomy'] );

		return $this->delete_terms_by_id( $term_ids, $options );
	}

	/**
	 * Delete terms by ids.
	 *
	 * @param int[] $term_ids List of term ids to delete.
	 * @param mixed $options  user options.
	 *
	 * @return int Number of posts deleted.
	 */
	protected function delete_terms_by_id( $term_ids, $options ) {
		$count = 0;

		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id, $options['taxonomy'] );

			if ( is_wp_error( $term ) ) {
				continue;
			}

			if ( isset( $options['no_posts'] ) && $term->count > 0 ) {
				continue;
			}

			wp_delete_term( $term_id, $options['taxonomy'] );
			$count ++;
		}

		return $count;
	}

	/**
	 * Custom string function use to get is string start with specified string.
	 *
	 * @param string $haystack search string.
	 * @param string $needle   find string.
	 *
	 * @return bool.
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
	 * @return bool.
	 */
	protected function bd_ends_with( $haystack, $needle ) {
		$length = strlen( $needle );

		return $length === 0 ||
		( substr( $haystack, -$length ) === $needle );
	}

	/**
	 * Get terms which is start with specified string.
	 *
	 * @param string $term_text user input text.
	 * @param array  $options   user options.
	 *
	 * @return array term ids.
	 */
	protected function term_starts( $term_text, $options ) {
		$term_ids = array();
		$terms    = get_terms(
			$options['taxonomy'], array(
				'hide_empty' => false,
			)
		);

		foreach ( $terms as $term ) {
			if ( $this->bd_starts_with( $term->name, $term_text ) ) {
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}

	/**
	 * Get terms which is ends with specified string.
	 *
	 * @param string $term_text user input text.
	 * @param array  $options   user options.
	 *
	 * @return array term ids.
	 */
	protected function term_ends( $term_text, $options ) {
		$term_ids = array();
		$terms    = get_terms(
			$options['taxonomy'], array(
				'hide_empty' => false,
			)
		);

		foreach ( $terms as $term ) {
			if ( $this->bd_ends_with( $term->name, $term_text ) ) {
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}

	/**
	 * Get terms which is contain specified string.
	 *
	 * @param string $term_text user input text.
	 * @param array  $options   user options.
	 *
	 * @return array term ids.
	 */
	protected function term_contains( $term_text, $options ) {
		$term_ids = array();
		$terms    = get_terms(
			$options['taxonomy'], array(
				'hide_empty' => false,
			)
		);

		foreach ( $terms as $term ) {
			if ( strpos( $term->name, $term_text ) !== false ) {
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
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
				'post_type' => 'post',
				'tax_query' => array(
					array(
						'taxonomy' => $options['taxonomy'],
						'field'    => 'slug',
						'terms'    => $term->slug,
					),
				),
			);

			$posts = get_posts( $args );

			$term_ids[] = $this->get_term_id_by_name( $options['term_text'], $options['term_opt'], $term->term_id, count( $posts ) );
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
				if ( $post_count === $term_text ) {
					return $term_id;
				}
				break;

			case 'not_equal_to':
				if ( $post_count !== $term_text ) {
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
	 * Wrapper for WP_Term.
	 *
	 * Adds some performance enhancing defaults.
	 *
	 * @since  6.0
	 *
	 * @param array $options  List of options.
	 * @param mixed $taxonomy List of Taxonomies.
	 *
	 * @return array Result array
	 */
	public function term_query( $options, $taxonomy ) {
		$defaults = array(
			'fields'     => 'ids', // retrieve only ids.
			'taxonomy'   => $taxonomy,
			'hide_empty' => 0,
			'count'      => true,
		);
		$options  = wp_parse_args( $options, $defaults );

		$term_query = new \WP_Term_Query();

		/**
		 * This action runs before the query happens.
		 *
		 * @since 5.5
		 * @since 5.6 added $term_query param.
		 *
		 * @param \WP_Query $term_query Query object.
		 */
		do_action( 'bd_before_term_query', $term_query );

		$terms = $term_query->query( $options );

		/**
		 * This action runs after the query happens.
		 *
		 * @since 5.5
		 * @since 5.6 added $term_query param.
		 *
		 * @param \WP_Query $term_query Query object.
		 */
		do_action( 'bd_after_term_query', $term_query );

		return $terms;
	}
}
