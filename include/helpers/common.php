<?php
/**
 * Contains the helper functions.
 *
 * Some of the functions where created before dropping support for PHP 5.2 and that's the reason why they are not namespaced.
 *
 * @since 6.0.0 File created.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Get a value from an array based on key.
 *
 * If key is present returns the value, else returns the default value.
 *
 * @since 5.6.0 added `bd` prefix.
 *
 * @param array  $array   Array from which value has to be retrieved.
 * @param string $key     Key, whose value to be retrieved.
 * @param string $default Optional. Default value to be returned, if the key is not found.
 *
 * @return mixed Value if key is present, else the default value.
 */
function bd_array_get( $array, $key, $default = null ) {
	return isset( $array[ $key ] ) ? $array[ $key ] : $default;
}

/**
 * Get a value from an array based on key and convert it into bool.
 *
 * @since 5.6.0 added `bd` prefix.
 *
 * @param array  $array   Array from which value has to be retrieved.
 * @param string $key     Key, whose value to be retrieved.
 * @param bool   $default (Optional) Default value to be returned, if the key is not found.
 *
 * @return bool Boolean converted Value if key is present, else the default value.
 */
function bd_array_get_bool( $array, $key, $default = false ) {
	return bd_to_bool( bd_array_get( $array, $key, $default ) );
}

/**
 * Convert a string value into boolean, based on whether the value "True" or "False" is present.
 *
 * @since 5.5
 *
 * @param string $string String value to compare.
 *
 * @return bool True if string is "True", False otherwise.
 */
function bd_to_bool( $string ) {
	return filter_var( $string, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Get GMT Offseted time in Unix Timestamp format.
 *
 * @since 6.0.0
 *
 * @param string $time_string Time string.
 *
 * @return int GMT Offseted time.in Unix Timestamp.
 */
function bd_get_gmt_offseted_time( $time_string ) {
	$gmt_offset = sanitize_text_field( get_option( 'gmt_offset' ) );

	return strtotime( $time_string ) - ( $gmt_offset * HOUR_IN_SECONDS );
}

/**
 * Get the formatted list of allowed mime types.
 * This function was originally defined in the Bulk Delete Attachment addon.
 *
 * @since 5.5
 *
 * @return array List of allowed mime types after formatting
 */
function bd_get_allowed_mime_types() {
	$mime_types = get_allowed_mime_types();
	sort( $mime_types );

	$processed_mime_types        = array();
	$processed_mime_types['all'] = __( 'All mime types', 'bulk-delete' );

	$last_value = '';
	foreach ( $mime_types as $key => $value ) {
		$splitted = explode( '/', $value, 2 );
		$prefix   = $splitted[0];

		if ( '' == $last_value || $prefix != $last_value ) {
			$processed_mime_types[ $prefix ] = __( 'All', 'bulk-delete' ) . ' ' . $prefix;
			$last_value                      = $prefix;
		}

		$processed_mime_types[ $value ] = $value;
	}

	return $processed_mime_types;
}

/**
 * Get current theme name.
 *
 * @since 5.5.4
 *
 * @return string Current theme name.
 */
function bd_get_current_theme_name() {
	if ( get_bloginfo( 'version' ) < '3.4' ) {
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );

		return $theme_data['Name'] . ' ' . $theme_data['Version'];
	} else {
		$theme_data = wp_get_theme();

		return $theme_data->Name . ' ' . $theme_data->Version;
	}
}

/**
 * Try to identity the hosting provider.
 *
 * @since 5.5.4
 *
 * @return string Web host name if identified, empty string otherwise.
 */
function bd_identify_host() {
	$host = '';
	if ( defined( 'WPE_APIKEY' ) ) {
		$host = 'WP Engine';
	} elseif ( defined( 'PAGELYBIN' ) ) {
		$host = 'Pagely';
	}

	return $host;
}

/**
 * Print plugins that are currently active.
 *
 * @since 5.5.4
 */
function bd_print_current_plugins() {
	$plugins        = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	foreach ( $plugins as $plugin_path => $plugin ) {
		// If the plugin isn't active, don't show it.
		if ( ! in_array( $plugin_path, $active_plugins ) ) {
			continue;
		}

		echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
	}
}

/**
 * Print network active plugins.
 *
 * @since 5.5.4
 */
function bd_print_network_active_plugins() {
	$plugins        = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

	foreach ( $plugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );

		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}

		$plugin = get_plugin_data( $plugin_path );

		echo $plugin['Name'] . ' :' . $plugin['Version'] . "\n";
	}
}

/**
 * Print scheduled jobs.
 *
 * @since 6.0
 */
function bd_print_scheduled_jobs() {
	$cron        = _get_cron_array();
	$date_format = _x( 'M j, Y @ G:i', 'Cron table date format', 'bulk-delete' );

	foreach ( $cron as $timestamp => $cronhooks ) {
		foreach ( (array) $cronhooks as $hook => $events ) {
			if ( 'do-bulk-delete-' === substr( $hook, 0, 15 ) ) {
				foreach ( (array) $events as $key => $event ) {
					echo date_i18n( $date_format, $timestamp + ( get_option( 'gmt_offset' ) * 60 * 60 ) ) . ' (' . $timestamp . ')';
					echo ' | ';
					echo $event['schedule'];
					echo ' | ';
					echo $hook;
					echo "\n";
				}
			}
		}
	}
}
