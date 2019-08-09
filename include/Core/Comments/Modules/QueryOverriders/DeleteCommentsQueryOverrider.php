<?php

namespace BulkWP\BulkDelete\Addon\Modules\QueryOverriders;

use BulkWP\BulkDelete\Core\Base\BaseQueryOverrider;

/**
 * Overrides query for deleting comments.
 */
class DeleteCommentsQueryOverrider extends BaseQueryOverrider {
	protected $field;
	protected $value;

	/**
	 * Parse the query and retrieve the values.
	 *
	 * @param array $query WP_Comment_Query arguments.
	 */
	public function parse_query( $query ) {
		if ( isset( $query->query_vars['field_name'], $query->query_vars['value'] ) ) {
			$this->field = $query->query_vars['field_name'];
			$this->value = $query->query_vars['value'];

			// TODO: Use WP_Comment_Query related filters.
			add_filter( 'posts_where', array( $this, 'filter_where' ) );
			add_filter( 'posts_selection', array( $this, 'remove_where_filter' ) );
		}
	}

	/**
	 * Modify the where clause.
	 *
	 * @param string $where (optional) Where clause.
	 * @return string Modified Where clause
	 */
	public function filter_where( $where = '' ) {
		global $wpdb;

		$where .= sprintf( " AND %s.post_title %s = '%s' ", $wpdb->comments, esc_sql( $this->field ), esc_sql( $this->value ) );
		return $where;
	}

	/**
	 * Remove the `posts_where` filter.
	 */
	public function remove_where_filter() {
		remove_filter( 'posts_where', array( $this, 'filter_where' ) );
	}
}
