<?php

namespace BulkWP\BulkDelete\Core;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Controller.
 *
 * Handle all requests and automatically perform nonce checks.
 *
 * @since 5.5.4
 * @since 6.0.0 Added namespace.
 */
class Controller {

	/**
	 * Controller constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'request_handler' ) );
		add_action( 'bd_pre_bulk_action', array( $this, 'increase_timeout' ), 9 );
		add_action( 'bd_before_scheduler', array( $this, 'increase_timeout' ), 9 );

		add_filter( 'bd_get_action_nonce_check', array( $this, 'verify_get_request_nonce' ), 10, 2 );

		if ( defined( 'BD_DEBUG' ) && BD_DEBUG ) {
			add_action( 'bd_after_query', array( $this, 'log_sql_query' ) );
		}
	}

	/**
	 * Handle both POST and GET requests.
	 * This method automatically triggers all the actions after checking the nonce.
	 */
	public function request_handler() {
		if ( isset( $_POST['bd_action'] ) ) {
			$bd_action   = sanitize_text_field( $_POST['bd_action'] );
			$nonce_valid = false;

			if ( 'delete_jetpack_messages' === $bd_action && wp_verify_nonce( $_POST['sm-bulk-delete-misc-nonce'], 'sm-bulk-delete-misc' ) ) {
				$nonce_valid = true;
			}

			/**
			 * Perform nonce check.
			 *
			 * @since 5.5
			 */
			if ( ! apply_filters( 'bd_action_nonce_check', $nonce_valid, $bd_action ) ) {
				return;
			}

			/**
			 * Before performing a bulk action.
			 * This hook is for doing actions just before performing any bulk operation.
			 *
			 * @since 5.4
			 */
			do_action( 'bd_pre_bulk_action', $bd_action );

			/**
			 * Perform the bulk operation.
			 * This hook is for doing the bulk operation. Nonce check has already happened by this point.
			 *
			 * @since 5.4
			 */
			do_action( 'bd_' . $bd_action, $_POST );
		}

		if ( isset( $_GET['bd_action'] ) ) {
			$bd_action   = sanitize_text_field( $_GET['bd_action'] );
			$nonce_valid = false;

			/**
			 * Perform nonce check.
			 *
			 * @since 5.5.4
			 */
			if ( ! apply_filters( 'bd_get_action_nonce_check', $nonce_valid, $bd_action ) ) {
				return;
			}

			/**
			 * Perform the bulk operation.
			 * This hook is for doing the bulk operation. Nonce check has already happened by this point.
			 *
			 * @since 5.5.4
			 */
			do_action( 'bd_' . $bd_action, $_GET );
		}
	}

	/**
	 * Verify if GET request has a valid nonce.
	 *
	 * @since  5.5.4
	 *
	 * @param bool   $result Whether nonce is valid.
	 * @param string $action Action name.
	 *
	 * @return bool True if nonce is valid, otherwise return $result.
	 */
	public function verify_get_request_nonce( $result, $action ) {
		if ( check_admin_referer( "bd-{$action}", "bd-{$action}-nonce" ) ) {
			return true;
		}

		return $result;
	}

	/**
	 * Increase PHP timeout.
	 *
	 * This is to prevent bulk operations from timing out
	 *
	 * @since 5.5.4
	 */
	public function increase_timeout() {
		// phpcs:ignore PHPCompatibility.PHP.DeprecatedIniDirectives.safe_modeDeprecatedRemoved
		if ( ! ini_get( 'safe_mode' ) ) {
			// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			@set_time_limit( 0 );
		}
	}

	/**
	 * Log SQL query used by Bulk Delete.
	 *
	 * Query is logged only when `BD_DEBUG` is set.
	 *
	 * @since 5.6
	 * @since 6.0.0 Moved into Controller class.
	 *
	 * @param \WP_Query $wp_query WP Query object.
	 */
	public function log_sql_query( $wp_query ) {
		$query = $wp_query->request;

		/**
		 * Bulk Delete query is getting logged.
		 *
		 * @since 5.6
		 *
		 * @param string $query Bulk Delete SQL Query.
		 */
		do_action( 'bd_log_sql_query', $query );

		error_log( 'Bulk Delete Query: ' . $query );
	}
}
