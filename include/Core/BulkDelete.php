<?php

namespace BulkWP\BulkDelete\Core;

use BulkWP\BulkDelete\Core\Base\BasePage;
use BulkWP\BulkDelete\Core\Cron\CronListPage;
use BulkWP\BulkDelete\Core\Metas\DeleteMetasPage;
use BulkWP\BulkDelete\Core\Metas\Modules\DeleteCommentMetaModule;
use BulkWP\BulkDelete\Core\Metas\Modules\DeletePostMetaModule;
use BulkWP\BulkDelete\Core\Metas\Modules\DeleteUserMetaModule;
use BulkWP\BulkDelete\Core\Pages\DeletePagesPage;
use BulkWP\BulkDelete\Core\Pages\Modules\DeletePagesByStatusModule;
use BulkWP\BulkDelete\Core\Posts\DeletePostsPage;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByCategoryModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByPostTypeModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByRevisionModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStatusModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByStickyPostModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTagModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByTaxonomyModule;
use BulkWP\BulkDelete\Core\Posts\Modules\DeletePostsByURLModule;
use BulkWP\BulkDelete\Core\Users\DeleteUsersPage;
use BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserMetaModule;
use BulkWP\BulkDelete\Core\Users\Modules\DeleteUsersByUserRoleModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Main Plugin class.
 *
 * @since 5.0 Converted to Singleton
 * @since 6.0.0 Renamed to BulkDelete and added namespace.
 */
final class BulkDelete {
	/**
	 * The one true BulkDelete instance.
	 *
	 * @var BulkDelete
	 *
	 * @since 5.0
	 */
	private static $instance;

	/**
	 * Path to the main plugin file.
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Path where translations are stored.
	 *
	 * @var string
	 */
	private $translations_path;

	/**
	 * Has the plugin loaded?
	 *
	 * @since 6.0.0
	 *
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * Controller that handles all requests and nonce checks.
	 *
	 * @var \BulkWP\BulkDelete\Core\Controller
	 */
	private $controller;

	/**
	 * Bulk Delete Autoloader.
	 *
	 * Will be used by add-ons to extend the namespace.
	 *
	 * @var \BulkWP\BulkDelete\BulkDeleteAutoloader
	 */
	private $loader;

	/**
	 * List of Primary Admin pages.
	 *
	 * @var BasePage[]
	 *
	 * @since 6.0.0
	 */
	private $primary_pages = array();

	/**
	 * List of Secondary Admin pages.
	 *
	 * @var BasePage[]
	 *
	 * @since 6.0.0
	 */
	private $secondary_pages = array();

	/**
	 * Plugin version.
	 */
	const VERSION = '5.6.1';

	/**
	 * Set the BulkDelete constructor as private.
	 *
	 * An instance should be created by calling the `get_instance` method.
	 *
	 * @see BulkDelete::get_instance()
	 */
	private function __construct() {}

