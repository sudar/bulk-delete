<?php
/**
 * Utility class for deleting users
 *
 * @package Bulk Delete
 * @author Sudar
 */
class Bulk_Delete_Users {

    /**
     * Render delete users box
     */
    function render_delete_users_box() {

        if ( Bulk_Delete_Util::is_users_box_hidden( Bulk_Delete::BOX_USERS ) ) {
            printf( __('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'tools.php?page=' . Bulk_Delete::USERS_PAGE_SLUG );
            return;
        }
?>
        <!-- Users Start-->
        <h4><?php _e("Select the user roles from which you want to delete users", 'bulk-delete'); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
<?php
        $users_count = count_users();
        foreach( $users_count['avail_roles'] as $role => $count ) {
?>
            <tr>
                <td scope="row" >
                    <input name="smbdu_roles[]" value = "<?php echo $role; ?>" type = "checkbox" />
                </td>
                <td>
                    <label for="smbdu_roles"><?php echo $role; ?> (<?php echo $count . " "; _e("Users", 'bulk-delete'); ?>)</label>
                </td>
            </tr>
<?php
        }
?>
            <tr>
                <td colspan="2">
                    <h4><?php _e("Choose your filtering options", 'bulk-delete'); ?></h4>
                </td>
            </tr>

<?php 
        if ( !Bulk_Delete_Util::is_simple_login_log_present() ) {
            $disabled = "disabled";
        } else {
            $disabled = '';
        }
?>
            <tr>
                <td scope="row">
                <input name="smbdu_login_restrict" id="smbdu_login_restrict" value = "true" type = "checkbox" <?php echo $disabled; ?> >
                </td>
                <td>
                    <?php _e("Only restrict to users who have not logged in the last ", 'bulk-delete');?>
                    <input type ="textbox" name="smbdu_login_days" id="smbdu_login_days" value ="0" maxlength="4" size="4" <?php echo $disabled; ?> ><?php _e("days", 'bulk-delete');?>
<?php 
        if ( !Bulk_Delete_Util::is_simple_login_log_present() ) {
?>
                    <span style = "color:red">
                        <?php _e('Need Simple Login Log Plugin', 'bulk-delete'); ?> <a href = "http://wordpress.org/plugins/simple-login-log/">Install now</a>
                    </span>
<?php
        }
?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbdu_role_no_posts" id="smbdu_role_no_posts" value = "true" type = "checkbox" >
                </td>
                <td>
                    <?php _e( "Only if user doesn't have any post", 'bulk-delete' ); ?>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <input name="smbdu_userrole_cron" value = "false" type = "radio" checked="checked" /> <?php _e('Delete now', 'bulk-delete'); ?>
                    <input name="smbdu_userrole_cron" value = "true" type = "radio" id = "smbdu_userrole_cron" disabled > <?php _e('Schedule', 'bulk-delete'); ?>
                    <input name="smbdu_userrole_cron_start" id = "smbdu_userrole_cron_start" value = "now" type = "text" disabled><?php _e('repeat ', 'bulk-delete');?>
                    <select name = "smbdu_userrole_cron_freq" id = "smbdu_userrole_cron_freq" disabled>
                        <option value = "-1"><?php _e("Don't repeat", 'bulk-delete'); ?></option>
<?php
        $schedules = wp_get_schedules();
        foreach($schedules as $key => $value) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php                        
        }
?>
                    </select>
                    <span class = "bdu-users-by-role-pro" style = "color:red"><?php _e('Only available in Pro Addon', 'bulk-delete'); ?> <a href = "http://sudarmuthu.com/out/buy-bulk-delete-users-by-role-addon">Buy now</a></span>
                </td>
            </tr>

            <tr>
                <td scope="row" colspan="2">
                    <?php _e("Enter time in Y-m-d H:i:s format or enter now to use current time", 'bulk-delete');?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="smbdu_action" value = "bulk-delete-users-by-role" class="button-primary"><?php _e("Bulk Delete ", 'bulk-delete') ?>&raquo;</button>
        </p>
        <!-- Users end-->
<?php
    }

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
