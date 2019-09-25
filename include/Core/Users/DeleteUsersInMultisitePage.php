<?php

namespace BulkWP\BulkDelete\Core\Users;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Users in Multisite Page.
 *
 * Shows the list of modules that allows you to delete users in multisite.
 *
 * @since 6.1.0
 */
class DeleteUsersInMultisitePage extends DeleteUsersPage {

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		parent::initialize();

		$this->page_slug = 'bulk-delete-users-in-multisite';

		$this->show_link_in_plugin_list = false;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function register() {
		add_menu_page(
			__( 'Bulk WP', 'bulk-delete' ),
			__( 'Bulk WP', 'bulk-delete' ),
			$this->capability,
			$this->page_slug,
			array( $this, 'render_page' ),
			'dashicons-trash',
			$this->get_bulkwp_menu_position()
		);

		parent::register();
	}

	protected function get_top_level_menu_slug() {
		return $this->page_slug;
	}
}
