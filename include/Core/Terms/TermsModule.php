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

	public function filter_js_array( $js_array ) {
		return $js_array;
	}

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
		$term_ids = bd_term_query( $query, $options['taxonomy'] );

		return $this->delete_terms_by_id( $term_ids, $options );
	}

	/**
	 * Render the "private post" setting fields.
	 */
	protected function render_private_post_settings() {
		bd_render_private_post_settings( $this->field_slug );
	}

	/**
	 * Delete terms by ids.
	 *
	 * @param int[] $term_ids List of term ids to delete.
	 * @param mixed $options
	 *
	 * @return int Number of posts deleted.
	 */
	protected function delete_terms_by_id( $term_ids, $options ) {
		$count = 0;
		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id, $options['taxonomy'] );
			if( isset( $options['no_posts'] ) ){
				if( $term->count == 0 ){
					wp_delete_term( $term_id, $options['taxonomy'] );
					$count++;
				}
			}else{
				wp_delete_term( $term_id, $options['taxonomy'] );
				$count++;
			}
		}

		return $count;
	}

	protected function bd_starts_with($haystack, $needle){
	     $length = strlen($needle);

	     return (substr($haystack, 0, $length) === $needle);
	}

	protected function bd_ends_with($haystack, $needle){
	    $length = strlen($needle);

	    return $length === 0 ||
	    (substr($haystack, -$length) === $needle);
	}

	protected function term_starts( $term_text , $options ){
		$term_ids = array();
		$terms    = get_terms( $options['taxonomy'], array(
		    'hide_empty' => false,
		) );

		foreach( $terms as $term ){
			if( $this->bd_starts_with( $term->name, $term_text ) ){
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}

	protected function term_ends( $term_text , $options ){
		$term_ids = array();
		$terms    = get_terms( $options['taxonomy'], array(
		    'hide_empty' => false,
		) );

		foreach( $terms as $term ){
			if( $this->bd_ends_with( $term->name, $term_text ) ){
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}

	protected function term_contains( $term_text , $options ){
		$term_ids = array();
		$terms    = get_terms( $options['taxonomy'], array(
		    'hide_empty' => false,
		) );

		foreach( $terms as $term ){
			if ( strpos( $term->name, $term_text ) !== false ) {
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}
	protected function term_count_query( $options ){
		$term_ids = array();
		$terms    = get_terms( $options['taxonomy'], array(
		    'hide_empty' => false,
		) );

		foreach( $terms as $term ){
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

			$posts = get_posts($args);

			if( count($posts) == $options['term_text'] && $options['term_opt'] == 'equal_to' ){
				$term_ids[] = $term->term_id;
			}elseif( count($posts) != $options['term_text'] && $options['term_opt'] == 'not_equal_to' ){
				$term_ids[] = $term->term_id;
			}elseif( count($posts) < $options['term_text'] && $options['term_opt'] == 'less_than' ){
				$term_ids[] = $term->term_id;
			}elseif( count($posts) > $options['term_text'] && $options['term_opt'] == 'greater_than' ){
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}

	/**
	 * Wrapper for WP_Term.
	 *
	 * Adds some performance enhancing defaults.
	 *
	 * @since  6.0
	 *
	 * @param array $options  List of options
	 * @param mixed $taxonomy
	 *
	 * @return array Result array
	 */
	function term_query( $options, $taxonomy ) {
		$defaults = array(
			'fields'                 => 'ids', // retrieve only ids
			'taxonomy'				           => $taxonomy,
			'hide_empty'			          => 0,
			'count'					             => true,
		);
		$options = wp_parse_args( $options, $defaults );

		$term_query = new WP_Term_Query();

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
