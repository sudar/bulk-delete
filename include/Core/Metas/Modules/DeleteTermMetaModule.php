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
		$this->field_slug    = 'term_meta';
		$this->meta_box_slug = 'bd-term-meta';
		$this->action        = 'delete_term_meta';
		$this->cron_hook     = 'do-bulk-delete-term-meta';
		$this->messages      = array(
			'box_label'        => __( 'Bulk Delete Term Meta', 'bulk-delete' ),
			'scheduled'        => __( 'Term meta fields from the posts with the selected criteria are scheduled for deletion.', 'bulk-delete' ),
			'confirm_deletion' => __( 'Are you sure you want to delete all the term meta fields?', 'bulk-delete' ),
			/* translators: 1 Number of term meta deleted */
			'deleted_one'      => __( 'Deleted %d term meta field', 'bulk-delete' ),
			/* translators: 1 Number of term meta deleted */
			'deleted_multiple' => __( 'Deleted %d term meta fields', 'bulk-delete' ),
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
					<select class="enhanced-terms-dropdown" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_id">
						<option><?php _e( 'Choose Terms', 'bulk-delete' ); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<h4><?php _e( 'Select the term meta that you want to delete', 'bulk-delete' ); ?></h4>
		<table class="optiontable">
			<tr>
				<td>
					<select class="enhanced-term-meta-dropdown" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key">
						<option><?php _e( 'Choose Term Meta', 'bulk-delete' ); ?></option>
					</select>

					<?php $this->render_string_operators_dropdown( 'string', array( '=', '!=' ) ); ?>

					<input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_value" />
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
		$options['term_id'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_id', '' ) );

		$options['term_meta_key']   = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_key', '' ) );
		$options['term_meta_value'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_value', '' ) );

		$options['term_meta_operator'] = sanitize_text_field( bd_array_get( $request, 'smbd_' . $this->field_slug . '_operator', '' ) );

		return $options;
	}

	/**
	 * Delete action.
	 *
	 * @param array $options User options.
	 */
	public function do_delete( $options ) {
		$count = 0;

		if ( '=' === $options['term_meta_operator'] ) {
			$is_deleted = delete_term_meta( $options['term_id'], $options['term_meta_key'], $options['term_meta_value'] );
			if ( $is_deleted ) {
				$count++;
			}
		} elseif ( '!=' === $options['term_meta_operator'] ) {
			$term_values = get_term_meta( $options['term_id'], $options['term_meta_key'] );
			foreach ( $term_values as $term_value ) {
				if ( $options['term_meta_value'] !== $term_value ) {
					$is_deleted = delete_term_meta( $options['term_id'], $options['term_meta_key'], $term_value );
					if ( $is_deleted ) {
						$count++;
					}
				}
			}
		}

		return $count;
	}

	/**
	 * Append any module specific options to JS array.
	 *
	 * This function will be overridden by the child classes.
	 *
	 * @param array $js_array JavaScript Array.
	 *
	 * @return array Modified JavaScript Array
	 */
	public function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'noValidation';

		return $js_array;
	}
}
