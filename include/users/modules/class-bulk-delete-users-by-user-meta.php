<?php
/**
 * Bulk Delete Users by User Meta.
 *
 * @since   5.5
 * @author  Sudar
 * @package BulkDelete\Users\Modules
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Bulk Delete Users by User Meta.
 *
 * @since 5.5
 */
class Bulk_Delete_Users_By_User_Meta extends BD_Meta_Box_Module {
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
		$this->field_slug    = 'u_meta';
		$this->meta_box_slug = 'bd_users_by_meta';
		$this->meta_box_hook = "bd_add_meta_box_for_{$this->item_type}";
		$this->delete_action = 'delete_users_by_meta';
		$this->cron_hook     = 'do-bulk-delete-users-by-meta';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-users-by-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-u-ma';
		$this->messages = array(
			'box_label'      => __( 'By User Meta', 'bulk-delete' ),
			'scheduled'      => __( 'Users from with the selected user meta are scheduled for deletion.', 'bulk-delete' ),
			'deleted_single' => __( 'Deleted %d user with the selected user meta', 'bulk-delete' ),
			'deleted_plural' => __( 'Deleted %d users with the selected user meta', 'bulk-delete' ),
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
        <h4><?php _e( 'Select the user meta from which you want to delete users', 'bulk-delete' ); ?></h4>

        <fieldset class="options">
        <table class="optiontable">
		<select name="smbd_u_meta_key">
<?php
		$meta_keys = $this->get_unique_meta_keys();
		foreach ( $meta_keys as $meta_key ) {
			printf( '<option value="%s">%s</option>', $meta_key, $meta_key );
		}
?>
		</select>
		<select name="smbd_u_meta_compare">
			<option value="=">=</option>
			<option value="!=">!=</option>
			<option value=">">></option>
			<option value=">=">>=</option>
			<option value="<"><</option>
			<option value="<="><=</option>
		</select>
		<input type="text" name="smbd_u_meta_value" id="smbd_u_meta_value" placeholder="<?php _e( 'Meta Value', 'bulk-delete' );?>">

		</table>

        <table class="optiontable">
<?php
		$this->render_filtering_table_header();
		if ( ! bd_is_simple_login_log_present() ) {
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
		if ( ! bd_is_simple_login_log_present() ) {
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
                    <input name="smbd_u_meta_no_posts" id="smbd_u_meta_no_posts" value="true" type="checkbox">
                    <?php _e( "Only if user doesn't have any post. Only posts from 'post' post type would be considered.", 'bulk-delete' ); ?>
                </td>
            </tr>

			<?php $this->render_limit_settings(); ?>
			<?php $this->render_cron_settings(); ?>

        </table>
        </fieldset>
        <!-- Users end-->
<?php
		$this->render_submit_button( $this->delete_action );
	}

	/**
	 * Process the request for deleting users by meta.
	 *
	 * @since 5.5
	 */
	public function process() {
		$delete_options                   = array();
		$delete_options['meta_key']       = array_get( $_POST, 'smbd_u_meta_key' );
		$delete_options['meta_compare']   = array_get( $_POST, 'smbd_u_meta_compare', '=' );
		$delete_options['meta_value']     = array_get( $_POST, 'smbd_u_meta_value' );

		$delete_options['no_posts']       = array_get_bool( $_POST, 'smbd_u_meta_no_posts', false );
		$delete_options['login_restrict'] = array_get_bool( $_POST, 'smbd_u_login_restrict', false );
		$delete_options['login_days']     = absint( array_get( $_POST, 'smbd_u_login_days', 0 ) );

		$delete_options['limit_to']       = absint( array_get( $_POST, 'smbd_u_meta_limit_to', 0 ) );

		$this->process_delete( $delete_options );
	}

	/**
	 * Delete users by user meta.
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

		$options = array(
			'meta_key'     => $delete_options['meta_key'],
			'meta_value'   => $delete_options['meta_value'],
			'meta_compare' => $delete_options['meta_compare'],
		);

		if ( $delete_options['limit_to'] > 0 ) {
			$options['number'] = $delete_options['limit_to'];
		}

		$users = get_users( $options );

		foreach ( $users as $user ) {
			if ( $delete_options['no_posts'] && count_user_posts( $user->ID ) > 0 ) {
				continue;
			}

			if ( $delete_options['login_restrict'] ) {
				$login_days = $delete_options['login_days'];
				$last_login = bd_get_last_login( $user->ID );

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

		return $count;
	}

	/**
	 * Filter JS Array and add validation hooks.
	 *
	 * @since 5.5
	 * @param array  $js_array JavaScript Array
	 * @return array           Modified JavaScript Array
	 */
	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][] = '_u_meta';
        $js_array['validators'][ $this->delete_action ] = 'validateUserMeta';

		$js_array['pre_action_msg'][ $this->delete_action ] = 'deleteUsersByMetaWarning';
		$js_array['msg']['deleteUsersByMetaWarning'] = __( 'Are you sure you want to delete all the users from the selected user meta?', 'bulk-delete' );

        $js_array['error_msg'][ $this->delete_action ] = 'enterUserMetaValue';
        $js_array['msg']['enterUserMetaValue'] = __( 'Please enter the value for the user meta field based on which you want to delete users', 'bulk-delete' );

		return $js_array;
	}

	private function get_unique_meta_keys() {
		global $wpdb;

		return $wpdb->get_col( "SELECT DISTINCT(meta_key) FROM {$wpdb->prefix}usermeta ORDER BY meta_key" );
	}
}

Bulk_Delete_Users_By_User_Meta::factory();
?>
