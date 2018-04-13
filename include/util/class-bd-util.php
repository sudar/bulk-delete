<?php
/**
 * Utility classes and functions.
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Util
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Utility class.
 *
 * Ideally most of the functions should be inside the `BulkDelete\Util` and not as static functions.
 */
class BD_Util {
	// Meta boxes
	const VISIBLE_POST_BOXES     = 'metaboxhidden_toplevel_page_bulk-delete-posts';
	const VISIBLE_PAGE_BOXES     = 'metaboxhidden_bulk-delete_page_bulk-delete-pages';
	const VISIBLE_USER_BOXES     = 'metaboxhidden_bulk-delete_page_bulk-delete-users';

	/**
	 * Check whether the meta box in posts page is hidden or not.
	 *
	 * @static
	 * @access public
	 *
	 * @param string $box The name of the box
	 *
	 * @return bool True if the box is hidden, False otherwise
	 */
	public static function is_posts_box_hidden( $box ) {
		$hidden_boxes = self::get_posts_hidden_boxes();

		return is_array( $hidden_boxes ) && in_array( $box, $hidden_boxes );
	}

	/**
	 * Get the list of hidden boxes in posts page.
	 *
	 * @static
	 * @access public
	 *
	 * @return array The list of hidden meta boxes
	 */
	public static function get_posts_hidden_boxes() {
		$current_user = wp_get_current_user();

		return get_user_meta( $current_user->ID, self::VISIBLE_POST_BOXES, true );
	}

	/**
	 * Check whether the meta box in pages page is hidden or not.
	 *
	 * @since  5.0
	 * @static
	 * @access public
	 *
	 * @param string $box The name of the box to check
	 *
	 * @return bool True if the box is hidden, False otherwise
	 */
	public static function is_pages_box_hidden( $box ) {
		$hidden_boxes = self::get_pages_hidden_boxes();

		return is_array( $hidden_boxes ) && in_array( $box, $hidden_boxes );
	}

	/**
	 * Get the list of hidden boxes in posts page.
	 *
	 * @since  5.0
	 * @static
	 * @access public
	 *
	 * @return the array of hidden meta boxes
	 */
	public static function get_pages_hidden_boxes() {
		$current_user = wp_get_current_user();

		return get_user_meta( $current_user->ID, self::VISIBLE_PAGE_BOXES, true );
	}

	/**
	 * Check whether the meta box in users page is hidden or not.
	 *
	 * @static
	 * @access public
	 *
	 * @param string $box The name of the box to check
	 *
	 * @return bool True if the box is hidden, False otherwise
	 */
	public static function is_users_box_hidden( $box ) {
		$hidden_boxes = self::get_users_hidden_boxes();

		return is_array( $hidden_boxes ) && in_array( $box, $hidden_boxes );
	}

	/**
	 * Get the list of hidden boxes in users page.
	 *
	 * @static
	 * @access public
	 *
	 * @return array The array of hidden meta boxes
	 */
	public static function get_users_hidden_boxes() {
		$current_user = wp_get_current_user();

		return get_user_meta( $current_user->ID, self::VISIBLE_USER_BOXES, true );
	}

	/**
	 * Generate display name from post type and status.
	 *
	 * @static
	 *
	 * @param string $str
	 *
	 * @return string Label
	 */
	public static function display_post_type_status( $str ) {
		$type_status = self::split_post_type_status( $str );

		$status = $type_status['status'];
		$type   = $type_status['type'];
		$label  = '';

		switch ( $status ) {
			case 'private':
				$label = $type . ' - Private Posts';
				break;
			case 'future':
				$label = $type . ' - Scheduled Posts';
				break;
			case 'draft':
				$label = $type . ' - Draft Posts';
				break;
			case 'pending':
				$label = $type . ' - Pending Posts';
				break;
			case 'publish':
				$label = $type . ' - Published Posts';
				break;
		}

		return $label;
	}

	/**
	 * Split post type and status.
	 *
	 * @static
	 * @access public
	 *
	 * @param string $str
	 *
	 * @return array
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
