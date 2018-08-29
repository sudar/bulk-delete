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

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_roles[]" class="enhanced-role-dropdown"
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

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_taxonomy" class="enhanced-taxonomy-list" data-placeholder="<?php _e( 'Select Taxonomy', 'bulk-delete' ); ?>">
			<?php foreach ( $taxonomies as $taxonomy ) : ?>
				<option value="<?php echo esc_attr( $taxonomy->name ); ?>">
					<?php echo esc_html( $taxonomy->label . ' (' . $taxonomy->name . ')' ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render Category dropdown.
	 */
	protected function render_category_dropdown() {
		$categories = $this->get_categories();
		?>

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_category[]" data-placeholder="<?php _e( 'Select Categories', 'bulk-delete' ); ?>"
				class="<?php echo sanitize_html_class( $this->enable_ajax_if_needed_to_dropdown_class_name( count( $categories ), 'select2-taxonomy' ) ); ?>"
				data-taxonomy="category" multiple>

			<option value="all">
				<?php _e( 'All Categories', 'bulk-delete' ); ?>
			</option>

			<?php foreach ( $categories as $category ) : ?>
				<option value="<?php echo absint( $category->cat_ID ); ?>">
					<?php echo esc_html( $category->cat_name ), ' (', absint( $category->count ), ' ', __( 'Posts', 'bulk-delete' ), ')'; ?>
				</option>
			<?php endforeach; ?>

		</select>
		<?php
	}

	/**
	 * Render String based comparison operators dropdown.
	 */
	protected function render_string_comparison_operators() {
		?>
		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_operator">
			<option value="equal_to"><?php _e( 'equal to', 'bulk-delete' ); ?></option>
			<option value="not_equal_to"><?php _e( 'not equal to', 'bulk-delete' ); ?></option>
			<option value="starts_with"><?php _e( 'starts with', 'bulk-delete' ); ?></option>
			<option value="ends_with"><?php _e( 'ends with', 'bulk-delete' ); ?></option>
			<option value="contains"><?php _e( 'contains', 'bulk-delete' ); ?></option>
			<option value="not_contains"><?php _e( 'not contains', 'bulk-delete' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render number based comparison operators dropdown.
	 */
	protected function render_number_comparison_operators() {
		?>
		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_operator">
			<option value="equal_to"><?php _e( 'equal to', 'bulk-delete' ); ?></option>
			<option value="not_equal_to"><?php _e( 'not equal to', 'bulk-delete' ); ?></option>
			<option value="less_than"><?php _e( 'less than', 'bulk-delete' ); ?></option>
			<option value="greater_than"><?php _e( 'greater than', 'bulk-delete' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render Tags dropdown.
	 */
	protected function render_tags_dropdown() {
		$tags = $this->get_tags();
		?>

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>[]" data-placeholder="<?php _e( 'Select Tags', 'bulk-delete' ); ?>"
				class="<?php echo sanitize_html_class( $this->enable_ajax_if_needed_to_dropdown_class_name( count( $tags ), 'select2-taxonomy' ) ); ?>"
				data-taxonomy="post_tag" multiple>

			<option value="all">
				<?php _e( 'All Tags', 'bulk-delete' ); ?>
			</option>

			<?php foreach ( $tags as $tag ) : ?>
				<option value="<?php echo absint( $tag->term_id ); ?>">
					<?php echo esc_html( $tag->name ), ' (', absint( $tag->count ), ' ', __( 'Posts', 'bulk-delete' ), ')'; ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Get the class name for select2 dropdown based on the number of items present.
	 *
	 * @param int    $count      The number of items present.
	 * @param string $class_name Primary class name.
	 *
	 * @return string Class name.
	 */
	protected function enable_ajax_if_needed_to_dropdown_class_name( $count, $class_name ) {
		if ( $count >= $this->get_enhanced_select_threshold() ) {
			$class_name .= '-ajax';
		}

		return $class_name;
	}

	/**
	 * Render Sticky Posts dropdown.
	 */
	protected function render_sticky_post_dropdown() {
		$posts = $this->get_sticky_posts();
		?>
		<table class="optiontable">
			<tr>
				<td scope="row">
					<input type="checkbox" class="smbd_sticky_post_options" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>[]" value="All">
					<label>All</label>
				</td>
			</tr>
			<?php
			foreach ( $posts as $post ) :
				$user = get_userdata( $post->post_author );
				?>
			<tr>
				<td scope="row">
				<input type="checkbox" class="smbd_sticky_post_options" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>[]" value="<?php echo absint( $post->ID ); ?>">
				<label><?php echo esc_html( $post->post_title . ' Published by ' . $user->display_name . ' on ' . $post->post_date ); ?></label>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
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

	/**
	 * Render the "private post" setting fields.
	 */
	protected function render_private_post_settings() {
		bd_render_private_post_settings( $this->field_slug );
	}

	/**
	 * Get the threshold after which enhanced select should be used.
	 *
	 * @return int Threshold.
	 */
	protected function get_enhanced_select_threshold() {
		/**
		 * Filter the enhanced select threshold.
		 *
		 * @since 6.0.0
		 *
		 * @param int Threshold.
		 */
		return apply_filters( 'bd_enhanced_select_threshold', 1000 );
	}
}
