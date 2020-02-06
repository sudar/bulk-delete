<?php
/**
 * From Bulk Delete v6.1.0, the minimum required PHP version is v5.6
 * This opens the possibility of using Traits.
 */

namespace BulkWP\BulkDelete\Core\Base\Mixin;

/**
 * Operator related helpers.
 *
 * Used in Renderer.
 *
 * @since 6.1.0
 */
trait OperatorHelpers {

	/**
	 * Get the master list of operators.
	 *
	 * The operators are cached using static variable for performance.
	 *
	 * @return array Master list of operators.
	 */
	protected function get_operator_master_list() {
		static $operator_with_labels = [];

		if ( empty( $operator_with_labels ) ) {
			$operator_with_labels = [
				'='           => __( 'equal to', 'bulk-delete' ),
				'!='          => __( 'not equal to', 'bulk-delete' ),
				'<'           => __( 'less than', 'bulk-delete' ),
				'<='          => __( 'less than or equal to', 'bulk-delete' ),
				'>'           => __( 'greater than', 'bulk-delete' ),
				'>='          => __( 'greater than or equal to', 'bulk-delete' ),
				'IN'          => __( 'in', 'bulk-delete' ),
				'NOT IN'      => __( 'not in', 'bulk-delete' ),
				'BETWEEN'     => __( 'between', 'bulk-delete' ),
				'NOT BETWEEN' => __( 'not between', 'bulk-delete' ),
				'EXISTS'      => __( 'exists', 'bulk-delete' ),
				'NOT EXISTS'  => __( 'not exists', 'bulk-delete' ),
				'LIKE'        => __( 'contains', 'bulk-delete' ),
				'NOT LIKE'    => __( 'not contains', 'bulk-delete' ),
				'STARTS_WITH' => __( 'starts with', 'bulk-delete' ),
				'ENDS_WITH'   => __( 'ends with', 'bulk-delete' ),
				'CONTAINS'    => __( 'contains', 'bulk-delete' ),
			];
		}

		return $operator_with_labels;
	}

	/**
	 * Resolve the operators array.
	 *
	 * @param array $operators Operators array.
	 *
	 * @return array Resolved operators.
	 */
	protected function resolve_operators( $operators ) {
		$operator_list_to_render = [];

		$operator_master_list = $this->get_operator_master_list();

		foreach ( $operators as $operator ) {
			if ( array_key_exists( $operator, $operator_master_list ) ) {
				$operator_master_list[] = $operator;
			} else {
				$operator_list_to_render = array_merge( $operator_list_to_render, $this->resolve_operator( $operator ) );
			}
		}

		return $operator_list_to_render;
	}

	/**
	 * Resolve the operator.
	 *
	 * Users can specify placeholders for operators.
	 * The following placeholders are currently supported
	 * - equals
	 * - numeric
	 * - ins
	 * - betweens
	 * - exists-all
	 * - likes
	 * - string-start-end
	 * - string-all
	 *
	 * @param string $operator Operator.
	 *
	 * @return array Resolved operators.
	 */
	protected function resolve_operator( $operator ) {
		switch ( $operator ) {
			case 'equals':
				return [ '=', '!=' ];

			case 'numeric':
				return [ '<', '<=', '>', '>=' ];

			case 'ins':
				return [ 'IN', 'NOT IN' ];

			case 'betweens':
				return [ 'BETWEEN', 'NOT BETWEEN' ];

			case 'exist-all':
				return [ 'EXISTS', 'NOT EXISTS' ];

			case 'likes':
				return [ 'LIKE', 'NOT LIKE' ];

			case 'string-start-end':
				return [ 'STARTS_WITH', 'ENDS_WITH' ];

			case 'string-all':
				return [ 'STARTS_WITH', 'ENDS_WITH', 'CONTAINS' ];
		}
	}

	/**
	 * Get the label for an operator.
	 *
	 * @param string $operator Operator.
	 *
	 * @return string Operator label.
	 */
	protected function get_operator_label( $operator ) {
		$operator_with_labels = $this->get_operator_master_list();

		if ( ! array_key_exists( $operator, $operator_with_labels ) ) {
			return '';
		}

		return $operator_with_labels[ $operator ];
	}
}
