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
		$categories = $this->get_categories();
		?>

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_category[]" data-placeholder="<?php _e( 'Select Categories', 'bulk-delete' ); ?>"
				class="<?php echo sanitize_html_class( $this->enable_ajax_if_needed_to_dropdown_class_name( count( $categories ), 'select2-taxonomy' ) ); ?>"
				data-taxonomy="category" multiple>

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
	 * Render Tags dropdown.
	 */
	protected function render_tags_dropdown() {
		$tags = $this->get_tags();
		?>

		<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>[]" data-placeholder="<?php _e( 'Select Tags', 'bulk-delete' ); ?>"
				class="<?php echo sanitize_html_class( $this->enable_ajax_if_needed_to_dropdown_class_name( count( $tags ), 'select2-taxonomy' ) ); ?>"
				data-taxonomy="post_tag" multiple>

			<option value="all">
				<?php _e( 'All Tags', 'bulk-delete' ); ?>
			</option>

			<?php foreach ( $tags as $tag ) : ?>
				<option value="<?php echo absint( $tag->term_id ); ?>">
					<?php echo esc_html( $tag->name ), ' (', absint( $tag->count ), ' ', __( 'Posts', 'bulk-delete' ), ')'; ?>
				</option>
			<?php endforeach; ?>
		</select>
	<?php
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
	 * Are tags present in this WordPress installation?
	 *
	 * Only one tag is retrieved to check if tags are present for performance reasons.
	 *
	 * @return bool True if tags are present, False otherwise.
	 */
	protected function are_tags_present() {
		$tags = $this->get_tags( 1 );

		return ( count( $tags ) > 0 );
	}

	/**
	 * Get the list of tags.
	 *
	 * @param int $max_count The maximum number of tags to be returned (Optional). Default 0.
	 *                       If 0 then the maximum number of tags specified in `get_enhanced_select_threshold` will be returned.
	 *
	 * @return array List of tags.
	 */
	protected function get_tags( $max_count = 0 ) {
		if ( absint( $max_count ) === 0 ) {
			$max_count = $this->get_enhanced_select_threshold();
		}

		$tags = get_tags(
			array(
				'hide_empty' => false,
				'number'     => $max_count,
			)
		);

		return $tags;
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

		if ( ! is_array( $sticky_post_ids ) ) {
			return 0;
		}

		return $this->delete_posts_by_id( $sticky_post_ids, $force_delete );
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

	/**
	 * Generate display name from post type and status.
	 *
	 * @static
	 *
	 * @param string $str
	 *
	 * @return string Label
	 */
	public static function display_post_type_status( $str ) {
		$type_status = self::split_post_type_status( $str );

		$status = $type_status['status'];
		$type   = $type_status['type'];
		$label  = '';

		switch ( $status ) {
			case 'private':
				$label = $type . ' - Private Posts';
				break;
			case 'future':
				$label = $type . ' - Scheduled Posts';
				break;
			case 'draft':
				$label = $type . ' - Draft Posts';
				break;
			case 'pending':
				$label = $type . ' - Pending Posts';
				break;
			case 'publish':
				$label = $type . ' - Published Posts';
				break;
		}

		return $label;
	}

	/**
	 * Split post type and status.
	 *
	 * @static
	 * @access public
	 *
	 * @param string $str
	 *
	 * @return array
	 */
	public static function split_post_type_status( $str ) {
		$type_status = array();

		$str_arr = explode( '-', $str );

		if ( count( $str_arr ) > 1 ) {
			$type_status['status'] = end( $str_arr );
			$type_status['type']   = implode( '-', array_slice( $str_arr, 0, -1 ) );
		} else {
			$type_status['status'] = 'publish';
			$type_status['type']   = $str;
		}

		return $type_status;
	}
}
