<?php

namespace BulkWP\BulkDelete\Core\Terms\Modules;

use BulkWP\BulkDelete\Core\Terms\TermsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Terms by Postfix and Prefix.
 *
 * @since 6.0.0
 */
class DeleteTermsByPostfixAndPrefixModule extends TermsModule {
	protected function initialize() {
		$this->item_type     = 'terms';
		$this->field_slug    = 'by_name';
		$this->meta_box_slug = 'bd_by_name';
		$this->action        = 'delete_terms_by_name';
		$this->cron_hook     = 'do-bulk-delete-term-name';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-category/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-sc';
		$this->messages      = array(
			'box_label'  => __( 'By Terms by Name', 'bulk-delete' ),
			'scheduled'  => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
			'cron_label' => __( 'Delete Terms By Name', 'bulk-delete' ),
		);
	}

	/**
	 * Render Delete terms by postfix and prefix box.
	 */
	public function render() {
		?>
		<!-- Category Start-->
		<h4><?php _e( 'Select the taxonomy from which you want to delete', 'bulk-delete' ); ?></h4>
		<fieldset class="options">
			<table class="optiontable">
				<?php $this->render_taxonomy_dropdown(); ?>
			</table>

			<table class="optiontable">
				<?php $this->render_term_options(); ?>
			</table>

			<table class="optiontable">
				<?php
				$this->render_have_post_settings();
				?>
			</table>

		</fieldset>
		<?php
		$this->render_submit_button();
	}

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
		$options['taxonomy']     = bd_array_get( $request, 'smbd_' . $this->field_slug . '_taxonomy' );
		$options['term_opt']     = bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_opt' );
		$options['term_text']    = bd_array_get( $request, 'smbd_' . $this->field_slug . '_term_text' );
		$options['no_posts']     = bd_array_get( $request, 'smbd_' . $this->field_slug . '_no_posts' );

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
		$query = array();

		if ( $options['term_opt'] == 'equal_to' ) {
			$query['name__like'] = $options['term_text'];
		}elseif ( $options['term_opt'] == 'not_equal_to' ) {
			$query['name__like'] = '';
		}elseif ( $options['term_opt'] == 'starts' ) {
			$query['name__like'] = '';
		}elseif ( $options['term_opt'] == 'ends' ) {
			$query['name__like'] = '';
		}elseif ( $options['term_opt'] == 'contains' ) {
			$query['name__like'] = '';
		}elseif ( $options['term_opt'] == 'non_contains' ) {
			$query['name__like'] = '';
		}

		if( isset($options['no_posts']) ){
			$query['count'] = '';
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
		return _n( 'Deleted %d post with the selected post category', 'Deleted %d posts with the selected post category', $items_deleted, 'bulk-delete' );
	}
}
