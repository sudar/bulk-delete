<?php

namespace BulkWP\BulkDelete\Core\Sites\Modules;

use BulkWP\BulkDelete\Core\Sites\SitesModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Sites by Name.
 *
 * @since 6.2.0
 */
class DeleteSitesByNameModule extends SitesModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'sites';
		$this->field_slug    = 'sites_by_name';
		$this->meta_box_slug = 'bd_delete_sites_by_name';
		$this->action        = 'delete_sites_by_name';
		$this->messages      = array(
			'box_label'        => __( 'Delete Sites by Name', 'bulk-delete' ),
			'confirm_deletion' => __( 'Are you sure you want to delete all the sites based on the selected option?', 'bulk-delete' ),
			'validation_error' => __( 'Please enter the site name that should be deleted', 'bulk-delete' ),
			/* translators: 1 Number of sites deleted */
			'deleted_one'      => __( 'Deleted %d site with the selected options', 'bulk-delete' ),
			/* translators: 1 Number of sites deleted */
			'deleted_multiple' => __( 'Deleted %d sites with the selected options', 'bulk-delete' ),
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function render() {
		?>
		<h4><?php _e( 'Enter the site name that you want to delete', 'bulk-delete' ); ?></h4>
		<fieldset class="options">
			<table class="optiontable">
				<tr>
					<td><?php _e( 'Delete Sites if the name ', 'bulk-delete' ); ?></td>
					<td><?php $this->render_string_operators_dropdown( 'stringy', array( '=', 'LIKE', 'STARTS_WITH', 'ENDS_WITH' ) ); ?></td>
					<td><input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_value" placeholder="<?php _e( 'Site Name', 'bulk-delete' ); ?>" class="validate"></td>
				</tr>
			</table>
		</fieldset>

		<?php
		$this->render_submit_button();
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateTextbox';

		return $js_array;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		$options['operator'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_operator' );
		$options['value']    = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_value' ) );

		return $options;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function build_query( $options ) {
		// Left empty on purpose.
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function do_delete( $options ) {
		global $wpdb;
		$count    = 0;
		$operator = $options['operator'];
		$value    = $options['value'];

		switch ( $operator ) {
			case 'LIKE':
				$value = '%' . $value . '%';
				break;
			case 'STARTS_WITH':
				$operator = 'LIKE';
				$value    = $value . '%';
				break;
			case 'ENDS_WITH':
				$operator = 'LIKE';
				$value    = '%' . $value;
				break;
		}

		if ( ! is_subdomain_install() ) {
			$value = get_network()->path . $value . '/';
		}

		$site_ids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE path {$operator} %s", $value ) );

		foreach ( $site_ids as $site_id ) {
			// TODO: Do we need to log errors if there is any error?
			$response = wp_delete_site( $site_id );
			if ( ! is_wp_error( $response ) ) {
				$count++;
			}
		}

		return $count;
	}
}
