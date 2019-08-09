<?php

namespace BulkWP\BulkDelete\Addon\QueryOverriders;

use BulkWP\BulkDelete\Core\Base\BaseQueryOverrider;

/**
 * Overrides query that is used for retrieving comments.
 *
 * @since 6.1.0
 */
class DeleteCommentsQueryOverrider extends BaseQueryOverrider {
	/**
	 * DB column name. Mostly either `comment_author_IP` or `comment_author`.
	 *
	 * @var string
	 */
	protected $db_column_name;

	/**
	 * The value that should be compared.
	 *
	 * @var string
	 */
	protected $db_column_value;

	public function load() {
		add_action( 'parse_comment_query', array( $this, 'parse_query' ) );
	}

	/**
	 * Parse the query and retrieve the values.
	 *
	 * @param array $query WP_Comment_Query arguments.
	 */
	public function parse_query( $query ) {
		if ( isset( $query->query_vars['bd_db_column_name'], $query->query_vars['bd_db_column_value'] ) ) {
			$this->db_column_name  = $query->query_vars['bd_db_column_name'];
			$this->db_column_value = $query->query_vars['bd_db_column_value'];

			add_filter( 'comments_clauses', array( $this, 'filter_where' ) );
		}
	}

	/**
	 * Modify the where clause.
	 *
	 * @param array $clauses Comment clauses.
	 *
	 * @return array Modified comment clauses.
	 */
	public function filter_where( $clauses ) {
		$clauses['where'] .= sprintf( " AND %s = '%s' ", esc_sql( $this->db_column_name ), esc_sql( $this->db_column_value ) );

		$this->remove_where_filter();

		return $clauses;
	}

	/**
	 * Remove the `posts_where` filter.
	 */
	public function remove_where_filter() {
		remove_filter( 'comments_clauses', array( $this, 'filter_where' ) );
	}
}
