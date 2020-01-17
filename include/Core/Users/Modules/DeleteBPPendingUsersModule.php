<?php

namespace BulkWP\BulkDelete\Core\Users\Modules;

use BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'Buddy Press' ) ) {
	return;
}

/**
 * Bulk Delete Buddy Press pending users.
 *
 * @since 6.2.0.
 */
class DeleteBPPendingUsersModule extends DeleteUsersByUserMetaModule {
	/**
	 * Initialize and setup variables.
	 */
	protected function initialize() {
		$this->item_type     = 'users';
		$this->field_slug    = 'u_meta';
		$this->meta_box_slug = 'bd_bp_users_by_meta';
		$this->action        = 'delete_users_by_meta';
		$this->messages      = array(
			'box_label'         => __( 'Delete Pending Users', 'bulk-delete' ),
			'confirm_deletion'  => __( 'Are you sure you want to delete all the Buddy Press pending users?', 'bulk-delete' ),
			'confirm_scheduled' => __( 'Are you sure you want to schedule deletion for all the users?', 'bulk-delete' ),
			/* translators: 1 Number of users deleted */
			'deleted_one'       => __( 'Deleted %d Buddy Press pending user', 'bulk-delete' ),
			/* translators: 1 Number of users deleted */
			'deleted_multiple'  => __( 'Deleted %d Buddy Press pending users', 'bulk-delete' ),
		);
	}
	/**
	 * Render delete users box.
	 */
	public function render() {
		$options = array(
			'fields'       => 'ids',
			'meta_key'     => '_bprwg_is_moderated',
			'meta_value'   => 'true',
			'meta_compare' => '=',
		);

		$users = $this->query_users( $options );
		$count = count( $users );
		?>
		<!-- Users Start-->
		<h4><?php _e( 'Delete ' . esc_attr( $count ) . ' pending users signed up via buddy press', 'bulk-delete' ); ?></h4>

		<fieldset class="options">
			<table class="optiontable">
				<?php
				$this->render_filtering_table_header();
				$this->render_user_login_restrict_settings( false );
				?>
			</table>
		</fieldset>
		<!-- Users end-->

		<?php
		$this->render_submit_button();
	}

	/**
	 * Process user input and create metabox options.
	 *
	 * @param array $request Request array.
	 * @param array $options User options.
	 *
	 * @return array User options.
	 */
	protected function convert_user_input_to_options( $request, $options ) {
		$options['meta_key']     = '_bprwg_is_moderated';
		$options['meta_compare'] = '=';
		$options['meta_value']   = 'true';

		return $options;
	}
}
