<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\BulkDelete\Core\Terms\QueryOverriders\DeleteTermsQueryOverrider;
use BulkWP\BulkDelete\Core\Terms\TermsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Terms by Name.
 *
 * @since 6.0.0
 */
class DeleteTermsByNameModule extends TermsModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->item_type     = 'terms';
		$this->field_slug    = 'terms_by_name';
		$this->meta_box_slug = 'bd_delete_terms_by_name';
		$this->action        = 'delete_terms_by_name';
		$this->messages      = array(
			'box_label'        => __( 'Delete Terms by Name', 'bulk-delete' ),
			'confirm_deletion' => __( 'Are you sure you want to delete all the terms based on the selected option?', 'bulk-delete' ),
			'validation_error' => __( 'Please enter the term name that should be deleted', 'bulk-delete' ),
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
					<td><?php _e( 'Delete Terms if the name ', 'bulk-delete' ); ?></td>
					<td><?php $this->render_string_operators_dropdown( 'string', array( '=', '!=', 'LIKE', 'NOT LIKE', 'STARTS_WITH', 'ENDS_WITH' ) ); ?></td>
					<td><input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_value" placeholder="<?php _e( 'Term Name', 'bulk-delete' ); ?>" class="validate"></td>
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
		$term_ids = array();
		$operator = $options['operator'];
		$value    = $options['value'];
		if ( empty( $value ) ) {
			return $term_ids;
		}

		switch ( $operator ) {
			case '=':
				$term_ids = $this->get_terms_that_are_equal_to( $options );
				break;

			case 'LIKE':
				$term_ids = $this->get_terms_that_contains( $options );
				break;

			default:
				$term_ids = $this->get_matching_terms_for_other_operators( $options );
				break;
		}

		return $term_ids;
	}

	/**
	 * Get terms with name that are equal to a specific string.
	 *
	 * @param array $options User options.
	 *
	 * @return int[] Term ids.
	 */
	protected function get_terms_that_are_equal_to( $options ) {
		$query = array(
			'taxonomy' => $options['taxonomy'],
			'name'     => $options['value'],
		);

		return $this->query_terms( $query );
	}

	/**
	 * Get terms with name that contains a specific string.
	 *
	 * @param array $options User options.
	 *
	 * @return int[] Term ids.
	 */
	protected function get_terms_that_contains( $options ) {
		$query = array(
			'taxonomy'   => $options['taxonomy'],
			'name__like' => $options['value'],
		);

		return $this->query_terms( $query );
	}

	/**
	 * Get matching terms for not equal to, not contains, starts with and ends with operators.
	 *
	 * @param array $options User options.
	 *
	 * @return int[] Term ids.
	 */
	protected function get_matching_terms_for_other_operators( $options ) {
		$operator = $options['operator'];
		$value    = $options['value'];
		switch ( $operator ) {
			case 'NOT LIKE':
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
		$query           = array(
			'taxonomy'       => $options['taxonomy'],
			'bd_operator'    => $operator,
			'bd_value'       => $value,
			'bd_column_name' => 'name',
		);
		$query_overrider = new DeleteTermsQueryOverrider();
		$query_overrider->load();

		return $this->query_terms( $query );
	}
}
