<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\BulkDelete\Core\Terms\TermsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Terms by Name.
 *
 * @since 6.0.0
 */
class DeleteTermsByNameModule extends TermsModule {

	protected function initialize() {
		$this->item_type     = 'terms';
		$this->field_slug    = 'terms_by_name';
		$this->meta_box_slug = 'bd_delete_terms_by_name';
		$this->action        = 'delete_terms_by_name';
		$this->messages      = array(
			'box_label'  => __( 'Delete Terms by Name', 'bulk-delete' ),
			'scheduled'  => __( 'The selected terms are scheduled for deletion', 'bulk-delete' ),
			'cron_label' => __( 'Delete Terms By Name', 'bulk-delete' ),
		);
	}

	public function render() {
		?>

		<fieldset class="options">
			<h4><?php _e( 'Select the taxonomy from which you want to delete terms', 'bulk-delete' ); ?></h4>

			<?php $this->render_taxonomy_dropdown(); ?>

			<h4><?php _e( 'Choose your filtering options', 'bulk-delete' ); ?></h4>

			<?php _e( 'Delete Terms if the name ', 'bulk-delete' ); ?>
			<?php $this->render_string_comparison_operators(); ?>
			<input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_value" placeholder="<?php _e( 'Term Name', 'bulk-delete' ); ?>">
		</fieldset>

		<?php
		$this->render_submit_button();
	}

	/**
	 * Filter the js array.
	 *
	 * @param array $js_array JavaScript Array.
	 *
	 * @return array Modified JavaScript Array
	 */
	public function filter_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validatePostTypeSelect2';
		$js_array['error_msg'][ $this->action ]  = 'selectPostType';
		$js_array['msg']['selectPostType']       = __( 'Please select at least one post type', 'bulk-delete' );

		$js_array['dt_iterators'][] = '_' . $this->field_slug;

		$js_array['validators'][ $this->action ] = 'noValidation';

		$js_array['pre_action_msg'][ $this->action ] = 'deleteTermsWarning';
		$js_array['msg']['deleteTermsWarning']       = __( 'Are you sure you want to delete all the terms based on the selected option?', 'bulk-delete' );

		return $js_array;
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['operator'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_operator' );
		$options['value']    = bd_array_get( $request, 'smbd_' . $this->field_slug . '_value' );

		return $options;
	}

	/**
	 * Build query from delete options.
	 *
	 * @param array $options Delete options.
	 *
	 * @return array Query.
	 */
	protected function build_query( $options ) {
		$query    = array();
		$value    = $options['value'];
		$operator = $options['operator'];

		switch ( $operator ) {
			case 'equal_to':
				$query['name__like'] = $value;
				break;

			case 'not_equal_to':
				$name_like_args = array(
					'name__like' => $value,
					'taxonomy'   => $options['taxonomy'],
				);

				$term_ids         = $this->query_terms( $name_like_args, $options );
				$query['exclude'] = $term_ids;
				break;

			case 'starts':
				$term_ids         = $this->term_starts( $value, $options );
				$query['include'] = $term_ids['include'];
				$query['exclude'] = $term_ids['exclude'];
				break;

			case 'ends':
				$term_ids         = $this->term_ends( $value, $options );
				$query['include'] = $term_ids['include'];
				$query['exclude'] = $term_ids['exclude'];
				break;

			case 'contains':
				$term_ids         = $this->term_contains( $value, $options );
				$query['include'] = $term_ids['include'];
				$query['exclude'] = $term_ids['exclude'];
				break;

			case 'not_contains':
				$term_ids         = $this->term_contains( $value, $options );
				$query['exclude'] = $term_ids['include'];
				$query['include'] = $term_ids['exclude'];
				break;
		}

		return $query;
	}

	/**
	 * Response message for deleting posts.
	 *
	 * @param int $items_deleted Total number of posts deleted.
	 *
	 * @return string Response message
	 */
	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted %d term with the selected options', 'Deleted %d terms with the selected options', $items_deleted, 'bulk-delete' );
	}

	/**
	 * Get terms which is start with specified string.
	 *
	 * @param string $term_text user input text.
	 * @param array  $options   user options.
	 *
	 * @return array term ids.
	 */
	protected function term_starts( $term_text, $options ) {
		$term_ids = array(
			'include' => array(),
			'exclude' => array(),
		);
		$terms    = get_terms(
			$options['taxonomy'], array(
				'hide_empty' => false,
			)
		);

		foreach ( $terms as $term ) {
			if ( $this->bd_starts_with( $term->name, $term_text ) ) {
				$term_ids['include'][] = $term->term_id;

				continue;
			}
			$term_ids['exclude'][] = $term->term_id;
		}

		return $term_ids;
	}

	/**
	 * Get terms which is ends with specified string.
	 *
	 * @param string $term_text user input text.
	 * @param array  $options   user options.
	 *
	 * @return array term ids.
	 */
	protected function term_ends( $term_text, $options ) {
		$term_ids = array(
			'include' => array(),
			'exclude' => array(),
		);
		$terms    = get_terms(
			$options['taxonomy'], array(
				'hide_empty' => false,
			)
		);

		foreach ( $terms as $term ) {
			if ( $this->bd_ends_with( $term->name, $term_text ) ) {
				$term_ids['include'][] = $term->term_id;

				continue;
			}
			$term_ids['exclude'][] = $term->term_id;
		}

		return $term_ids;
	}

	/**
	 * Get terms which is contain specified string.
	 *
	 * @param string $term_text user input text.
	 * @param array  $options   user options.
	 *
	 * @return array term ids.
	 */
	protected function term_contains( $term_text, $options ) {
		$term_ids = array(
			'include' => array(),
			'exclude' => array(),
		);
		$terms    = get_terms(
			$options['taxonomy'], array(
				'hide_empty' => false,
			)
		);

		foreach ( $terms as $term ) {
			if ( strpos( $term->name, $term_text ) !== false ) {
				$term_ids['include'][] = $term->term_id;

				continue;
			}
			$term_ids['exclude'][] = $term->term_id;
		}

		return $term_ids;
	}
}
