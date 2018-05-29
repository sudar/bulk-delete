<?php

namespace BulkWP\BulkDelete\Core\Base\Mixin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Container of all Render methods.
 *
 * Ideally this should be a Trait. Since Bulk Delete still supports PHP 5.3, this is implemented as a class.
 * Once the minimum requirement is increased to PHP 5.3, this will be changed into a Trait.
 *
 * @since 6.0.0
 */
abstract class Renderer extends Fetcher {
	/**
	 * Render Post Types as radio buttons.
	 */
	protected function render_post_type_as_radios() {
		$field_slug = $this->field_slug;

		$post_types = $this->get_post_types();
		?>

		<?php foreach ( $post_types as $post_type ) : ?>

			<tr>
				<td scope="row">
					<input type="radio" name="<?php echo esc_attr( $field_slug ); ?>_post_type"
						value="<?php echo esc_attr( $post_type->name ); ?>"
						id="smbd_post_type_<?php echo esc_html( $post_type->name ); ?>">

					<label for="smbd_post_type_<?php echo esc_html( $post_type->name ); ?>">
						<?php echo esc_html( $post_type->label ); ?>
					</label>
				</td>
			</tr>

		<?php endforeach; ?>
		<?php
	}

	/**
	 * Render Post type with status and post count checkboxes.
	 */
	protected function render_post_type_with_status() {
		$post_types_by_status = $this->get_post_types_by_status();
		?>
		<tr>
			<td scope="row" colspan="2">
				<select class="select2-post" multiple="multiple" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>[]">
				<?php foreach ( $post_types_by_status as $post_type => $all_status ) : ?>
					<optgroup label="<?php echo esc_html( $post_type ); ?>">
					<?php foreach ( $all_status as $status_key => $status_value ) : ?>
						<option value="<?php echo esc_attr( $status_key ); ?>"><?php echo esc_html( $status_value ); ?></option>
					<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * Split post type and status.
	 *
	 * @param string $str Post type and status combination.
	 *
	 * @return array Post type and status as elements of array.
	 */
	protected function split_post_type_and_status( $str ) {
		$type_status = array();

		$str_arr = explode( '-', $str );

		if ( count( $str_arr ) > 1 ) {
			$type_status['status'] = end( $str_arr );
			$type_status['type']   = implode( '-', array_slice( $str_arr, 0, - 1 ) );
		} else {
			$type_status['status'] = 'publish';
			$type_status['type']   = $str;
		}

		return $type_status;
	}

	/**
	 * Render Tags dropdown.
	 */
	protected function render_user_role_dropdown() {
		$users_count = count_users();
		?>
		<select name="smbd_u_roles[]" class="select2" multiple="multiple" data-placeholder="<?php _e( 'Select Role', 'bulk-delete' ); ?>">
		<?php 
			foreach ( $users_count['avail_roles'] as $role => $count ) {
			$role_detail = get_role( $role );
			if( isset( $role_detail->name ) ){
				$role_name = $role_detail->name;
			}else{
				$role_name = $role;
			}
		?>
			<option value="<?php echo $role; ?>"><?php echo $role; ?> (<?php echo $count . ' '; _e( 'Users', 'bulk-delete' ); ?>)</option>
			<?php
		} ?>
		</select>
		<?php 
	}
}
