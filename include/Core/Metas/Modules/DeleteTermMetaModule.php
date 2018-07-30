<?php
namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\BulkDelete\Core\Metas\MetasModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Term Meta.
 *
 * @since 6.1.0
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
	 *
	 * @return void
	 */
	public function render() {
		?>
		<!-- Term Meta box start-->
		<fieldset class="options">
		<?php
		$taxonomies = $this->get_taxonomies();
		?>
		<h4><?php _e( 'Select the taxonomy whose term meta fields you want to delete', 'bulk-delete' ); ?></h4>
		<table class="optiontable">
		<?php
		foreach ( $taxonomies as $taxonomy ) {
			?>
			<tr>
				<td>
					<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_taxonomy" value = "<?php echo $taxonomy; ?>" type = "radio" class = "smbd_<?php echo esc_attr( $this->field_slug ); ?>_taxonomy">
					<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_taxonomy"><?php echo $taxonomy; ?> </label>
				</td>
			</tr>
			<?php
		}
		?>
		</table>

		<h4><?php _e( 'Choose your term want to delete', 'bulk-delete' ); ?></h4>
		<table class="optiontable">
			<tr>
				<td>
					<select class="select2 select2-terms" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term">
						<option>Choose Terms</option>
					</select>
				</td>
			</tr>
		</table>

		<h4><?php _e( 'Choose your term meta want to delete', 'bulk-delete' ); ?></h4>
		<table class="optiontable">
			<tr>
				<td>
					<select class="select2 select2-term-meta" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_meta">
						<option>Choose Term Meta</option>
					</select>

					<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_meta_option">
						<option value="equal">Equal to</option>
						<option value="not_equal">Not equal to</option>
					</select>

					<input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_term_meta_value" />
				</td>
			</tr>
		</table>

		<?php
		/**
		 * Add more fields to the delete post meta field form.
		 * This hook can be used to add more fields to the delete post meta field form.
		 *
		 * @since 5.4
		 */
		do_action( 'bd_delete_post_meta_form' );
		?>

		</fieldset>

		<p>
			<button type="submit" name="bd_action" value="delete_meta_term" class="button-primary"><?php _e( 'Bulk Delete ', 'bulk-delete' ); ?>&raquo;</button>
		</p>
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
		$options['term'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term', 'term' ) );

		$options['term_meta']       = bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_meta', 'term_meta' );
		$options['term_meta_value'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_meta_value', '' ) );

		$options['term_meta_option'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_meta_option', '' ) );

		return $options;
	}

	/**
	 * Delete action.
	 *
	 * @param array $options User options.
	 */
	public function do_delete( $options ) {
		$count = 0;

		if ( $options['term_meta_option'] === 'equal' ) {
			$is_delete = delete_term_meta( $options['term'], $options['term_meta'], $options['term_meta_value'] );
			if ( $is_delete ) {
				$count++;
			}
		} elseif ( $options['term_meta_option'] === 'not_equal' ) {
			$term_value = get_term_meta( $options['term'], $options['term_meta'], true );
			if ( $term_value !== $options['term_meta_value'] ) {
				delete_term_meta( $options['term'], $options['term_meta'] );
				$count++;
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
