<?php

namespace BulkWP\BulkDelete\Core\Sites;

use BulkWP\BulkDelete\Core\Base\BaseModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Module for deleting sites.
 *
 * @since 6.2.0
 */
abstract class SitesModule extends BaseModule {
	protected $item_type = 'sites';

	/**
	 * Build query params for WP_Site_Query by using delete options.
	 *
	 * Return an empty query array to short-circuit deletion.
	 *
	 * @param array $options Delete options.
	 *
	 * @return array Query.
	 */
	abstract protected function build_query( $options );

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

		return $this->delete_sites_from_query( $query, $options );
	}

	/**
	 * Query and Delete sites.
	 *
	 * @access protected
	 *
	 * @param array $query   Options to query sites.
	 * @param array $options Delete options.
	 *
	 * @return int Number of sites deleted.
	 */
	protected function delete_sites_from_query( $query, $options ) {
		$count    = 0;
		$comments = $this->query_sites( $query );

		foreach ( $comments as $comment ) {
			$deleted = wp_delete_site( $comment, $options['force_delete'] );

			if ( $deleted ) {
				$count ++;
			}
		}

		return $count;
	}

	/**
	 * Query sites using options.
	 *
	 * @param array $options Query options.
	 *
	 * @return array List of comment IDs.
	 */
	protected function sites( $options ) {
		$defaults = array(
			'update_site_meta_cache' => false,
			'fields'                 => 'ids',
		);

		$options = wp_parse_args( $options, $defaults );

		$wp_site_query = new \WP_Site_Query();

		/**
		 * This action before the query happens.
		 *
		 * @since 6.2.0
		 *
		 * @param \WP_Site_Query $wp_site_query Query object.
		 */
		do_action( 'bd_before_query', $wp_site_query );

		$sites = (array) $wp_site_query->query( $options );

		/**
		 * This action runs after the query happens.
		 *
		 * @since 6.2.0
		 *
		 * @param \WP_Site_Query $wp_site_query Query object.
		 */
		do_action( 'bd_after_query', $wp_site_query );

		return $sites;
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
