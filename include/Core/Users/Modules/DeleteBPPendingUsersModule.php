<?php

namespace BulkWP\BulkDelete\Core\Users\Modules;

use BulkWP\BulkDelete\Core\Users\UsersModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Buddy Press pending users.
 *
 * @since 6.2.0.
 */
class DeleteBPPendingUsersModule extends UsersModule {
	/**
	 * Initialize and setup variables.
	 */
	protected function initialize() {
		$this->item_type     = 'users';
		$this->field_slug    = 'bp_pending_user';
		$this->meta_box_slug = 'bd_bp_users_by_meta';
		$this->action        = 'delete_bp_pending_users';
		$this->messages      = array(
			'box_label'        => __( 'Delete Pending Users', 'bulk-delete' ),
			'confirm_deletion' => __( 'Are you sure you want to delete all the Buddy Press pending users?', 'bulk-delete' ),
			/* translators: 1 Number of users deleted */
			'deleted_one'      => __( 'Deleted %d Buddy Press pending user', 'bulk-delete' ),
			/* translators: 1 Number of users deleted */
			'deleted_multiple' => __( 'Deleted %d Buddy Press pending users', 'bulk-delete' ),
		);
	}

	/**
	 * Render delete users box.
	 */
	public function render() {
		$count = \BP_Signup::count_signups();
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

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'noValidation';

		return $js_array;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		// Left empty on purpose.
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function do_delete( $options ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'users';
		$count      = 0;
		$date_query = '';

		if ( $options['registered_restrict'] ) {
			$date_query = $this->get_date_query( $options );
		}

		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$table_name} WHERE user_status = %d {$date_query}", 2 ) );

		foreach ( $ids as $id ) {
			$deleted = wp_delete_user( $id );
			if ( $deleted ) {
				$count++;
			}
		}

		$table_name = $wpdb->prefix . 'signups';
		$date_query = str_replace( 'user_registered', 'registered', $date_query );
		$status     = $wpdb->get_col( $wpdb->prepare( "DELETE FROM {$table_name} WHERE active = %d {$date_query}", 0 ) );

		return $count;
	}

	/**
	 * Returns date query.
	 *
	 * @param array $options Delete Options.
	 *
	 * @return string $date_query
	 */
	protected function get_date_query( $options ) {
		$date_query = '';
		switch ( $options['registered_date_op'] ) {
			case 'before':
				$operator   = '<';
				$date       = date( 'Y-m-d', strtotime( '-' . $options['registered_days'] . ' days' ) );
				$date_query = 'AND user_registered ' . $operator . " '" . $date . "'";
				break;
			case 'after':
				$operator   = 'between';
				$start_date = date( 'Y-m-d', strtotime( '-' . $options['registered_days'] . ' days' ) );
				$end_date   = date( 'Y-m-d', strtotime( 'now' ) );
				$date_query = 'AND user_registered ' . $operator . " '" . $start_date . "' AND '" . $end_date . "'";
				break;
		}

		return $date_query;
	}
}
