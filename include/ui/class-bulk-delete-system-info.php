<?php
/**
 * System Info
 *
 * These are functions are used for exporting data from Bulk Delete.
 *
 * @since       5.0
 * @note        Based on the code from Easy Digital Downloads plugin
 * @author		Sudar
 * @package     BulkDelete\Admin
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Encapsulates System info
 *
 * @since 5.0
 */
class Bulk_Delete_System_Info {

	/**
	 * Shows the system info panel which contains version data and debug info.
	 * The data for the system info is generated by the Browser class.
	 *
	 * @since 5.0
	 * @static
	 * @global $wpdb - global object $wpdb Used to query the database using the WordPress Database API
	 * @return void
	 */
	public static function display_system_info() {
		global $wpdb;

		if ( get_bloginfo( 'version' ) < '3.4' ) {
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$theme      = $theme_data['Name'] . ' ' . $theme_data['Version'];
		} else {
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name . ' ' . $theme_data->Version;
		}

		// Try to identity the hosting provider
		$host = false;
		if ( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
		} elseif ( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		}
?>
<div class="wrap">
    <h2><?php _e( 'System Information', 'bulk-delete' ); ?></h2>
    <?php settings_errors(); ?>

    <form action="<?php echo esc_url( admin_url( 'admin.php?page=' . Bulk_Delete::INFO_PAGE_SLUG ) ); ?>" method="post">
    <div id = "poststuff">
        <div id="post-body" class="metabox-holder columns-2">

			<div class="updated" >
				<p><strong><?php _e( 'Please include this information when posting support requests.', 'bulk-delete' ); ?></strong></p>
			</div>

            <div id="postbox-container-1" class="postbox-container">
                <iframe frameBorder="0" height = "1500" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option( 'admin_color' ); ?>&version=<?php echo Bulk_Delete::VERSION; ?>"></iframe>
            </div>

            <div id="postbox-container-2" class="postbox-container">

            <textarea wrap="off" style="width:100%;height:500px;font-family:Menlo,Monaco,monospace;white-space:pre;" readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="bulk-delete-sysinfo" title="<?php _e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'bulk-delete' ); ?>">
### Begin System Info ###
<?php
		do_action( 'bd_system_info_before' );
?>

Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

SITE_URL:                 <?php echo site_url() . "\n"; ?>
HOME_URL:                 <?php echo home_url() . "\n"; ?>
Browser:                  <?php echo esc_html( $_SERVER['HTTP_USER_AGENT'] ), "\n"; ?>

Permalink Structure:      <?php echo get_option( 'permalink_structure' ) . "\n"; ?>
Active Theme:             <?php echo $theme . "\n"; ?>
GMT Offset:               <?php echo esc_html( get_option( 'gmt_offset' ) ), "\n\n"; ?>
<?php
		if ( false !== $host ) { ?>
Host:                     <?php echo $host . "\n\n"; ?>
<?php
		}

		$post_types = get_post_types();
?>
Registered Post types:    <?php echo implode( ', ', $post_types ) . "\n"; ?>
<?php
		foreach ( $post_types as $post_type ) {
			echo $post_type;
			if ( strlen( $post_type ) < 26 ) {
				echo str_repeat( ' ', 26 - strlen( $post_type ) );
			}
			$post_count = wp_count_posts( $post_type );
			foreach ( $post_count as $key => $value ) {
				echo $key, '=', $value, ', ';
			}
			echo "\n";
		}
?>

Bulk Delete Version:      <?php echo Bulk_Delete::VERSION . "\n"; ?>
WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php echo $wpdb->db_version() . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress Memory Limit:   <?php echo WP_MEMORY_LIMIT; ?><?php echo "\n"; ?>
PHP Memory Limit:         <?php echo ini_get( 'memory_limit' ) . "\n"; ?>

WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
DISABLE_WP_CRON:          <?php echo defined( 'DISABLE_WP_CRON' ) ? DISABLE_WP_CRON ? 'Yes' . "\n" : 'No' . "\n" : 'Not set' . "\n" ?>
EMPTY_TRASH_DAYS:         <?php echo defined( 'EMPTY_TRASH_DAYS' ) ? EMPTY_TRASH_DAYS : 'Not set', "\n" ?>

PHP Safe Mode:            <?php echo ini_get( 'safe_mode' ) ? 'Yes' : 'No', "\n"; ?>
PHP Upload Max Size:      <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Upload Max Filesize:  <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
PHP Max Input Vars:       <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
PHP Arg Separator:        <?php echo ini_get( 'arg_separator.output' ) . "\n"; ?>
PHP Allow URL File Open:  <?php echo ini_get( 'allow_url_fopen' ) ? 'Yes' : 'No', "\n"; ?>

WP Table Prefix:          <?php echo 'Length: '. strlen( $wpdb->prefix ), "\n";?>

Session:                  <?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:             <?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
Cookie Path:              <?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
Save Path:                <?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
Use Cookies:              <?php echo ini_get( 'session.use_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>
Use Only Cookies:         <?php echo ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>

DISPLAY ERRORS:           <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
FSOCKOPEN:                <?php echo ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
cURL:                     <?php echo ( function_exists( 'curl_init' ) ) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>
SOAP Client:              <?php echo ( class_exists( 'SoapClient' ) ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>
SUHOSIN:                  <?php echo ( extension_loaded( 'suhosin' ) ) ? 'Your server has SUHOSIN installed.' : 'Your server does not have SUHOSIN installed.'; ?><?php echo "\n"; ?>

ACTIVE PLUGINS:

<?php
		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) ) {
				continue;
			}

			echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
		}

		if ( is_multisite() ) {
?>

NETWORK ACTIVE PLUGINS:

<?php
			$plugins = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );

				// If the plugin isn't active, don't show it.
				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}

				$plugin = get_plugin_data( $plugin_path );

				echo $plugin['Name'] . ' :' . $plugin['Version'] ."\n";
			}
		}
		do_action( 'bd_system_info_after' );
?>
### End System Info ###</textarea>
            <p class="submit">
                <input type="hidden" name="bd_action" value="download_sysinfo">
                <?php submit_button( 'Download System Info File', 'primary', 'bulk-delete-download-sysinfo', false ); ?>
            </p>
            </div> <!-- #postbox-container-2 -->
        </div> <!-- #post-body -->
    </div><!-- #poststuff -->
    </form>
</div><!-- .wrap -->
<?php
		/**
		 * Runs just before displaying the footer text in the "System Info" admin page.
		 *
		 * This action is primarily for adding extra content in the footer of "System Info" admin page.
		 *
		 * @since 5.0
		 */
		do_action( 'bd_admin_footer_info_page' );
	}

	/**
	 * Generates the System Info Download File
	 *
	 * @since 5.0
	 * @return void
	 */
	public static function generate_sysinfo_download() {
		nocache_headers();

		header( 'Content-type: text/plain' );
		header( 'Content-Disposition: attachment; filename="bulk-delete-system-info.txt"' );

		echo wp_strip_all_tags( $_POST['bulk-delete-sysinfo'] );
		die();
	}
}

add_action( 'bd_download_sysinfo', array( 'Bulk_Delete_System_Info', 'generate_sysinfo_download' ) );
?>
