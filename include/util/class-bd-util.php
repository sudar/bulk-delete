<?php
/**
 * Utility classes and functions.
 *
 * @author     Sudar
 * @package    BulkDelete\Util
 */


/**
 * Utility class.
 *
 * Ideally most of the functions should be inside the `BulkDelete\Util` and not as static functions.
 */
class BD_Util {

	// simple login log
	const SIMPLE_LOGIN_LOG_TABLE = 'simple_login_log';

	// Meta boxes
	const VISIBLE_POST_BOXES     = 'metaboxhidden_toplevel_page_bulk-delete-posts';
	const VISIBLE_PAGE_BOXES     = 'metaboxhidden_bulk-delete_page_bulk-delete-pages';
	const VISIBLE_USER_BOXES     = 'metaboxhidden_bulk-delete_page_bulk-delete-users';

	/**
	 * Find out if Simple Login Log is installed or not
	 * http://wordpress.org/plugins/simple-login-log/
	 *
	 * @static
	 * @access public
	 * @return bool    True if plugin is installed, False otherwise
	 */
	public static function is_simple_login_log_present() {
		global $wpdb;

		if ( $wpdb->get_row( "SHOW TABLES LIKE '{$wpdb->prefix}" . self::SIMPLE_LOGIN_LOG_TABLE . "'" ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check whether the meta box in posts page is hidden or not
	 *
	 * @static
	 * @access public
	 * @param string  $box The name of the box
	 * @return bool        True if the box is hidden, False otherwise
	 */
	public static function is_posts_box_hidden( $box ) {
		$hidden_boxes = self::get_posts_hidden_boxes();
		return is_array( $hidden_boxes ) && in_array( $box, $hidden_boxes );
	}

	/**
	 * Get the list of hidden boxes in posts page
	 *
	 * @static
	 * @access public
	 * @return array The list of hidden meta boxes
	 */
	public static function get_posts_hidden_boxes() {
		$current_user = wp_get_current_user();
		return get_user_meta( $current_user->ID, self::VISIBLE_POST_BOXES, true );
	}

	/**
	 * Check whether the meta box in pages page is hidden or not
	 *
	 * @since  5.0
	 * @static
	 * @access public
	 * @param string  $box The name of the box to check
	 * @return bool        True if the box is hidden, False otherwise
	 */
	public static function is_pages_box_hidden( $box ) {
		$hidden_boxes = self::get_pages_hidden_boxes();
		return is_array( $hidden_boxes ) && in_array( $box, $hidden_boxes );
	}

	/**
	 * Get the list of hidden boxes in posts page
	 *
	 * @since  5.0
	 * @static
	 * @access public
	 * @return the array of hidden meta boxes
	 */
	public static function get_pages_hidden_boxes() {
		$current_user = wp_get_current_user();
		return get_user_meta( $current_user->ID, self::VISIBLE_PAGE_BOXES, true );
	}

	/**
	 * Check whether the meta box in users page is hidden or not
	 *
	 * @static
	 * @access public
	 * @param string  $box The name of the box to check
	 * @return bool        True if the box is hidden, False otherwise
	 */
	public static function is_users_box_hidden( $box ) {
		$hidden_boxes = self::get_users_hidden_boxes();
		return is_array( $hidden_boxes ) && in_array( $box, $hidden_boxes );
	}

	/**
	 * Get the list of hidden boxes in users page
	 *
	 * @static
	 * @access public
	 * @return array The array of hidden meta boxes
	 */
	public static function get_users_hidden_boxes() {
		$current_user = wp_get_current_user();
		return get_user_meta( $current_user->ID, self::VISIBLE_USER_BOXES, true );
	}

	/**
	 * Get the list of cron schedules
	 *
	 * @static
	 * @access public
	 * @return array The list of cron schedules
	 */
	public static function get_cron_schedules() {

		$cron_items = array();
		$cron = _get_cron_array();
		$date_format = _x( 'M j, Y @ G:i', 'Cron table date format', 'bulk-delete' );
		$i = 0;

		foreach ( $cron as $timestamp => $cronhooks ) {
			foreach ( (array) $cronhooks as $hook => $events ) {
				if ( 'do-bulk-delete-' == substr( $hook, 0, 15 ) ) {
					$cron_item = array();

					foreach ( (array) $events as $key => $event ) {
						$cron_item['timestamp'] = $timestamp;
						$cron_item['due'] = date_i18n( $date_format, $timestamp + ( get_option( 'gmt_offset' ) * 60 * 60 ) );
						$cron_item['schedule'] = $event['schedule'];
						$cron_item['type'] = $hook;
						$cron_item['args'] = $event['args'];
						$cron_item['id'] = $i;
					}

					$cron_items[$i] = $cron_item;
					$i++;
				}
			}
		}
		return $cron_items;
	}

	/**
	 * Generate display name from post type and status
	 *
	 * @static
	 * @param string $str
	 * @return string
	 */
	public static function display_post_type_status( $str ) {
		$type_status = self::split_post_type_status( $str );

		$type   = $type_status['type'];
		$status = $type_status['status'];

		switch ( $status ) {
			case 'private':
				return $type . ' - Private Posts';
				break;
			case 'future':
				return $type . ' - Scheduled Posts';
				break;
			case 'draft':
				return $type . ' - Draft Posts';
				break;
			case 'pending':
				return $type . ' - Pending Posts';
				break;
			case 'publish':
				return $type . ' - Published Posts';
				break;
		}
	}

	/**
	 * Split post type and status
	 *
	 * @static
	 * @access public
	 * @param string $str
	 * @return string
	 */
	public static function split_post_type_status( $str ) {
		$type_status = array();

		$str_arr = explode( '-', $str );

		if ( count( $str_arr ) > 1 ) {
			$type_status['status'] = end( $str_arr );
			$type_status['type']   = implode( '-', array_slice( $str_arr, 0, -1 ) );
		} else {
			$type_status['status'] = 'publish';
			$type_status['type']   = $str;
		}

		return $type_status;
	}
}

/**
 * Get a value from an array based on key.
 * If key is present returns the value, else returns the default value
 *
 * @param array   $array   Array from which value has to be retrieved
 * @param string  $key     Key, whose value to be retrieved
 * @param string  $default Optional. Default value to be returned, if the key is not found
 * @return mixed           Value if key is present, else the default value
 */
if ( ! function_exists( 'array_get' ) ) {
	function array_get( $array, $key, $default = null ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : $default;
	}
}

/**
 * Get a value from an array based on key and convert it into bool.
 *
 * @param array   $array   Array from which value has to be retrieved
 * @param string  $key     Key, whose value to be retrieved
 * @param string  $default Optional. Default value to be returned, if the key is not found
 * @return mixed           Boolean converted Value if key is present, else the default value
 */
if ( ! function_exists( 'array_get_bool' ) ) {
	function array_get_bool( $array, $key, $default = null ) {
		return filter_var( array_get( $array, $key, $default ), FILTER_VALIDATE_BOOLEAN );
	}
}

/**
 * Wrapper for WP_query.
 *
 * Adds some performance enhancing defaults.
 *
 * @since  5.5
 * @param  array $options List of options
 * @return array          Result array
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
	return $wp_query->query( $options );
}

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
			$options['post_status'] = 'publish';
		}
	}

	// limit to query
	if ( $delete_options['limit_to'] > 0 ) {
		$options['showposts'] = $delete_options['limit_to'];
	} else {
		$options['nopaging']  = 'true';
	}

	// date query
	if ( $delete_options['restrict'] ) {
		if ( 'before' == $delete_options['date_op'] || 'after' == $delete_options['date_op'] ) {
			$options['date_query'] = array(
				array(
					'column'                   => 'post_date',
					$delete_options['date_op'] => "{$delete_options['days']} day ago",
				),
			);
		} else {
			// backward compatibility. This will be removed in Bulk Delete v6.0
			$options['op']   = $delete_options['date_op'];
			$options['days'] = $delete_options['days'];

			if ( ! class_exists( 'Bulk_Delete_By_Days' ) ) {
				require_once Bulk_Delete::$PLUGIN_DIR . '/include/util/class-bulk-delete-by-days.php';
			}
			$bulk_Delete_By_Days = new Bulk_Delete_By_Days;
		}
	}

	return $options;
}
?>
