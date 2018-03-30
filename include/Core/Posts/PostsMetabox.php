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
	public function render_private_post_settings() {
		bd_render_private_post_settings( $this->field_slug );
	}
}
