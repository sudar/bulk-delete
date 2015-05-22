<?php
/**
 * Bulk Delete Users by User Role
 *
 * @since   5.5
 * @author  Sudar
 * @package BulkDelete\Users\Modules
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Bulk Delete Users by User Role.
 * 
 * @since 5.5
 */
class Bulk_Delete_Users_By_User_Role extends BD_Meta_Box_Module {
	/**
	 * Make this class a "hybrid Singleton".
	 *
	 * @static
	 * @since 5.5
	 */
	public static function factory() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Initialize and setup variables.
	 *
	 * @since 5.5
	 */
	protected function initialize() {
		$this->item_type     = 'users';
		$this->field_slug    = 'u_role';
		$this->meta_box_slug = 'bd_users_by_role';
		$this->meta_box_hook = 'bd_add_meta_box_for_users';
		$this->delete_action = 'delete_users_by_role';
		$this->cron_hook     = 'do-bulk-delete-users-by-role';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-users-by-role/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-u-ur';
		$this->messages = array(
			'box_label'      => __( 'By User Role', 'bulk-delete' ),
			'scheduled'      => __( 'Users from the selected userrole are scheduled for deletion.', 'bulk-delete' ),
			'deleted_single' => __( 'Deleted %d user from the selected roles', 'bulk-delete' ),
			'deleted_plural' => __( 'Deleted %d users from the selected roles', 'bulk-delete' ),
		);
	}

	/**
	 * Render delete users box.
	 * 
	 * @since 5.5
	 */
	public function render() {
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
                    <input name="smbd_u_roles[]" value = "<?php echo $role; ?>" type = "checkbox">
                    <label for="smbd_u_roles"><?php echo $role; ?> (<?php echo $count . ' '; _e( 'Users', 'bulk-delete' ); ?>)</label>
                </td>
            </tr>
<?php
		}
?>
		</table>

        <table class="optiontable">
<?php
			$this->render_filtering_table_header();
		if ( ! BD_Util::is_simple_login_log_present() ) {
			$disabled = 'disabled';
		} else {
			$disabled = '';
		}
?>
            <tr>
                <td scope="row" colspan="2">
                <input name="smbd_u_login_restrict" id="smbd_u_login_restrict" value = "true" type = "checkbox" <?php echo $disabled; ?>>
                    <?php _e( 'Only restrict to users who have not logged in the last ', 'bulk-delete' );?>
                    <input type="number" name="smbd_u_login_days" id="smbd_u_login_days" class="screen-per-page" value="0" min="0" <?php echo $disabled; ?>> <?php _e( 'days', 'bulk-delete' );?>
<?php
		if ( ! BD_Util::is_simple_login_log_present() ) {
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
                <td scope="row" colspan="2">
                    <input name="smbd_u_role_no_posts" id="smbd_u_role_no_posts" value="true" type="checkbox">
                    <?php _e( "Only if user doesn't have any post. Only posts from 'post' post type would be considered.", 'bulk-delete' ); ?>
                </td>
            </tr>

			<?php $this->render_limit_settings(); ?>
			<?php $this->render_cron_settings(); ?>

        </table>
        </fieldset>
        <!-- Users end-->
<?php
		$this->render_submit_button();
	}

	/**
	 * Process the request for deleting users by role.
	 *
	 * @since 5.5
	 */
	public function process() {
		$delete_options                   = array();
		$delete_options['selected_roles'] = array_get( $_POST, 'smbd_u_roles' );
		$delete_options['no_posts']       = array_get_bool( $_POST, 'smbd_u_role_no_posts', false );

		$delete_options['login_restrict'] = array_get_bool( $_POST, 'smbd_u_login_restrict', false );
		$delete_options['login_days']     = absint( array_get( $_POST, 'smbd_u_login_days' ) );
		$delete_options['limit_to']       = absint( array_get( $_POST, 'smbd_u_role_limit_to', 0 ) );

		$this->process_delete( $delete_options );
	}

	/**
	 * Delete users by user role.
	 *
	 * @since 5.5
	 * @param array $delete_options Delete Options
	 * @return int  Number of users deleted
	 */
	public function delete( $delete_options ) {
		if ( ! function_exists( 'wp_delete_user' ) ) {
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
				if ( $delete_options['no_posts'] == true && count_user_posts ( $user->ID ) > 0 ) {
					continue;
				}

				if ( $delete_options['login_restrict'] == true ) {
					$login_days = $delete_options['login_days'];
					$last_login = $this->get_last_login( $user->ID );

					if ( null != $last_login ) {
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
	 * @since 5.5
	 * @param array  $js_array JavaScript Array
	 * @return array           Modified JavaScript Array
	 */
	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][] = '_u_role';

		$js_array['pre_action_msg']['delete_users_by_role'] = 'deleteUsersWarning';
		$js_array['msg']['deleteUsersWarning'] = __( 'Are you sure you want to delete all the users from the selected user role?', 'bulk-delete' );

		$js_array['error_msg']['delete_users_by_role'] = 'selectOneUserRole';
		$js_array['msg']['selectOneUserRole'] = __( 'Select at least one user role from which users should be deleted', 'bulk-delete' );

		return $js_array;
	}

	/**
	 * Find the last login date/time of a user.
	 *
	 * @since 5.5
	 * @access private
	 * @param int $user_id
	 * @return string
	 */
	private function get_last_login( $user_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT time FROM {$wpdb->prefix}" . BD_Util::SIMPLE_LOGIN_LOG_TABLE .
				' WHERE uid = %d ORDER BY time DESC LIMIT 1', $user_id ) );
	}
}

Bulk_Delete_Users_By_User_Role::factory();
?>
