<?php

namespace BulkWP\BulkDelete\Core\Users;

use BulkWP\BulkDelete\Core\Base\BaseModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Encapsulates the Bulk Delete User Meta box Module Logic.
 * All Bulk Delete User Meta box Modules should extend this class.
 *
 * @see BaseModule
 * @since 5.5.2
 * @since 6.0.0 Renamed to UsersModule.
 */
abstract class UsersModule extends BaseModule {
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
	abstract protected function build_query( $options );

	/**
	 * Handle common filters.
	 *
	 * @param array $request Request array.
	 *
	 * @return array User options.
	 */
	protected function parse_common_filters( $request ) {
		$options = array();

		$options['login_restrict'] = bd_array_get_bool( $request, "smbd_{$this->field_slug}_login_restrict", false );
		$options['login_days']     = absint( bd_array_get( $request, "smbd_{$this->field_slug}_login_days", 0 ) );

		$options['registered_restrict'] = bd_array_get_bool( $request, "smbd_{$this->field_slug}_registered_restrict", false );
		$options['registered_days']     = absint( bd_array_get( $request, "smbd_{$this->field_slug}_registered_days", 0 ) );

		$options['no_posts']            = bd_array_get_bool( $request, "smbd_{$this->field_slug}_no_posts", false );
		$options['no_posts_post_types'] = bd_array_get( $request, "smbd_{$this->field_slug}_no_post_post_types", array() );

		$options['limit_to'] = absint( bd_array_get( $request, "smbd_{$this->field_slug}_limit_to", 0 ) );

		return $options;
	}

	protected function do_delete( $options ) {
		$query = $this->build_query( $options );

		if ( empty( $query ) ) {
			// Short circuit deletion, if nothing needs to be deleted.
			return 0;
		}

		return $this->delete_users_from_query( $query, $options );
	}

	/**
	 * Query and Delete users.
	 *
	 * @since  5.5.2
	 * @access protected
	 *
	 * @param array $query   Options to query users.
	 * @param array $options Delete options.
	 *
	 * @return int Number of users who were deleted.
	 */
	protected function delete_users_from_query( $query, $options ) {
		$count = 0;
		$users = $this->query_users( $query );

		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		foreach ( $users as $user ) {
			if ( ! $this->can_delete_by_registered_date( $options, $user ) ) {
				continue;
			}

			if ( ! $this->can_delete_by_logged_date( $options, $user ) ) {
				continue;
			}

			if ( ! $this->can_delete_by_post_count( $options, $user ) ) {
				continue;
			}

			$deleted = wp_delete_user( $user->ID );
			if ( $deleted ) {
				$count ++;
			}
		}

		return $count;
	}

	/**
	 * Query users using options.
	 *
	 * @param array $options Query options.
	 *
	 * @return \WP_User[] List of users.
	 */
	protected function query_users( $options ) {
		$defaults = array(
			'count_total' => false,
		);

		$options = wp_parse_args( $options, $defaults );

		$wp_user_query = new \WP_User_Query( $options );

		/**
		 * This action before the query happens.
		 *
		 * @since 6.0.0
		 *
		 * @param \WP_User_Query $wp_user_query Query object.
		 */
		do_action( 'bd_before_query', $wp_user_query );

		$users = (array) $wp_user_query->get_results();

		/**
		 * This action runs after the query happens.
		 *
		 * @since 6.0.0
		 *
		 * @param \WP_User_Query $wp_user_query Query object.
		 */
		do_action( 'bd_after_query', $wp_user_query );

		return $users;
	}

	/**
	 * Can the user be deleted based on the 'post count' option?
	 *
	 * @since  5.5.2
	 * @access protected
	 *
	 * @param array    $delete_options Delete Options.
	 * @param \WP_User $user           User object that needs to be deleted.
	 *
	 * @return bool True if the user can be deleted, false otherwise.
	 */
	protected function can_delete_by_post_count( $delete_options, $user ) {
		return ! (
			$delete_options['no_posts'] &&
			count_user_posts( $user->ID, $delete_options['no_posts_post_types'] ) > 0
		);
	}

