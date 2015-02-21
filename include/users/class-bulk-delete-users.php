<?php
/**
 * Utility class for deleting users
 *
 * @author     Sudar
 * @package    BulkDelete
 */


class Bulk_Delete_Users {

	/**
	 * Render delete users box
	 */
	public static function render_delete_users_by_role_box() {

		if ( BD_Util::is_users_box_hidden( Bulk_Delete::BOX_USERS ) ) {
			printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::USERS_PAGE_SLUG );
			return;
		}
?>
        <!-- Users Start-->
        <h4><?php _e( 'Select the user roles from which you want to delete users', 'bulk-delete' ); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
<?php
		$users_count = count_users();
		foreach ( $users_count['avail_roles'] as $role => $count ) {
?>
            <tr>
                <td scope="row" >
                    <input name="smbdu_roles[]" value = "<?php echo $role; ?>" type = "checkbox">
                    <label for="smbdu_roles"><?php echo $role; ?> (<?php echo $count . " "; _e( 'Users', 'bulk-delete' ); ?>)</label>
                </td>
            </tr>
<?php
		}
?>
            <tr>
                <td scope="row">
                    <h4><?php _e( 'Choose your filtering options', 'bulk-delete' ); ?></h4>
                </td>
            </tr>

<?php
		if ( !BD_Util::is_simple_login_log_present() ) {
			$disabled = "disabled";
		} else {
			$disabled = '';
		}
?>
            <tr>
                <td scope="row">
                <input name="smbdu_login_restrict" id="smbdu_login_restrict" value = "true" type = "checkbox" <?php echo $disabled; ?>>
                    <?php _e( 'Only restrict to users who have not logged in the last ', 'bulk-delete' );?>
                    <input type ="textbox" name="smbdu_login_days" id="smbdu_login_days" value ="0" maxlength="4" size="4" <?php echo $disabled; ?> ><?php _e( 'days', 'bulk-delete' );?>
<?php
		if ( !BD_Util::is_simple_login_log_present() ) {
?>
                    <span style = "color:red">
                        <?php _e( 'Need Simple Login Log Plugin', 'bulk-delete' ); ?> <a href = "http://wordpress.org/plugins/simple-login-log/">Install now</a>
                    </span>
<?php
		}
?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbdu_role_no_posts" id="smbdu_role_no_posts" value = "true" type = "checkbox">
                    <?php _e( "Only if user doesn't have any post. Only posts from 'post' post type would be considered.", 'bulk-delete' ); ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbdu_userrole_limit" id="smbdu_userrole_limit" value = "true" type = "checkbox">
                    <?php _e( 'Only delete first ', 'bulk-delete' );?>
					<input type="textbox" name="smbdu_userrole_limit_to" id="smbdu_userrole_limit_to" disabled value ="0" maxlength="4" size="4">
					<?php _e( 'users.', 'bulk-delete' );?>
                    <?php _e( 'Use this option if there are more than 1000 users or the script timesout.', 'bulk-delete' ) ?>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <input name="smbdu_userrole_cron" value = "false" type = "radio" checked="checked"> <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbdu_userrole_cron" value = "true" type = "radio" id = "smbdu_userrole_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbdu_userrole_cron_start" id = "smbdu_userrole_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name = "smbdu_userrole_cron_freq" id = "smbdu_userrole_cron_freq" disabled>
                        <option value = "-1"><?php _e( "Don't repeat", 'bulk-delete' ); ?></option>
<?php
		$schedules = wp_get_schedules();
		foreach ( $schedules as $key => $value ) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php
		}
?>
                    </select>
                    <span class = "bdu-users-by-role-pro" style = "color:red"><?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/scheduler-for-deleting-users-by-role/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-u-ur">Buy now</a></span>
                </td>
            </tr>

            <tr>
                <td scope="row">
                    <?php _e( 'Enter time in Y-m-d H:i:s format or enter now to use current time', 'bulk-delete' );?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="bd_action" value = "delete_users_by_role" class="button-primary"><?php _e( 'Bulk Delete ', 'bulk-delete' ) ?>&raquo;</button>
        </p>
        <!-- Users end-->
