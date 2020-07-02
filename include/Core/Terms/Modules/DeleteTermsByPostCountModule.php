<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\BulkDelete\Core\Terms\QueryOverriders\DeleteTermsQueryOverrider;
use BulkWP\BulkDelete\Core\Terms\TermsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Terms by Post Count.
 *
 * @since 6.0.0
 */
class DeleteTermsByPostCountModule extends TermsModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'terms';
		$this->field_slug    = 'terms_by_post_count';
		$this->meta_box_slug = 'bd_delete_terms_by_post_count';
		$this->action        = 'delete_terms_by_post_count';
		$this->messages      = array(
			'box_label'        => __( 'Delete Terms by Post Count', 'bulk-delete' ),
			'confirm_deletion' => __( 'Are you sure you want to delete all the terms based on the selected option?', 'bulk-delete' ),
			'validation_error' => __( 'Please enter the post count based on which terms should be deleted. A valid post count will be greater than or equal to zero', 'bulk-delete' ),
			/* translators: 1 Number of terms deleted */
			'deleted_one'      => __( 'Deleted %d term with the selected options', 'bulk-delete' ),
			/* translators: 1 Number of terms deleted */
			'deleted_multiple' => __( 'Deleted %d terms with the selected options', 'bulk-delete' ),
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function render() {
		?>
		<h4><?php _e( 'Select the taxonomy from which you want to delete terms', 'bulk-delete' ); ?></h4>
		<fieldset class="options">
			<table class="optiontable">
				<tr><?php $this->render_taxonomy_dropdown(); ?></tr>

				<h4><?php _e( 'Choose your filtering options', 'bulk-delete' ); ?></h4>
				<tr>
					<td><?php _e( 'Delete Terms if the post count is ', 'bulk-delete' ); ?></td>
					<td><?php $this->render_numeric_operators_dropdown( 'numeric', array( '=', '!=', '<', '>' ) ); ?></td>
					<td><input type="number" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_count" placeholder="Post count" min="0" class="validate"></td>
					<td>
						<?php
						$markup  = '';
						$content = __( 'Post count is the number of posts that are assigned to a term.', 'bulk-delete' );
						echo '&nbsp' . bd_generate_help_tooltip( $markup, $content );
						?>
					</td>
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
		$options['operator']   = bd_array_get( $request, 'smbd_' . $this->field_slug . '_operator' );
		$options['post_count'] = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_count' ) );

		return $options;
	}

	/**
	 * Get the list of terms ids that need to be deleted.
	 *
	 * Return an empty query array to short-circuit deletion.
	 *
	 * @param array $options Delete options.
	 *
	 * @return int[] List of term ids to delete.
	 */
	protected function get_term_ids_to_delete( $options ) {
		$query = array(
			'taxonomy'       => $options['taxonomy'],
			'bd_operator'    => $options['operator'],
			'bd_value'       => $options['post_count'],
			'bd_column_name' => 'count',
		);

		$query_overrider = new DeleteTermsQueryOverrider();
		$query_overrider->load();

		return $this->query_terms( $query );
	}
}