	/**
	 * Can the user be deleted based on the 'registered date' option?
	 *
	 * @since  5.5.3
	 * @access protected
	 *
	 * @param array    $delete_options Delete Options.
	 * @param \WP_User $user           User object that needs to be deleted.
	 *
	 * @return bool True if the user can be deleted, false otherwise.
	 */
	protected function can_delete_by_registered_date( $delete_options, $user ) {
		if ( $delete_options['registered_restrict'] ) {
			$registered_days = $delete_options['registered_days'];

			if ( $registered_days > 0 ) {
				$user_meta = get_userdata( $user->ID );
				if ( strtotime( $user_meta->user_registered ) > strtotime( '-' . $registered_days . 'days' ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Can the user be deleted based on the 'logged in date' option?
	 *
	 * @since  5.5.2
	 * @access protected
	 *
	 * @param array    $delete_options Delete Options.
	 * @param \WP_User $user           User object that needs to be deleted.
	 *
	 * @return bool True if the user can be deleted, false otherwise.
	 */
	protected function can_delete_by_logged_date( $delete_options, $user ) {
		if ( $delete_options['login_restrict'] ) {
			$login_days = $delete_options['login_days'];
			$last_login = bd_get_last_login( $user->ID );

			if ( null !== $last_login ) {
				// we have a logged-in entry for the user in simple login log plugin.
				if ( strtotime( $last_login ) > strtotime( '-' . $login_days . 'days' ) ) {
					return false;
				}
			} else {
				// we don't have a logged-in entry for the user in simple login log plugin.
				if ( $login_days > 0 ) {
					// non-zero value for login date. So don't delete this user.
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Render User Login restrict settings.
	 *
	 * @since 5.5
	 */
	protected function render_user_login_restrict_settings() {
?>
		<tr>
			<td scope="row" colspan="2">
			<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_registered_restrict" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_registered_restrict" value="true" type="checkbox">
				<?php _e( 'Restrict to users who are registered in the site for at least ', 'bulk-delete' );?>
				<input type="number" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_registered_days" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_registered_days" class="screen-per-page" value="0" min="0" disabled> <?php _e( 'days.', 'bulk-delete' );?>
			</td>
		</tr>

		<?php
		if ( bd_is_simple_login_log_present() ) {
			$disabled = '';
		} else {
			$disabled = 'disabled';
		}
?>
		<tr>
			<td scope="row" colspan="2">
			<input name="smbd_<?php echo $this->field_slug; ?>_login_restrict" id="smbd_<?php echo $this->field_slug; ?>_login_restrict" value="true" type="checkbox" <?php echo $disabled; ?>>
				<?php _e( 'Restrict to users who have not logged in the last ', 'bulk-delete' );?>
				<input type="number" name="smbd_<?php echo $this->field_slug; ?>_login_days" id="smbd_<?php echo $this->field_slug; ?>_login_days" class="screen-per-page" value="0" min="0" disabled> <?php _e( 'days', 'bulk-delete' );?>.
		<?php if ( 'disabled' == $disabled ) { ?>
				<span style = "color:red">
					<?php _e( 'Need the free "Simple Login Log" Plugin', 'bulk-delete' ); ?> <a href = "http://wordpress.org/plugins/simple-login-log/">Install now</a>
				</span>
		<?php } ?>
			</td>
		</tr>

		<?php if ( bd_is_simple_login_log_present() ) : ?>
			<tr>
				<td scope="row" colspan="2">
					<?php _e( 'Enter "0 days" to delete users who have never logged in after the "Simple Login Log" plugin has been installed.', 'bulk-delete' ); ?>
			</tr>
		<?php endif; ?>
<?php
	}

	/**
	 * Render delete user with no posts settings.
	 *
	 * @since 5.5
	 */
	protected function render_user_with_no_posts_settings() {
	?>
		<tr>
			<td scope="row" colspan="2">
				<input type="checkbox" value="true"
				       name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_no_posts"
				       id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_no_posts" class="user_restrict_to_no_posts_filter">

				<?php _e( "Restrict to users who don't have any posts.", 'bulk-delete' ); ?>
			</td>
		</tr>

		<tr class="user_restrict_to_no_posts_filter_items visually-hidden">
			<td scope="row" colspan="2">
				<table class="filter-items">
					<tr>
						<td scope="row">
							<?php _e( 'Select the post types. By default all post types are considered.', 'bulk-delete' ); ?>
						</td>
					</tr>

					<?php $this->render_post_type_checkboxes( "smbd_{$this->field_slug}_no_post_post_types" ); ?>
				</table>
			</td>
		</tr>

	<?php
	}

	/**
	 * Render Post Types as checkboxes.
	 *
	 * @since 5.6.0
	 *
	 * @param string $name Name of post type checkboxes.
	 */
	protected function render_post_type_checkboxes( $name ) {
		$post_types = bd_get_post_types();
		?>

		<?php foreach ( $post_types as $post_type ) : ?>

		<tr>
			<td scope="row">
				<input type="checkbox" name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo esc_attr( $post_type->name ); ?>"
					id="smbd_post_type_<?php echo esc_html( $post_type->name ); ?>" checked>

				<label for="smbd_post_type_<?php echo esc_html( $post_type->name ); ?>">
					<?php echo esc_html( $post_type->label ); ?>
				</label>
			</td>
		</tr>

		<?php endforeach; ?>
		<?php
	}
}