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
				<select class="enhanced-post-types-with-status" multiple="multiple" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>[]">
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
	 * Render user role dropdown.
	 */
	protected function render_user_role_dropdown() {
		global $wp_roles;
		?>

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_roles[]" class="select2"
				multiple="multiple" data-placeholder="<?php _e( 'Select User Role', 'bulk-delete' ); ?>">

			<?php foreach ( $wp_roles->roles as $role => $role_details ) : ?>
				<option value="<?php echo esc_attr( $role ); ?>">
					<?php echo esc_html( $role_details['name'] ), ' (', absint( $this->get_user_count_by_role( $role ) ), ' ', __( 'Users', 'bulk-delete' ), ')'; ?>
				</option>
			<?php endforeach; ?>
		</select>

		<?php
	}

	/**
	 * Render Post type dropdown.
	 */
	protected function render_post_type_dropdown() {
		bd_render_post_type_dropdown( $this->field_slug );
	}

	/**
	 * Render Taxonomy dropdown.
	 */
	protected function render_taxonomy_dropdown() {
		$taxonomies = get_taxonomies( array(), 'objects' );
		?>
		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_taxonomy" class="select2" multiple="multiple" data-placeholder="<?php _e( 'Select Taxonomy', 'bulk-delete' ); ?>">
			<?php foreach ( $taxonomies as $taxonomy ) : ?>
				<option value="<?php echo esc_attr( $taxonomy->name ); ?>">
					<?php echo esc_html( $taxonomy->label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render term options.
	 */
	protected function render_term_options() {
		?>
		<h4><?php _e( 'Delete terms that', 'bulk-delete' ); ?></h4>
		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_opt">
			<?php if( $this->field_slug == 'terms_by_name' ){ ?>
				<option value="equal_to">equal to</option>
				<option value="not_equal_to">not equal to</option>
				<option value="starts">starts</option>
				<option value="ends">ends</option>
				<option value="contains">contains</option>
				<option value="non_contains">non contains</option>
			<?php }elseif( $this->field_slug == 'terms_by_post_count' ){ ?>
				<option value="equal_to">equal to</option>
				<option value="not_equal_to">not equal to</option>
				<option value="less_than">less than</option>
				<option value="greater_than">greater than</option>
			<?php }?>
		</select> With 
		<input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_text">
		<?php 
	}

	/**
	 * Render have post settings.
	 */
	protected function render_have_post_settings() {
		?>
		<p><label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_no_posts"><input type="checkbox" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_no_posts" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_no_posts"> <?php _e( 'Only if it doesn\'t contain any post', 'bulk-delete' ); ?></label></p>
		<?php 
	}
}
