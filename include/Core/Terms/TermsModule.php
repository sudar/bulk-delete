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
}
