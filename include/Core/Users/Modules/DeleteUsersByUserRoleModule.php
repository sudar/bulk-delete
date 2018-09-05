<?php

namespace BulkWP\BulkDelete\Core\Users\Modules;

use BulkWP\BulkDelete\Core\Users\UsersModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Users by User Role Module.
 *
 * @since 5.5
 * @since 6.0.0 Renamed to DeleteUsersByUserRoleModule
 */
class DeleteUsersByUserRoleModule extends UsersModule {
	/**
	 * Initialize and setup variables.
	 *
	 * @since 5.5
	 */
	protected function initialize() {
		$this->item_type     = 'users';
		$this->field_slug    = 'u_role';
		$this->meta_box_slug = 'bd_users_by_role';
		$this->action        = 'delete_users_by_role';
		$this->cron_hook     = 'do-bulk-delete-users-by-role';
		$this->scheduler_url = 'https://bulkwp.com/addons/scheduler-for-deleting-users-by-role/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-u-ur';
		$this->messages      = array(
			'box_label'  => __( 'By User Role', 'bulk-delete' ),
			'scheduled'  => __( 'Users from the selected user role are scheduled for deletion.', 'bulk-delete' ),
			'cron_label' => __( 'Delete Users by User Role', 'bulk-delete' ),
		);
	}

	/**
	 * Render delete users box.
	 *
	 * @since 5.5
	 */
	public function render() {
		?>
		<h4><?php _e( 'Select the user roles from which you want to delete users', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<?php $this->render_user_role_dropdown(); ?>
			</table>

			<table class="optiontable">
				<?php
					$this->render_filtering_table_header();
					$this->render_user_login_restrict_settings();
					$this->render_user_with_no_posts_settings();
					$this->render_limit_settings();
					$this->render_cron_settings();
				?>
			</table>
		</fieldset>
		<?php
		$this->render_submit_button();
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['selected_roles'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_roles', array() );

		return $options;
	}

	/**
	 * Build query params for WP_User_Query by using delete options.
	 *
	 * Return an empty query array to short-circuit deletion.
	 *
	 * @since 6.0.0
	 *
	 * @param array $options Delete options.
	 *
	 * @return array Query.
	 */
	protected function build_query( $options ) {
		$query = array(
			'role__in' => $options['selected_roles'],
			'number'   => $options['limit_to'],
		);

		return $query;
	}

	/**
	 * Filter JS Array and add validation hooks.
	 *
	 * @since 5.5
	 *
	 * @param array $js_array JavaScript Array.
	 *
	 * @return array Modified JavaScript Array
	 */
	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][]              = '_' . $this->field_slug;
		$js_array['validators'][ $this->action ] = 'validateUserRole';

		$js_array['pre_action_msg'][ $this->action ] = 'deleteUsersWarning';
		$js_array['msg']['deleteUsersWarning']       = __( 'Are you sure you want to delete all the users from the selected user role?', 'bulk-delete' );

		$js_array['error_msg'][ $this->action ] = 'selectOneUserRole';
		$js_array['msg']['selectOneUserRole']   = __( 'Select at least one user role from which users should be deleted', 'bulk-delete' );

		return $js_array;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of users deleted */
		return _n( 'Deleted %d user from the selected roles', 'Deleted %d users from the selected roles', $items_deleted, 'bulk-delete' );
	}
}
