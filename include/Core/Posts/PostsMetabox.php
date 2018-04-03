<?php
namespace BulkWP\BulkDelete\Core\Posts;

use BulkWP\BulkDelete\Core\Base\BaseMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Metabox for deleting posts.
 *
 * @since 6.0.0
 */
abstract class PostsMetabox extends BaseMetabox {
	protected $item_type = 'posts';

	public function filter_js_array( $js_array ) {
		$js_array['msg']['deletePostsWarning'] = __( 'Are you sure you want to delete all the posts based on the selected option?', 'bulk-delete' );
		$js_array['msg']['selectPostOption']   = __( 'Please select posts from at least one option', 'bulk-delete' );

		$js_array['validators']['delete_posts_by_category'] = 'validateSelect2';
		$js_array['error_msg']['delete_posts_by_category']  = 'selectCategory';
		$js_array['msg']['selectCategory']                  = __( 'Please select at least one category', 'bulk-delete' );

		$js_array['validators']['delete_posts_by_tag']     = 'validateSelect2';
		$js_array['error_msg']['delete_posts_by_category'] = 'selectTag';
		$js_array['msg']['selectTag']                      = __( 'Please select at least one tag', 'bulk-delete' );

		$js_array['validators']['delete_posts_by_url'] = 'validateUrl';
		$js_array['error_msg']['delete_posts_by_url']  = 'enterUrl';
		$js_array['msg']['enterUrl']                   = __( 'Please enter at least one post url', 'bulk-delete' );

		$js_array['dt_iterators'][] = '_cats';
		$js_array['dt_iterators'][] = '_tags';
		$js_array['dt_iterators'][] = '_taxs';
		$js_array['dt_iterators'][] = '_types';
		$js_array['dt_iterators'][] = '_post_status';

		return $js_array;
	}

	/**
	 * Render the "private post" setting fields.
	 */
	protected function render_private_post_settings() {
		bd_render_private_post_settings( $this->field_slug );
	}

	/**
	 * Render Post type dropdown.
	 */
	protected function render_post_type_dropdown() {
		bd_render_post_type_dropdown( $this->field_slug );
	}

	/**
	 * Render Category dropdown.
	 */
	protected function render_category_dropdown() {
		$enhanced_select_threshold = $this->get_enhanced_select_threshold();

		$categories = $this->get_categories();

		$class_name = 'select2';
		if ( count( $categories ) >= $enhanced_select_threshold ) {
			$class_name = 'select2-ajax';
		}
		?>

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_category[]" multiple data-placeholder="<?php _e( 'Select Categories', 'bulk-delete' ); ?>"
			class="<?php echo sanitize_html_class( $class_name ); ?>" data-taxonomy="category">

			<option value="all">
				<?php _e( 'All Categories', 'bulk-delete' ); ?>
			</option>

			<?php foreach ( $categories as $category ) : ?>
				<option value="<?php echo absint( $category->cat_ID ); ?>">
					<?php echo esc_html( $category->cat_name ), ' (', absint( $category->count ), ' ', __( 'Posts', 'bulk-delete' ), ')'; ?>
				</option>
			<?php endforeach; ?>

		</select>
	<?php
	}

	/**
	 * Delete sticky posts.
	 *
	 * @param bool $force_delete Whether to bypass trash and force deletion.
	 *
	 * @return int Number of posts deleted.
	 */
	protected function delete_sticky_posts( $force_delete ) {
		$sticky_post_ids = get_option( 'sticky_posts' );

		foreach ( $sticky_post_ids as $sticky_post_id ) {
			wp_delete_post( $sticky_post_id, $force_delete );
		}

		return count( $sticky_post_ids );
	}

	/**
	 * Get the list of post statuses.
	 *
	 * This includes all custom post status, but excludes built-in private posts.
	 *
	 * @return array List of post status objects.
	 */
	protected function get_post_statuses() {
		return bd_get_post_statuses();
	}

	/**
	 * Get the threshold after which enhanced select should be used.
	 *
	 * @return int Threshold.
	 */
	protected function get_enhanced_select_threshold() {
		/**
		 * Filter the enhanced select threshold.
		 *
		 * @since 6.0.0
		 *
		 * @param int Threshold.
		 */
		return apply_filters( 'bd_enhanced_select_threshold', 1000 );
	}

	/**
	 * Get the list of categories.
	 *
	 * @return array List of categories.
	 */
	protected function get_categories() {
		$enhanced_select_threshold = $this->get_enhanced_select_threshold();

		$categories = get_categories(
			array(
				'hide_empty' => false,
				'number'     => $enhanced_select_threshold,
			)
		);

		return $categories;
	}

	/**
	 * Delete posts by ids.
	 *
	 * @param int[] $post_ids     List of post ids to delete.
	 * @param bool  $force_delete True to force delete posts, False otherwise.
	 *
	 * @return int Number of posts deleted.
	 */
	protected function delete_posts_by_id( $post_ids, $force_delete ) {
		foreach ( $post_ids as $post_id ) {
			// `$force_delete` parameter to `wp_delete_post` won't work for custom post types.
			// See https://core.trac.wordpress.org/ticket/43672
			if ( $force_delete ) {
				wp_delete_post( $post_id, true );
			} else {
				wp_trash_post( $post_id );
			}
		}

		return count( $post_ids );
	}
}
