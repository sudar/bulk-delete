<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\BulkDelete\Core\Terms\TermsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Terms by Post Count.
 *
 * @since 6.0.0
 */
class DeleteTermsByPostCountModule extends TermsModule {
	/**
	 * Initialize the values.
	 */
	protected function initialize() {
		$this->item_type     = 'terms';
		$this->field_slug    = 'terms_by_post_count';
		$this->meta_box_slug = 'bd_delete_terms_by_post_count';
		$this->action        = 'delete_terms_by_post_count';
		$this->cron_hook     = 'do-bulk-delete-term-by-post-count';
		$this->scheduler_url = '';
		$this->messages      = array(
			'box_label'  => __( 'Delete Terms by Post Count', 'bulk-delete' ),
			'scheduled'  => __( 'The selected terms are scheduled for deletion', 'bulk-delete' ),
			'cron_label' => __( 'Delete Terms By Post Count', 'bulk-delete' ),
		);
	}

	/**
	 * Render Delete terms by postfix and prefix box.
	 */
	public function render() {
		?>
		<!-- Category Start-->
		<h4><?php _e( 'Select the taxonomy from which you want to delete terms', 'bulk-delete' ); ?></h4>
		<fieldset class="options">
			<table class="optiontable">
				<?php $this->render_taxonomy_dropdown(); ?>
			</table>

			<h4><?php _e( 'Select the post type', 'bulk-delete' ); ?></h4>
			<table class="optiontable">
				<?php $this->render_post_type_dropdown(); ?>
			</table>

			<table class="optiontable">
				<?php $this->render_term_options(); ?>
			</table>

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

		return $js_array;
	}

	/**
	 * Process delete posts user inputs by category.
	 *
	 * @param array $request Request array.
	 * @param array $options Options for deleting posts.
	 *
	 * @return array $options  Inputs from user for posts that were need to delete
	 */
	protected function convert_user_input_to_options( $request, $options ) {
		$options['taxonomy']  = bd_array_get( $request, 'smbd_' . $this->field_slug . '_taxonomy' );
		$options['post_type'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_post_type' );
		$options['term_opt']  = bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_opt' );
		$options['term_text'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_text' );

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
		$query     = array();
		$taxonomy  = $options['taxonomy'];
		$term_text = $options['term_text'];

		if ( isset( $term_text ) ) {
			$query['taxonomy'] = $taxonomy;
		}

		$term_ids         = $this->term_count_query( $options );
		$query['include'] = $term_ids;

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
		return _n( 'Deleted %d term with the selected options', 'Deleted %d terms with the selected terms count', $items_deleted, 'bulk-delete' );
	}
}
