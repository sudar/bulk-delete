<?php

namespace BulkWP\BulkDelete\Core\Comments;

use BulkWP\BulkDelete\Core\Base\BaseDeletePage;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Comments Page.
 *
 * Shows the list of modules that allows you to delete comments.
 *
 * @since 6.1.0
 */
class DeleteCommentsPage extends BaseDeletePage {
	/**
	 * Initialize and setup variables.
	 */
	protected function initialize() {
		$this->page_slug = 'bulk-delete-comments';
		$this->item_type = 'comments';

		$this->label = array(
			'page_title' => __( 'Bulk Delete Comments', 'bulk-delete' ),
			'menu_title' => __( 'Bulk Delete Comments', 'bulk-delete' ),
		);

		$this->messages = array(
			'warning_message' => __( 'WARNING: Comments deleted once cannot be retrieved back. Use with caution.', 'bulk-delete' ),
		);
	}

	/**
	 * Add Help tabs.
	 *
	 * @param array $help_tabs Help tabs.
	 *
	 * @return array Modified list of Help tabs.
	 */
	protected function add_help_tab( $help_tabs ) {
		$overview_tab = array(
			'title'    => __( 'Overview', 'bulk-delete' ),
			'id'       => 'overview_tab',
			'content'  => '<p>' . __( 'This screen contains different modules that allows you to delete comments or schedule them for deletion.', 'bulk-delete' ) . '</p>',
			'callback' => false,
		);

		$help_tabs['overview_tab'] = $overview_tab;

		return $help_tabs;
	}
}
