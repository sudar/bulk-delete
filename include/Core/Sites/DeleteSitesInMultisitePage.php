<?php

namespace BulkWP\BulkDelete\Core\Sites;

use BulkWP\BulkDelete\Core\Base\BaseDeletePage;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Sites Page.
 *
 * Shows the list of modules that provides the ability to delete sites.
 *
 * @since 6.2.0
 */
class DeleteSitesInMultisitePage extends BaseDeletePage {
	/**
	 * Initialize and setup variables.
	 *
	 * @since 6.2.0
	 */
	protected function initialize() {
		$this->page_slug = 'bulk-delete-sites';
		$this->item_type = 'sites';

		$this->label = array(
			'page_title' => __( 'Bulk Delete Sites', 'bulk-delete' ),
			'menu_title' => __( 'Bulk Delete Sites', 'bulk-delete' ),
		);

		$this->messages = array(
			'warning_message' => __( 'WARNING: Sites deleted once cannot be retrieved back. Use with caution.', 'bulk-delete' ),
		);

		$this->show_link_in_plugin_list = true;
	}

	/**
	 * Add Help tabs.
	 *
	 * @since 6.2.0
	 *
	 * @param array $help_tabs List of help tabs.
	 *
	 * @return array Modified list of help tabs.
	 */
	protected function add_help_tab( $help_tabs ) {
		$overview_tab = array(
			'title'    => __( 'Overview', 'bulk-delete' ),
			'id'       => 'overview_tab',
			'content'  => '<p>' . __( 'This screen contains different modules that allows you to delete sites.', 'bulk-delete' ) . '</p>',
			'callback' => false,
		);

		$help_tabs['overview_tab'] = $overview_tab;

		return $help_tabs;
	}

	protected function get_top_level_menu_slug() {
		return 'bulk-delete-users-in-multisite';
	}
}
