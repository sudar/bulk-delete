<?php
/**
 * Utility and wrapper functions for WP_Query.
 *
 * @since      5.5
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Util
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Process delete options array and build query.
 *
 * @param array $delete_options Delete Options
 * @param array $options        (optional) Options query
 */
function bd_build_query_options( $delete_options, $options = array() ) {
	// private posts
	if ( isset( $delete_options['private'] ) ) {
		if ( $delete_options['private'] ) {
			$options['post_status'] = 'private';
		} else {
			if ( ! isset( $options['post_status'] ) ) {
				$options['post_status'] = 'publish';
			}
		}
	}

	// limit to query
	if ( $delete_options['limit_to'] > 0 ) {
		$options['showposts'] = $delete_options['limit_to'];
	} else {
		$options['nopaging']  = 'true';
	}

	// post type
	if ( isset( $delete_options['post_type'] ) ) {
		$options['post_type'] = $delete_options['post_type'];
	}

	// date query
	if ( $delete_options['restrict'] ) {
		if ( 'before' === $delete_options['date_op'] || 'after' === $delete_options['date_op'] ) {
			$options['date_query'] = array(
				array(
					'column'                   => 'post_date',
					$delete_options['date_op'] => "{$delete_options['days']} day ago",
				),
			);
		}
	}

	return $options;
}

/**
 * Wrapper for WP_query.
 *
 * Adds some performance enhancing defaults.
 *
 * @since  5.5
 *
 * @param array $options List of options
 *
 * @return array Result array
 */
function bd_query( $options ) {
	$defaults = array(
		'cache_results'          => false, // don't cache results
		'update_post_meta_cache' => false, // No need to fetch post meta fields
		'update_post_term_cache' => false, // No need to fetch taxonomy fields
		'no_found_rows'          => true,  // No need for pagination
		'fields'                 => 'ids', // retrieve only ids
	);
	$options = wp_parse_args( $options, $defaults );

	$wp_query = new WP_Query();

	/**
	 * This action runs before the query happens.
	 *
	 * @since 5.5
	 * @since 5.6 added $wp_query param.
	 *
	 * @param \WP_Query $wp_query Query object.
	 */
	do_action( 'bd_before_query', $wp_query );

	$posts = $wp_query->query( $options );

	/**
	 * This action runs after the query happens.
	 *
	 * @since 5.5
	 * @since 5.6 added $wp_query param.
	 *
	 * @param \WP_Query $wp_query Query object.
	 */
	do_action( 'bd_after_query', $wp_query );

	return $posts;
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
function bd_term_query( $options, $taxonomy ) {
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

function bd_starts_with($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function bd_ends_with($haystack, $needle)
{
    $length = strlen($needle);

    return $length === 0 || 
    (substr($haystack, -$length) === $needle);
}

function bd_term_starts( $term_text , $options ){
	$term_ids = array();
	$terms = get_terms( $options['taxonomy'], array(
	    'hide_empty' => false,
	) );

	foreach( $terms as $term ){
		if( bd_starts_with( $term->name, $term_text ) ){
			$term_ids[] = $term->term_id;
		}
	}
	return $term_ids;
}

function bd_term_ends( $term_text , $options ){
	$term_ids = array();
	$terms = get_terms( $options['taxonomy'], array(
	    'hide_empty' => false,
	) );

	foreach( $terms as $term ){
		if( bd_ends_with( $term->name, $term_text ) ){
			$term_ids[] = $term->term_id;
		}
	}
	return $term_ids;
}

function bd_term_contains( $term_text , $options ){
	$term_ids = array();
	$terms = get_terms( $options['taxonomy'], array(
	    'hide_empty' => false,
	) );

	foreach( $terms as $term ){
		if ( strpos( $term->name, $term_text ) !== false ) {
			$term_ids[] = $term->term_id;
		}
	}
	return $term_ids;
}