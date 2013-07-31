<?php
/**
 * Utility class
 *
 * @package Bulk Delete
 * @author Sudar
 */
class Bulk_Delete_Util {

    // simple login log
    const SIMPLE_LOGIN_LOG_TABLE = 'simple_login_log';

    /**
     * Find out if Simple Login Log is installed or not
     * http://wordpress.org/plugins/simple-login-log/
     */
    public static function is_simple_login_log_present() {
        global $wpdb;

        if( $wpdb->get_row( "SHOW TABLES LIKE '{$wpdb->prefix}" . self::SIMPLE_LOGIN_LOG_TABLE . "'" ) ) {
            return TRUE;
        }

        return FALSE;
    }
}
?>
