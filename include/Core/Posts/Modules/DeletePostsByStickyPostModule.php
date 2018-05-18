<?php

namespace BulkWP\BulkDelete\Core\Posts\Modules;

use BulkWP\BulkDelete\Core\Posts\PostsModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Sticky Post
 *
 * @since 6.0.0
 */
class DeletePostsByStickyPostModule extends PostsModule {
	/**
	 * Base parameters setup.
	 */
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'sticky_post';
		$this->meta_box_slug = 'bd_by_sticky_post';
		$this->action        = 'delete_posts_by_sticky_post';
		$this->messages      = array(
			'box_label'  => __( 'By Sticky Post', 'bulk-delete' ),
		);
	}

	/**
	 * Render Delete posts by tag box.
	 */
	public function render() {
		if ( ! $this->are_stickt_post_present() ) : ?>
			<h4>
				<?php _e( 'There are no sticky post present in this WordPress installation.', 'bulk-delete' ); ?>
			</h4>
			<?php return; ?>
		<?php endif; ?>

		<h4><?php _e( 'Select the sticky post from which you want to delete', 'bulk-delete' ); ?></h4>

		<!-- Tags start-->
		<fieldset class="options">
			<table class="form-table">
				<tr>
					<td scope="row" colspan="2">
						<?php $this->render_sticky_post_dropdown(); ?>
					</td>
				</tr>
			</table>

			<table class="optiontable">
				<?php
				$this->render_filtering_table_header();
				$this->render_delete_settings();
				?>
			</table>
		</fieldset>
<?php
		$this->render_submit_button();
	}

	public function filter_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateStickyPostSelect2';
		$js_array['error_msg'][ $this->action ]  = 'selectStickyPost';
		$js_array['msg']['selectStickyPost']       = __( 'Please select at least one sticky post', 'bulk-delete' );

		$js_array['dt_iterators'][] = '_' . $this->field_slug;

		return $js_array;
	}

	/**
	 * Process delete posts user inputs by sticky_post.
	 *
	 * @param array $request Request array.
	 * @param array $options Options for deleting posts.
	 *
	 * @return array $options  Inputs from user for posts that were need to delete
	 */
	protected function convert_user_input_to_options( $request, $options ) {
		$options['selected_posts'] = bd_array_get( $request, 'smbd_sticky_post' );

		$options['remove_sticky'] = bd_array_get( $request, 'smbd_sticky_post_remove_sticky' );

		return $options;
	}

	protected function build_query( $options ) {
		$query = array();

		if ( in_array( 'all', $options['selected_posts'], true ) ) {
			$query['post__in'] = get_option( 'sticky_posts' );
		} else {
			$query['post__in'] = $options['selected_posts'];
		}

		return $query;
	}

	/**
	 * Response message for deleting posts.
	 *
	 * @param int $items_deleted count of items deleted.
	 *
	 * @return string Response message
	 */
	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted %d sticky post', 'Deleted %d sticky posts', $items_deleted, 'bulk-delete' );
	}
}
