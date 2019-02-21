<?php

namespace BulkWP\BulkDelete\Core\Addon;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Upsell pro add-ons.
 *
 * @since 6.0.0
 */
class Upseller {
	/**
	 * Setup hooks.
	 */
	public function load() {
		add_action( 'bd_after_modules', array( $this, 'load_upsell_modules' ) );
	}

	/**
	 * Load upsell modules after free modules.
	 *
	 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage $page The page to which the modules are added.
	 */
	public function load_upsell_modules( $page ) {
		$upsell_addon_details = $this->get_upsell_addon_details_for_page( $page );

		foreach ( $upsell_addon_details as $upsell_addon_detail ) {
			$page->add_module( new UpsellModule( new AddonUpsellInfo( $upsell_addon_detail ) ) );
		}
	}

	/**
	 * Get Upsell add-on to be shown on a particular page.
	 *
	 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage $page Delete Page in which upsell add-ons to be shown.
	 *
	 * @return array List of Upsell modules.
	 */
	protected function get_upsell_addon_details_for_page( $page ) {
		switch ( $page->get_item_type() ) {
			case 'posts':
				return $this->get_post_upsell_addons();
			case 'pages':
				return $this->get_page_upsell_addons();
			case 'users':
				return $this->get_user_upsell_addons();
			case 'metas':
				return $this->get_meta_upsell_addons();
			default:
				return array();
		}
	}

	/**
	 * Get upsell add-ons for delete posts page.
	 *
	 * Eventually this will come from a feed.
	 *
	 * @return array List of upsell add-on details.
	 */
	protected function get_post_upsell_addons() {
		$addon_upsell_details = array(
			array(
				'name'           => 'Bulk Delete Posts by Custom Field',
				'description'    => 'This addon adds the ability to delete posts based on custom field. This will be really useful, if your plugin or theme uses custom fields to store additional information about a post.',
				'slug'           => 'bulk-delete-posts-by-custom-field',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-posts-by-custom-field/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete Posts based on Custom Field (Post Meta)?',
				'upsell_message' => '<strong>Bulk Delete Posts by Custom Field</strong> add-on allows you to delete posts based on custom field (also known as post meta).',
			),
			array(
				'name'           => 'Bulk Delete Posts by Title',
				'description'    => 'This addon adds the ability to delete posts based on title.',
				'slug'           => 'bulk-delete-posts-by-title',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-posts-by-title/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete Posts based on title?',
				'upsell_message' => '<strong>Bulk Delete Posts by Title</strong> add-on allows you to delete posts based on title.',
			),
			array(
				'name'           => 'Bulk Delete Posts by Duplicate Title',
				'description'    => 'This addon adds the ability to delete posts based on duplicate title.',
				'slug'           => 'bulk-delete-posts-by-duplicate-title',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-posts-by-duplicate-title/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete Posts that have duplicate titles?',
				'upsell_message' => '<strong>Bulk Delete Posts by Duplicate Title</strong> add-on allows you to delete posts that have duplicate title.',
			),
			array(
				'name'           => 'Bulk Delete Posts by Content',
				'description'    => 'This addon adds the ability to delete posts based on content.',
				'slug'           => 'bulk-delete-posts-by-content',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-posts-by-content/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete Posts based on the post content?',
				'upsell_message' => '<strong>Bulk Delete Posts by Content</strong> add-on allows you to delete posts based on its post content.',
			),
			array(
				'name'           => 'Bulk Delete Posts by User',
				'description'    => 'This addon adds the ability to delete posts based on the author who created the post.',
				'slug'           => 'bulk-delete-posts-by-user',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-posts-by-user/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete Posts based on the user who created it?',
				'upsell_message' => '<strong>Bulk Delete Posts by User</strong> add-on allows you to delete posts based on user who created the post.',
			),
			array(
				'name'           => 'Bulk Delete Posts by Attachment',
				'description'    => 'This addon adds the ability to delete posts based on attachment.',
				'slug'           => 'bulk-delete-posts-by-attachment',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-posts-by-attachment/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete Posts based on whether it has an attachment?',
				'upsell_message' => "<strong>Bulk Delete Posts by Attachment</strong> add-on allows you to delete posts based on whether a post contains (or doesn't contain) an attachment.",
			),
			array(
				'name'           => 'Bulk Delete From Trash',
				'description'    => 'This addon adds the ability to delete posts or pages from trash.',
				'slug'           => 'bulk-delete-from-trash',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-from-trash/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete Posts that are in trash?',
				'upsell_message' => '<strong>Bulk Delete From Trash</strong> add-on allows you to delete posts that are in trash.',
			),
		);

		/**
		 * List of Upsell add-ons based on item type.
		 *
		 * @since 6.0.0
		 *
		 * @param array  $addon_upsell_details Add-on details.
		 * @param string $item_type            Item type.
		 */
		return apply_filters( 'bd_upsell_addons', $addon_upsell_details, 'posts' );
	}

	/**
	 * Get upsell add-ons for delete pages page.
	 *
	 * Eventually this will come from a feed.
	 *
	 * @return array List of upsell add-on details.
	 */
	protected function get_page_upsell_addons() {
		$addon_details = array(
			array(
				'name'           => 'Bulk Delete From Trash',
				'description'    => 'This addon adds the ability to delete posts or pages from trash.',
				'slug'           => 'bulk-delete-from-trash',
				'url'            => 'https://bulkwp.com/addons/bulk-delete-from-trash/',
				'buy_url'        => '',
				'upsell_title'   => 'Want to delete pages that are in trash?',
				'upsell_message' => '<strong>Bulk Delete From Trash</strong> add-on allows you to delete pages that are in trash.',
			),
		);

		/**
		 * List of Upsell add-ons based on item type.
		 *
		 * @since 6.0.0
		 *
		 * @param array  $addon_details Add-on details.
		 * @param string $item_type     Item type.
		 */
		return apply_filters( 'bd_upsell_addons', $addon_details, 'pages' );
	}

	/**
	 * Get upsell add-ons for delete users page.
	 *
	 * Eventually this will come from a feed.
	 *
	 * @return array List of upsell add-on details.
	 */
	protected function get_user_upsell_addons() {
		/**
		 * List of Upsell add-ons based on item type.
		 *
		 * @since 6.0.0
		 *
		 * @param array  $addon_details Add-on details.
		 * @param string $item_type     Item type.
		 */
		return apply_filters( 'bd_upsell_addons', array(), 'users' );
	}

	/**
	 * Get upsell add-ons for delete metas page.
	 *
	 * Eventually this will come from a feed.
	 *
	 * @return array List of upsell add-on details.
	 */
	protected function get_meta_upsell_addons() {
		/**
		 * List of Upsell add-ons based on item type.
		 *
		 * @since 6.0.0
		 *
		 * @param array  $addon_details Add-on details.
		 * @param string $item_type     Item type.
		 */
		return apply_filters( 'bd_upsell_addons', array(), 'metas' );
	}
}
