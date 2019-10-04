<?php

namespace BulkWP\BulkDelete\Core\Users\Modules;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Users by User Meta in Multisite.
 *
 * @since 6.1.0
 */
class DeleteUsersByUserMetaInMultisiteModule extends DeleteUsersByUserMetaModule {
	/**
	 * Render delete users meta box.
	 *
	 * @since 6.1.0
	 */
	public function render() {
		?>
			<!-- Users Start-->
			<h4><?php _e( 'Select the user meta from which you want to delete users', 'bulk-delete' ); ?></h4>
			<fieldset class="options">
				<table class="optiontable">
					<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key" class="enhanced-dropdown">
						<?php
						$meta_keys = $this->get_unique_user_meta_keys();
						foreach ( $meta_keys as $meta_key ) {
							printf( '<option value="%s">%s</option>', esc_attr( $meta_key ), esc_html( $meta_key ) );
						}
						?>
					</select>

					<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_compare">
						<option value="=">Equals to</option>
						<option value="!=">Not Equals to</option>
						<option value=">">Greater than</option>
						<option value=">=">Greater than or equals to</option>
						<option value="<">Less than</option>
						<option value="<=">Less than or equals to</option>
						<option value="LIKE">Contains</option>
						<option value="NOT LIKE">Not Contains</option>
						<option value="STARTS WITH">Starts with</option>
						<option value="ENDS WITH">Ends with</option>
					</select>
					<input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_value" placeholder="<?php _e( 'Meta Value', 'bulk-delete' ); ?>">
				</table>

				<p>
					<?php _e( 'If you want to check for null values, then leave the value column blank', 'bulk-delete' ); ?>
				</p>
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
		parent::convert_user_input_to_options( $request, $options );
		$options['network_admin'] = true;

		return $options;
	}
}
