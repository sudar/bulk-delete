<?php

namespace BulkWP\BulkDelete\Core\Terms\QueryOverriders;

use BulkWP\BulkDelete\Core\Base\BaseQueryOverrider;

/**
 * Overrides query that is used for retrieving terms.
 *
 * @since 6.1.0
 */
class DeleteTermsQueryOverrider extends BaseQueryOverrider {
	/**
	 * Comparison operator.
	 *
	 * @var string
	 */
	protected $operator;

	/**
	 * The value that should be compared.
	 *
	 * @var string
	 */
	protected $db_column_value;

	/**
	 * The db column name that should be compared.
	 *
	 * @var string
	 */
	protected $db_column_name;

	public function load() {
		add_action( 'parse_term_query', array( $this, 'parse_query' ) );
	}

	/**
	 * Parse the query and retrieve the values.
	 *
	 * @param array $query WP_Comment_Query arguments.
	 */
	public function parse_query( $query ) {
		if ( isset( $query->query_vars['bd_operator'], $query->query_vars['bd_value'], $query->query_vars['bd_column_name'] ) ) {
			$this->operator        = $query->query_vars['bd_operator'];
			$this->db_column_value = $query->query_vars['bd_value'];
			$this->db_column_name  = $query->query_vars['bd_column_name'];

			add_filter( 'terms_clauses', array( $this, 'filter_where' ) );
		}
	}

	/**
	 * Modify the where clause.
	 *
	 * @param array $clauses Term clauses.
	 *
	 * @return array Modified term clauses.
	 */
	public function filter_where( $clauses ) {
		if ( 'name' === $this->db_column_name ) {
			$this->db_column_value = "'" . $this->db_column_value . "'";
		}
		$clauses['where'] .= sprintf( ' AND %s %s %s ', esc_sql( $this->db_column_name ), esc_sql( $this->operator ), $this->db_column_value );

		$this->remove_where_filter();

		return $clauses;
	}

	/**
	 * Remove the where filter.
	 */
	public function remove_where_filter() {
		remove_filter( 'terms_clauses', array( $this, 'filter_where' ) );
	}
}
