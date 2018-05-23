<?php
namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\BulkDelete\Core\Metas\MetasModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete User Meta.
 *
 * @since 6.0.0
 */
class DeleteUserMetaModule extends MetasModule {
	protected function initialize() {
		$this->field_slug    = 'user_comment';
		$this->meta_box_slug = 'bd-user-meta';
		$this->action        = 'delete_user_comment';
		$this->cron_hook     = 'do-bulk-delete-user-meta';
		$this->messages      = array(
			'box_label'  => __( 'Bulk Delete User Meta', 'bulk-delete' ),
			'scheduled'  => __( 'User meta fields from the users with the selected criteria are scheduled for deletion.', 'bulk-delete' ),
			'cron_label' => __( 'Delete User Meta`', 'bulk-delete' ),
		);
	}

	/**
	 * Render the Modules.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<!-- User Meta box start-->
        <fieldset class="options">
        <h4><?php _e( 'Select the user role whose user meta fields you want to delete', 'bulk-delete' ); ?></h4>
        <table class="optiontable">
<?php
$this->render_user_role_dropdown();
?>
        </table>

        <h4><?php _e( 'Choose your user meta field settings', 'bulk-delete' ); ?></h4>
        <table class="optiontable">
            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" value="false" type="radio" checked>
                    <label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on user meta key name only', 'bulk-delete' ); ?></label>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" value="true" type="radio" disabled>
                    <label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on user meta key name and value', 'bulk-delete' ); ?></label>
                    <span class="bd-um-pro" style="color:red; vertical-align: middle;">
                        <?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/bulk-delete-user-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-m-u" target="_blank">Buy now</a>
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key"><?php _e( 'User Meta Key ', 'bulk-delete' ); ?></label>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key" placeholder="<?php _e( 'Meta Key', 'bulk-delete' ); ?>">
                </td>
            </tr>
        </table>
<?php
		/**
		 * Add more fields to the delete user meta field form.
		 * This hook can be used to add more fields to the delete user meta field form.
		 *
		 * @since 5.4
		 */
		do_action( 'bd_delete_user_meta_form' );
?>
        <table class="optiontable">
            <tr>
                <td>
                    <h4><?php _e( 'Choose your deletion options', 'bulk-delete' ); ?></h4>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit" value = "true" type = "checkbox">
                    <?php _e( 'Only delete user meta field from first ', 'bulk-delete' );?>
                    <input type ="textbox" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit_to" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit_to" disabled value ="0" maxlength="4" size="4"><?php _e( 'users.', 'bulk-delete' );?>
                    <?php _e( 'Use this option if there are more than 1000 users and the script times out.', 'bulk-delete' ) ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron" value = "false" type = "radio" checked="checked"> <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron" value = "true" type = "radio" id = "smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron_start" id = "smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron_freq" id = "smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron_freq" disabled>
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
                    <span class="bd-um-pro" style="color:red">
                        <?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/bulk-delete-user-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-m-u">Buy now</a>
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <?php _e( 'Enter time in Y-m-d H:i:s format or enter now to use current time', 'bulk-delete' );?>
                </td>
            </tr>

        </table>
        </fieldset>

        <?php $this->render_submit_button(); ?>
        </p>
        <!-- User Meta box end-->
		<?php
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_type'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_post_type', 'post' ) );

		$options['use_value'] = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_use_value', false );
		$options['meta_key']  = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_meta_key', '' ) );

		return $options;
	}

	public function delete( $options ) {
		print_r($options);
		$count     = 0;
		$user_role = $options['user_role'];
		$meta_key  = $options['meta_key'];
		$use_value = $options['use_value'];
		$limit_to  = $options['limit_to'];

		$options = array(
			'role' => $user_role,
		);

		if ( $limit_to > 0 ) {
			$options['number'] = $limit_to;
		}

		if ( $use_value ) {
			$options['meta_query'] = apply_filters( 'bd_delete_user_meta_query', array(), $options );
		} else {
			$options['meta_key'] = $meta_key;
		}

		$users = get_users( $options );

		foreach ( $users as $user ) {
			if ( delete_user_meta( $user->ID, $meta_key ) ) {
				$count++;
			}
		}

		return $count;
	}

	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][]                 = '_' . $this->field_slug;
		$js_array['validators']['delete_meta_user'] = 'noValidation';

		$js_array['pre_action_msg']['delete_meta_user'] = 'deleteUMWarning';
		$js_array['msg']['deleteUMWarning']             = __( 'Are you sure you want to delete all the user meta fields that match the selected filters?', 'bulk-delete' );

		return $js_array;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted user meta field from %d user', 'Deleted user meta field from %d users', $items_deleted, 'bulk-delete' );
	}
}
