<?php
/**
 * Utility class for deleting users
 *
 * @package Bulk Delete
 * @author Sudar
 */
class Bulk_Delete_Users {

    /**
     * Delete users by user role
     */
    public static function delete_users_by_role( $delete_options ) {

        if( !function_exists( 'wp_delete_user' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/user.php' );
        }

        $count = 0;

        foreach ( $delete_options['selected_roles'] as $role ) {

            $options = array();
            $options['role'] = $role;
            $users = get_users( $options );

            foreach ( $users as $user ) {
                if ( $delete_options['no_posts'] == TRUE && count_user_posts ( $user->ID ) > 0 ) {
                    continue;
                }

                if ( $delete_options['login_restrict'] == TRUE ) {
                    $login_days = $delete_options['login_days'];
                    $last_login = self::get_last_login( $user->ID );

                    if ( $last_login != NULL ) {
                        if ( strtotime( $last_login ) > strtotime( '-' . $login_days . 'days' ) ) {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }

                wp_delete_user( $user->ID );
                $count ++;
            }
        }
        
        return $count;
    }

    /**
     * Find the last login date/time of a user
     */
    private static function get_last_login( $user_id ) {
        global $wpdb;

        return $wpdb->get_var( $wpdb->prepare( "SELECT time FROM {$wpdb->prefix}" . Bulk_Delete_Util::SIMPLE_LOGIN_LOG_TABLE . 
                    " WHERE uid = %d ORDER BY time DESC LIMIT 1", $user_id ) );
    }
}
?>
