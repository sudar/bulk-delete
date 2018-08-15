<?php
namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\BulkDelete\Core\Metas\MetasModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Term Meta.
 *
 * @since 6.0.0
 */
class DeleteTermMetaModule extends MetasModule {
	/**
	 * Initialize the Module.
	 */
	protected function initialize() {
		$this->field_slug    = 'meta_term';
		$this->meta_box_slug = 'bd-meta-term';
		$this->action        = 'delete_meta_term';
		$this->cron_hook     = 'do-bulk-delete-term-meta';
		$this->messages      = array(
			'box_label'  => __( 'Bulk Delete Term Meta', 'bulk-delete' ),
			'scheduled'  => __( 'Term meta fields from the posts with the selected criteria are scheduled for deletion.', 'bulk-delete' ),
			'cron_label' => __( 'Delete Term Meta', 'bulk-delete' ),
		);
	}

	/**
	 * Render the Modules.
	 */
	public function render() {
		?>
		<!-- Term Meta box start-->
		<fieldset class="options">
		<h4><?php _e( 'Select the taxonomy whose term meta fields you want to delete', 'bulk-delete' ); ?></h4>
		<table class="optiontable">
			<tr>
				<td>
					<?php $this->render_taxonomy_dropdown(); ?>
				</td>
			</tr>
		</table>

		<h4><?php _e( 'Choose your term want to delete', 'bulk-delete' ); ?></h4>
		<table class="optiontable">
			<tr>
				<td>
					<select class="enhanced-terms-dropdown" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term">
						<option><?php _e( 'Choose Terms', 'bulk-delete' ); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<h4><?php _e( 'Select the term meta that you want to delete', 'bulk-delete' ); ?></h4>
		<table class="optiontable">
			<tr>
				<td>
					<select class="enhanced-term-meta-dropdown" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_meta">
						<option><?php _e( 'Choose Term Meta', 'bulk-delete' ); ?></option>
					</select>

					<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_meta_option">
						<option value="equal"><?php _e( 'Equal to', 'bulk-delete' ); ?></option>
						<option value="not_equal"><?php _e( 'Not equal to', 'bulk-delete' ); ?></option>
					</select>

					<input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_meta_value" />
				</td>
			</tr>
		</table>

		<?php
		/**
		 * Add more fields to the delete term meta field form.
		 * This hook can be used to add more fields to the delete term meta field form.
		 *
		 * @since 6.0.0
		 */
		do_action( 'bd_delete_term_meta_form' );
		?>

		</fieldset>

		<?php $this->render_submit_button(); ?>

		<!-- Term Meta box end-->
		<?php
	}

	/**
	 * Convert user input to bulkwp standard.
	 *
	 * @param array $request Request array.
	 * @param array $options User options.
	 *
	 * @return array User options.
	 */
	protected function convert_user_input_to_options( $request, $options ) {
		$options['term'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term', '' ) );

		$options['term_meta']       = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_meta', '' ) );
		$options['term_meta_value'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_meta_value', '' ) );

		$options['term_meta_option'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_meta_option', '' ) );

		return $options;
	}

	/**
	 * Delete action.
	 *
	 * @param array $options User options.
	 */
	public function do_delete( $options ) {
		$count = 0;

		if ( 'equal' === $options['term_meta_option'] ) {
			$is_delete = delete_term_meta( $options['term'], $options['term_meta'], $options['term_meta_value'] );
			if ( $is_delete ) {
				$count++;
			}
		} elseif ( 'not_equal' === $options['term_meta_option'] ) {
			$term_values = get_term_meta( $options['term'], $options['term_meta'] );
			foreach ( $term_values as $term_value ) {
				if ( $options['term_meta_value'] !== $term_value ) {
					delete_term_meta( $options['term'], $options['term_meta'], $term_value );
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * Filter JS Array and add pro hooks.
	 *
	 * @param array $js_array JavaScript Array.
	 *
	 * @return array Modified JavaScript Array.
	 */
	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][]                 = '_' . $this->field_slug;
		$js_array['validators']['delete_meta_term'] = 'noValidation';

		$js_array['pre_action_msg']['delete_meta_term'] = 'deleteTMWarning';
		$js_array['msg']['deleteTMWarning']             = __( 'Are you sure you want to delete all the term meta fields?', 'bulk-delete' );

		return $js_array;
	}

	/**
	 * Get Success Message.
	 *
	 * @param int $items_deleted Number of items that were deleted.
	 *
	 * @return string Success message.
	 */
	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted %d term meta field', 'Deleted %d term meta field', $items_deleted, 'bulk-delete' );
	}
}
