<?php
/**
 * Class that encapsulates the logic for handling date format specified by the user.
 *
 * @package Bulk Delete
 * @author Sudar
 *
 * @since 6.0
 */
class Bulk_Delete_Date_Handler {
	/**
	 * Delete Options.
	 *
	 * @var array
	 */
	protected $delete_options;
	/**
	 * Date format of meta value that is stored in `wp_post_meta` table..
	 *
	 * @var string
	 */
	protected $meta_value_date_format;

	/**
	 * Date format of input value that will be compared with meta value.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $input_value_date_format;


	/**
	 * Creates query object after processing date with specified date format.
	 *
	 * @param array $delete_options Delete Options.
	 * @return \WP_Query $query Query object.
	 */
	public function get_query( $delete_options ) {
		$query = $this->process_date_fields( $delete_options );
		return $query;
	}

	public function process_date_fields( $delete_options ) {
		if ( ! empty( $delete_options['relative_date'] ) && 'custom' !== $delete_options['relative_date'] ) {
			$delete_options['meta_value'] = date( 'c', strtotime( $delete_options['relative_date'] ) );
		}

		if ( ! empty( $delete_options['date_unit'] ) && ! empty( $delete_options['date_type'] ) ) {
			$interval_unit = $delete_options['date_unit'];
			$interval_type = $delete_options['date_type'];

			switch ( $delete_options['meta_op'] ) {
				case '<':
				case '<=':
					$delete_options['meta_value'] = date( 'Y-m-d', strtotime( '-' . $interval_unit . ' ' . $interval_type ) );
					break;
				default:
					$delete_options['meta_value'] = date( 'Y-m-d', strtotime( $interval_unit . ' ' . $interval_type ) );
			}
		}

		// In v1.0 `date_format` was changed to `meta_value_date_format`.
		// Needed?
		if ( isset( $delete_options['date_format'] ) ) {
			$delete_options['meta_value_date_format'] = $delete_options['date_format'];
		}
		$meta_query = array(
			'key'     => $delete_options['meta_key'],
			'value'   => $delete_options['meta_value'],
			'compare' => $delete_options['meta_op'],
			'type'    => $delete_options['meta_type'],
		);

		$options = array(
			'meta_query' => array( $meta_query ),
		);

		if ( 'DATE' === $meta_query['type'] && ! empty( $delete_options['meta_value_date_format'] ) ) {
			$options['cf_meta_value_date_format'] = $delete_options['meta_value_date_format'];

			if ( ! empty( $delete_options['input_value_date_format'] ) ) {
				$options['cf_input_value_date_format'] = $delete_options['input_value_date_format'];
			} else {
				$options['cf_input_value_date_format'] = '%Y-%m-%d';
			}

			$this->load();
		}
		return $options;
	}

	/**
	 * Setup hooks and load.
	 *
	 * @since 1.0
	 */
	public function load() {
		add_action( 'parse_query', array( $this, 'parse_query' ) );
	}

	/**
	 * Parse the query object
	 *
	 * @since  0.3
	 *
	 * @param \WP_Query $query Query object.
	 */
	public function parse_query( $query ) {
		if ( isset( $query->query_vars['cf_meta_value_date_format'] ) ) {
			$this->meta_value_date_format  = $query->query_vars['cf_meta_value_date_format'];
			$this->input_value_date_format = $query->query_vars['cf_input_value_date_format'];

			add_filter( 'get_meta_sql', array( $this, 'process_sql_date_format' ), 10, 6 );
			add_filter( 'posts_selection', array( $this, 'remove_filter' ) );
		}
	}

	/**
	 * Process date format in sql query.
	 *
	 * @since 0.3
	 *
	 * @param array  $query          Array containing the query's JOIN and WHERE clauses.
	 * @param array  $input          Array of meta queries.
	 * @param string $type           Type of meta.
	 * @param string $primary_table  Primary table.
	 * @param string $primary_column Primary column ID.
	 * @param object $context        The main query object.
	 *
	 * @return array $query Processed query.
	 */
	public function process_sql_date_format( $query, $input, $type, $primary_table, $primary_column, $context ) {
		global $wpdb;

		if ( 'DATE' === $input[0]['type'] && 'post' === $type && 'wp_posts' === $primary_table && 'ID' === $primary_column ) {
			$meta_table = _get_meta_table( $type );

			$query['where'] = $wpdb->prepare( " AND ( $meta_table.meta_key = %s AND STR_TO_DATE($meta_table.meta_value, %s) {$input[0]['compare']} STR_TO_DATE(%s, %s) ) ",
				$input[0]['key'],
				$this->meta_value_date_format,
				$input[0]['value'],
				$this->input_value_date_format
			);
		}
		return $query;
	}

	/**
	 * Remove the filter
	 *
	 * @since 0.3
	 * @access public
	 */
	public function remove_filter() {
		remove_filter( 'get_meta_sql', array( $this, 'process_sql_date_format' ) );
	}
}