<?php
	}

	/**
	 * Process the request for deleting users by role
	 *
	 * @static
	 * @since 5.0
	 */
	public static function do_delete_users_by_role() {

		$delete_options = array();
		$delete_options['selected_roles']   = array_get( $_POST, 'smbdu_roles' );
		$delete_options['no_posts']         = array_get( $_POST, 'smbdu_role_no_posts', FALSE );

		$delete_options['login_restrict']   = array_get( $_POST, 'smbdu_login_restrict', FALSE );
		$delete_options['login_days']       = array_get( $_POST, 'smbdu_login_days' );
		$delete_options['limit_to']         = absint( array_get( $_POST, 'smbdu_userrole_limit_to', 0 ) );

		if ( array_get( $_POST, 'smbdu_userrole_cron', 'false' ) == 'true' ) {
			$freq = $_POST['smbdu_userrole_cron_freq'];
			$time = strtotime( $_POST['smbdu_userrole_cron_start'] ) - ( get_option( 'gmt_offset' ) * 60 * 60 );

			if ( $freq == -1 ) {
				wp_schedule_single_event( $time, Bulk_Delete::CRON_HOOK_USER_ROLE, array( $delete_options ) );
			} else {
				wp_schedule_event( $time, $freq , Bulk_Delete::CRON_HOOK_USER_ROLE, array( $delete_options ) );
			}

			$msg = __( 'Users from the selected userrole are scheduled for deletion.', 'bulk-delete' ) . ' ' .
				sprintf( __( 'See the full list of <a href = "%s">scheduled tasks</a>' , 'bulk-delete' ), get_bloginfo( "wpurl" ) . '/wp-admin/admin.php?page=' . Bulk_Delete::CRON_PAGE_SLUG );
		} else {
			$deleted_count = self::delete_users_by_role( $delete_options );
			$msg = sprintf( _n( 'Deleted %d user from the selected roles', 'Deleted %d users from the selected role' , $deleted_count, 'bulk-delete' ), $deleted_count );
		}

		add_settings_error(
			Bulk_Delete::USERS_PAGE_SLUG,
			'deleted-users',
			$msg,
			'updated'
		);
	}

	/**
	 * Delete users by user role
	 *
	 * @static
	 * @param unknown $delete_options
	 * @return integer
	 */
	public static function delete_users_by_role( $delete_options ) {

		if ( !function_exists( 'wp_delete_user' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$count = 0;

		foreach ( $delete_options['selected_roles'] as $role ) {

			$options = array();
			$options['role'] = $role;
			if ( $delete_options['limit_to'] > 0 ) {
				$options['number'] = $delete_options['limit_to'];
			}

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
	 * Filter JS Array and add validation hooks
	 *
	 * @since 5.4
	 * @static
	 * @param array   $js_array JavaScript Array
	 * @return array           Modified JavaScript Array
	 */
	public static function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][] = 'u_userrole';

		$js_array['pre_action_msg']['delete_users_by_role'] = 'deleteUsersWarning';
		$js_array['msg']['deleteUsersWarning'] = __( 'Are you sure you want to delete all the users from the selected user role?', 'bulk-delete' );

		$js_array['error_msg']['delete_users_by_role'] = 'selectOneUserRole';
		$js_array['msg']['selectOneUserRole'] = __( 'Select at least one user role from which users should be deleted', 'bulk-delete' );

		return $js_array;
	}

	/**
	 * Find the last login date/time of a user
	 *
	 * @static
	 * @access private
	 * @param unknown $user_id
	 * @return string
	 */
	private static function get_last_login( $user_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT time FROM {$wpdb->prefix}" . BD_Util::SIMPLE_LOGIN_LOG_TABLE .
				" WHERE uid = %d ORDER BY time DESC LIMIT 1", $user_id ) );
	}
}

add_action( 'bd_delete_users_by_role', array( 'Bulk_Delete_Users', 'do_delete_users_by_role' ) );
add_filter( 'bd_javascript_array', array( 'Bulk_Delete_Users' , 'filter_js_array' ) );
?>
