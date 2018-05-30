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
		$js_array['msg']['deletePostsWarning'] = __( 'Are you sure you want to delete all the posts based on the selected option?', 'bulk-delete' );
		$js_array['msg']['selectPostOption']   = __( 'Please select posts from at least one option', 'bulk-delete' );

		return $js_array;
	}

	protected function do_delete( $options ) {
		$query = $this->build_query( $options );

		if ( empty( $query ) ) {
			// Short circuit deletion, if nothing needs to be deleted.
			return 0;
		}

		return $this->delete_posts_from_query( $query, $options );
	}

	/**
	 * Build the query using query params and then Delete posts.
	 *
	 * @param array $query   Params for WP Query.
	 * @param array $options Delete Options.
	 *
	 * @return int Number of posts deleted.
	 */
	protected function delete_posts_from_query( $query, $options ) {
		$query    = bd_build_query_options( $options, $query );
		$post_ids = bd_query( $query );

		return $this->delete_posts_by_id( $post_ids, $options['force_delete'] );
	}

	/**
	 * Render the "private post" setting fields.
	 */
	protected function render_private_post_settings() {
		bd_render_private_post_settings( $this->field_slug );
	}

	
}
