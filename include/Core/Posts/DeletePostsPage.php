<?php

namespace BulkWP\BulkDelete\Core\Posts;

use BulkWP\BulkDelete\Core\Base\MetaboxPage;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Posts Page.
 *
 * Shows the list of modules that allows you to delete posts.
 *
 * @since 6.0.0
 */
class DeletePostsPage extends MetaboxPage {

	/**
	 * Position in which the Bulk WP menu should appear.
	 */
	const MENU_POSITION = '26';

	/**
	 * Initialize and setup variables.
	 */
	protected function initialize() {
		$this->page_slug  = 'bulk-delete-posts';
		$this->item_type  = 'posts';
		$this->capability = 'delete_posts';

		$this->label = array(
			'page_title' => __( 'Bulk Delete Posts', 'bulk-delete' ),
			'menu_title' => __( 'Bulk Delete Posts', 'bulk-delete' ),
		);

		$this->messages = array(
			'warning_message' => __( 'WARNING: Posts deleted once cannot be retrieved back. Use with caution.', 'bulk-delete' ),
		);

		add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 2 );
	}

	public function register() {
		parent::register();

		add_menu_page(
			__( 'Bulk WP', 'bulk-delete' ),
			__( 'Bulk WP', 'bulk-delete' ),
			$this->capability,
			$this->page_slug,
			array( $this, 'render_page' ),
			'dashicons-trash',
			$this->get_bulkwp_menu_position()
		);
	}

	/**
	 * Get the Menu position of BulkWP menu.
	 *
	 * @return int Menu position.
	 */
	protected function get_bulkwp_menu_position() {
		/**
		 * Bulk WP Menu position.
		 *
		 * @since 6.0.0
		 *
		 * @param int Menu Position.
		 */
		return apply_filters( 'bd_bulkwp_menu_position', self::MENU_POSITION );
	}

	/**
	 * Adds setting links in plugin listing page.
	 * Based on http://striderweb.com/nerdaphernalia/2008/06/wp-use-action-links/.
	 *
	 * @param array  $links List of current links
	 * @param string $file  Plugin filename
	 *
	 * @return array $links Modified list of links
	 */
	public function add_plugin_action_links( $links, $file ) {
		$this_plugin = plugin_basename( Bulk_Delete::$PLUGIN_FILE );

		if ( $file == $this_plugin ) {
			$delete_users_link = '<a href="admin.php?page=' . $this->page_slug . '">' . __( 'Bulk Delete Users', 'bulk-delete' ) . '</a>';
			array_unshift( $links, $delete_users_link ); // before other links
		}

		return $links;
	}

	/**
	 * Add Help tabs.
	 *
	 * @since 5.5
	 *
	 * @param mixed $help_tabs
	 */
	protected function add_help_tab( $help_tabs ) {
		$overview_tab = array(
			'title'    => __( 'Overview', 'bulk-delete' ),
			'id'       => 'overview_tab',
			'content'  => '<p>' . __( 'This screen contains different modules that allows you to delete users or schedule them for deletion.', 'bulk-delete' ) . '</p>',
			'callback' => false,
		);
		$help_tabs['overview_tab'] = $overview_tab;

		return $help_tabs;
	}
}
