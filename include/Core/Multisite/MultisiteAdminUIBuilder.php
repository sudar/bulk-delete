<?php

namespace BulkWP\BulkDelete\Core\Multisite;

use BulkWP\BulkDelete\Core\Base\BasePage;
use BulkWP\BulkDelete\Core\Users\DeleteUsersInMultisitePage;
use BulkWP\BulkDelete\Core\Users\DeleteUsersPage;
use BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaInMultisiteModule;
use BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserRoleInMultisiteModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Builds UI in Multisite.
 *
 * @since 6.1.0
 */
class MultisiteAdminUIBuilder {
	/**
	 * Path to the main plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * List of Primary Network Admin pages.
	 *
	 * @var \BulkWP\BulkDelete\Core\Base\BaseDeletePage[]
	 */
	protected $network_primary_pages = array();

	/**
	 * MultisiteAdminUIBuilder constructor.
	 *
	 * @param string $plugin_file Path to the main plugin file.
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Setup actions and load the UI.
	 */
	public function load() {
		if ( ! is_multisite() ) {
			return;
		}

		add_action( 'network_admin_menu', array( $this, 'on_network_admin_menu' ) );
	}

	/**
	 * Triggered when the `network_admin_menu` hook is fired.
	 *
	 * Register all multisite admin pages.
	 */
	public function on_network_admin_menu() {
		foreach ( $this->get_primary_network_pages() as $page ) {
			$page->register();
		}

		/**
		 * Runs just after adding all *delete* menu items to Bulk WP Network main menu.
		 *
		 * This action is primarily for adding extra *delete* menu items to the Bulk WP main menu.
		 *
		 * @since 6.1.0
		 */
		do_action( 'bd_after_primary_network_menus' );

		/**
		 * Runs just before adding non-action menu items to Bulk WP Network main menu.
		 *
		 * This action is primarily for adding extra menu items before non-action menu items to the Bulk WP Network main menu.
		 *
		 * @since 6.1.0
		 */
		do_action( 'bd_before_secondary_network_menus' );

		foreach ( $this->get_secondary_network_pages() as $page ) {
			$page->register();
		}

		/**
		 * Runs just after adding all menu items to Bulk WP Network main menu.
		 *
		 * This action is primarily for adding extra menu items to the Bulk WP Network main menu.
		 *
		 * @since 6.1.0
		 */
		do_action( 'bd_after_all_network_menus' );
	}

	/**
	 * Get the list of registered network admin pages.
	 *
	 * @return \BulkWP\BulkDelete\Core\Base\BaseDeletePage[] List of Primary Admin pages.
	 */
	protected function get_primary_network_pages() {
		if ( empty( $this->network_primary_pages ) ) {
			$this->load_primary_network_pages();
		}

		return $this->network_primary_pages;
	}

	/**
	 * Load Primary network admin pages.
	 *
	 * The pages need to be loaded in `init` hook, since the association between page and modules is needed in cron requests.
	 */
	protected function load_primary_network_pages() {
		$users_page = $this->get_delete_users_network_admin_page();

		$this->network_primary_pages[ $users_page->get_page_slug() ] = $users_page;

		/**
		 * List of primary network admin pages.
		 *
		 * @since 6.1.0
		 *
		 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage[] List of Admin pages.
		 */
		$this->network_primary_pages = apply_filters( 'bd_primary_network_pages', $this->network_primary_pages );
	}

	/**
	 * Get the Delete Users in Multisite page.
	 *
	 * @return \BulkWP\BulkDelete\Core\Users\DeleteUsersInMultisitePage
	 */
	protected function get_delete_users_network_admin_page() {
		$users_page = new DeleteUsersInMultisitePage( $this->get_plugin_file() );

		$users_page->add_module( new DeleteUsersByUserRoleInMultisiteModule() );
		$users_page->add_module( new DeleteUsersByUserMetaInMultisiteModule() );

		/**
		 * After the modules are registered in the delete users page.
		 *
		 * @since 6.1.0
		 *
		 * @param DeleteUsersPage $users_page The page in which the modules are registered.
		 */
		do_action( "bd_after_network_modules_{$users_page->get_page_slug()}", $users_page );

		/**
		 * After the modules are registered in a delete page.
		 *
		 * @since 6.1.0
		 *
		 * @param BasePage $users_page The page in which the modules are registered.
		 */
		do_action( 'bd_after_network_modules', $users_page );

		return $users_page;
	}

	/**
	 * Used for getting modules for multisite network admin.
	 * Get the module object instance by page slug and module class name.
	 *
	 * @param string $page_slug         Page Slug.
	 * @param string $module_class_name Module class name.
	 *
	 * @return \BulkWP\BulkDelete\Core\Base\BaseModule|null Module object instance or null if no match found.
	 */
	public function get_network_module( $page_slug, $module_class_name ) {
		$page = $this->get_network_page( $page_slug );

		if ( is_null( $page ) ) {
			return null;
		}

		return $page->get_module( $module_class_name );
	}

	/**
	 * Used for multisite network admin dashboard.
	 * Get the page object instance by page slug.
	 *
	 * @param string $page_slug Page slug.
	 *
	 * @return \BulkWP\BulkDelete\Core\Base\BaseDeletePage|null Page object instance or null if no match found.
	 */
	public function get_network_page( $page_slug ) {
		$pages = $this->get_primary_network_pages();

		if ( ! isset( $pages[ $page_slug ] ) ) {
			return null;
		}

		return $pages[ $page_slug ];
	}

	/**
	 * Get the secondary list of network pages.
	 *
	 * @return array
	 */
	protected function get_secondary_network_pages() {
		return array();
	}

	/**
	 * Get Plugin file.
	 *
	 * @return string Plugin File.
	 */
	protected function get_plugin_file() {
		return $this->plugin_file;
	}
}