	/**
	 * Main BulkDelete Instance.
	 *
	 * Insures that only one instance of BulkDelete exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since     5.0
	 * @static
	 * @staticvar array $instance
	 *
	 * @return BulkDelete The one true instance of BulkDelete.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BulkDelete ) ) {
			self::$instance = new BulkDelete();
		}

		return self::$instance;
	}

	/**
	 * Load the plugin if it is not loaded.
	 *
	 * This function will be invoked in the `plugins_loaded` hook.
	 */
	public function load() {
		if ( $this->loaded ) {
			return;
		}

		$this->load_dependencies();
		$this->setup_actions();

		$this->loaded = true;

		/**
		 * Bulk Delete plugin loaded.
		 *
		 * @since 6.0.0
		 *
		 * @param string Plugin main file.
		 */
		do_action( 'bd_loaded', $this->get_plugin_file() );
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since  5.0
	 * @access protected
	 *
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( "This class can't be cloned. Use `get_instance()` method to get an instance.", 'bulk-delete' ), '5.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since  5.0
	 * @access protected
	 *
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( "This class can't be serialized. Use `get_instance()` method to get an instance.", 'bulk-delete' ), '5.0' );
	}

	/**
	 * Load all dependencies.
	 *
	 * @since 6.0.0
	 */
	private function load_dependencies() {
		$this->controller = new Controller();
		$this->controller->load();
	}

	/**
	 * Loads the plugin's actions and hooks.
	 *
	 * @access private
	 *
	 * @since  5.0
	 *
	 * @return void
	 */
	private function setup_actions() {
		add_action( 'init', array( $this, 'on_init' ) );

		add_action( 'admin_menu', array( $this, 'on_admin_menu' ) );
	}

	/**
	 * Triggered when the `init` hook is fired.
	 *
	 * @since 6.0.0
	 */
	public function on_init() {
		$this->load_textdomain();
	}

	/**
	 * Loads the plugin language files.
	 *
	 * @since  5.0
	 */
	private function load_textdomain() {
		load_plugin_textdomain( 'bulk-delete', false, $this->get_translations_path() );
	}

	/**
	 * Triggered when the `admin_menu` hook is fired.
	 *
	 * Register all admin pages.
	 *
	 * @since 6.0.0
	 */
	public function on_admin_menu() {
		foreach ( $this->get_primary_pages() as $page ) {
			$page->register();
		}

		\Bulk_Delete_Misc::add_menu();

		/**
		 * Runs just after adding all *delete* menu items to Bulk WP main menu.
		 *
		 * This action is primarily for adding extra *delete* menu items to the Bulk WP main menu.
		 *
		 * @since 5.3
		 */
		do_action( 'bd_after_primary_menus' );

		/**
		 * Runs just before adding non-action menu items to Bulk WP main menu.
		 *
		 * This action is primarily for adding extra menu items before non-action menu items to the Bulk WP main menu.
		 *
		 * @since 5.3
		 */
		do_action( 'bd_before_secondary_menus' );

		foreach ( $this->get_secondary_pages() as $page ) {
			$page->register();
		}

		$this->addon_page = add_submenu_page(
			\Bulk_Delete::POSTS_PAGE_SLUG,
			__( 'Addon Licenses', 'bulk-delete' ),
			__( 'Addon Licenses', 'bulk-delete' ),
			'activate_plugins',
			\Bulk_Delete::ADDON_PAGE_SLUG,
			array( 'BD_License', 'display_addon_page' )
		);

		\BD_System_Info_page::factory();

		/**
		 * Runs just after adding all menu items to Bulk WP main menu.
		 *
		 * This action is primarily for adding extra menu items to the Bulk WP main menu.
		 *
		 * @since 5.3
		 */
		do_action( 'bd_after_all_menus' );
	}

	/**
	 * Get the list of registered admin pages.
	 *
	 * @since 6.0.0
	 *
	 * @return \BulkWP\BulkDelete\Core\Base\BaseDeletePage[] List of Primary Admin pages.
	 */
	public function get_primary_pages() {
		if ( empty( $this->primary_pages ) ) {
			$posts_page = $this->get_delete_posts_admin_page();
			$pages_page = $this->get_delete_pages_admin_page();
			$users_page = $this->get_delete_users_admin_page();
			$metas_page = $this->get_delete_metas_admin_page();

			$this->primary_pages[ $posts_page->get_page_slug() ] = $posts_page;
			$this->primary_pages[ $pages_page->get_page_slug() ] = $pages_page;
			$this->primary_pages[ $users_page->get_page_slug() ] = $users_page;
			$this->primary_pages[ $metas_page->get_page_slug() ] = $metas_page;
		}

		/**
		 * List of primary admin pages.
		 *
		 * @since 6.0.0
		 *
		 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage[] List of Admin pages.
		 */
		return apply_filters( 'bd_primary_pages', $this->primary_pages );
	}

	/**
	 * Get Bulk Delete Posts admin page.
	 *
	 * @return \BulkWP\BulkDelete\Core\Posts\DeletePostsPage
	 */
	private function get_delete_posts_admin_page() {
		$posts_page = new DeletePostsPage( $this->get_plugin_file() );

		$posts_page->add_metabox( new DeletePostsByStatusModule() );
		$posts_page->add_metabox( new DeletePostsByCategoryModule() );
		$posts_page->add_metabox( new DeletePostsByTagModule() );
		$posts_page->add_metabox( new DeletePostsByTaxonomyModule() );
		$posts_page->add_metabox( new DeletePostsByPostTypeModule() );
		$posts_page->add_metabox( new DeletePostsByURLModule() );
		$posts_page->add_metabox( new DeletePostsByRevisionModule() );
		$posts_page->add_metabox( new DeletePostsByStickyPostModule() );

		return $posts_page;
	}

	/**
	 * Get Bulk Delete Pages admin page.
	 *
	 * @since 6.0.0
	 *
	 * @return \BulkWP\BulkDelete\Core\Pages\DeletePagesPage
	 */
	private function get_delete_pages_admin_page() {
		$pages_page = new DeletePagesPage( $this->get_plugin_file() );

		$pages_page->add_metabox( new DeletePagesByStatusModule() );

		return $pages_page;
	}

	/**
	 * Get Bulk Delete Users admin page.
	 *
	 * @since 6.0.0
	 *
	 * @return \BulkWP\BulkDelete\Core\Users\DeleteUsersPage
	 */
	private function get_delete_users_admin_page() {
		$users_page = new DeleteUsersPage( $this->get_plugin_file() );

		$users_page->add_metabox( new DeleteUsersByUserRoleModule() );
		$users_page->add_metabox( new DeleteUsersByUserMetaModule() );

		return $users_page;
	}

	/**
	 * Get Bulk Delete Metas admin page.
	 *
	 * @since 6.0.0
	 *
	 * @return \BulkWP\BulkDelete\Core\Metas\DeleteMetasPage
	 */
	private function get_delete_metas_admin_page() {
		$metas_page = new DeleteMetasPage( $this->get_plugin_file() );

		$metas_page->add_metabox( new DeletePostMetaModule() );
		$metas_page->add_metabox( new DeleteUserMetaModule() );
		$metas_page->add_metabox( new DeleteCommentMetaModule() );

		return $metas_page;
	}

	/**
	 * Get the Cron List admin page.
	 *
	 * @since 6.0.0
	 *
	 * @return \BulkWP\BulkDelete\Core\Cron\CronListPage
	 */
	private function get_cron_list_admin_page() {
		$cron_list_page = new CronListPage( $this->get_plugin_file() );

		return $cron_list_page;
	}

	/**
	 * Get the list of secondary pages.
	 *
	 * @return BasePage[] Secondary Pages.
	 */
	private function get_secondary_pages() {
		if ( empty( $this->secondary_pages ) ) {
			$cron_list_page = $this->get_cron_list_admin_page();

			$this->secondary_pages[ $cron_list_page->get_page_slug() ] = $cron_list_page;
		}

		/**
		 * List of secondary admin pages.
		 *
		 * @since 6.0.0
		 *
		 * @param BasePage[] List of Admin pages.
		 */
		return apply_filters( 'bd_secondary_pages', $this->secondary_pages );
	}

	/**
	 * Get path to main plugin file.
	 *
	 * @return string Plugin file.
	 */
	public function get_plugin_file() {
		return $this->plugin_file;
	}

	/**
	 * Set path to main plugin file.
	 *
	 * @param string $plugin_file Path to main plugin file.
	 */
	public function set_plugin_file( $plugin_file ) {
		$this->plugin_file       = $plugin_file;
		$this->translations_path = dirname( plugin_basename( $this->get_plugin_file() ) ) . '/languages/';
	}

	/**
	 * Get path to translations.
	 *
	 * @return string Translations path.
	 */
	public function get_translations_path() {
		return $this->translations_path;
	}

	/**
	 * Get the hook suffix of a page.
	 *
	 * @param string $page_slug Page slug.
	 *
	 * @return string|null Hook suffix if found, null otherwise.
	 */
	public function get_page_hook_suffix( $page_slug ) {
		$admin_page = '';

		if ( array_key_exists( $page_slug, $this->get_primary_pages() ) ) {
			$admin_page = $this->primary_pages[ $page_slug ];
		}

		if ( array_key_exists( $page_slug, $this->get_secondary_pages() ) ) {
			$admin_page = $this->secondary_pages[ $page_slug ];
		}

		if ( $admin_page instanceof BasePage ) {
			return $admin_page->get_hook_suffix();
		}

		return null;
	}

	/**
	 * Getter for Autoloader.
	 *
	 * @return \BulkWP\BulkDelete\BulkDeleteAutoloader
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Setter for Autoloader.
	 *
	 * @param \BulkWP\BulkDelete\BulkDeleteAutoloader $loader Autoloader.
	 */
	public function set_loader( $loader ) {
		$this->loader = $loader;
	}
}
