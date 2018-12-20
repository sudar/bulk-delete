<?php

namespace BulkWP\BulkDelete\Core\Addon;

use BulkWP\BulkDelete\Core\Base\BasePage;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * A Feature Add-on.
 *
 * All Feature Add-ons will extend this class.
 * A Feature Add-on contains a bunch of modules and may also have Schedulers.
 *
 * @since 6.0.0
 */
abstract class FeatureAddon extends BaseAddon {
	/**
	 * List of pages that are registered by this add-on.
	 *
	 * @var \BulkWP\BulkDelete\Core\Base\BaseAddonPage[]
	 */
	protected $pages = array();

	/**
	 * List of modules that are registered by this add-on.
	 *
	 * This is an associate array, where the key is the item type and value is the array of modules.
	 * Eg: $modules['item_type'] = array( $module1, $module2 );
	 *
	 * @var array
	 */
	protected $modules = array();

	/**
	 * List of schedulers that are registered by this add-on.
	 *
	 * @var \BulkWP\BulkDelete\Core\Base\BaseScheduler[]
	 */
	protected $schedulers = array();

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function register() {
		foreach ( $this->pages as $page ) {
			$page->for_addon( $this->addon_info );
		}

		if ( ! empty( $this->pages ) ) {
			add_filter( 'bd_primary_pages', array( $this, 'register_pages' ) );
		}

		foreach ( array_keys( $this->modules ) as $page_slug ) {
			add_action( "bd_after_modules_{$page_slug}", array( $this, 'register_modules_in_page' ) );
		}

		foreach ( $this->schedulers as $scheduler ) {
			$scheduler->register();
		}
	}

	/**
	 * Register pages.
	 *
	 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage[] $primary_pages List of registered Primary pages.
	 *
	 * @return \BulkWP\BulkDelete\Core\Base\BaseDeletePage[] Modified list of primary pages.
	 */
	public function register_pages( $primary_pages ) {
		foreach ( $this->pages as $page ) {
			/**
			 * After the modules are registered in the delete posts page.
			 *
			 * @since 6.0.0
			 *
			 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage $page The page in which the modules are registered.
			 */
			do_action( "bd_after_modules_{$page->get_page_slug()}", $page );

			/**
			 * After the modules are registered in a delete page.
			 *
			 * @since 6.0.0
			 *
			 * @param BasePage $posts_page The page in which the modules are registered.
			 */
			do_action( 'bd_after_modules', $page );

			$primary_pages[ $page->get_page_slug() ] = $page;
		}

		return $primary_pages;
	}

	/**
	 * Register modules for a page.
	 *
	 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage $page Page.
	 */
	public function register_modules_in_page( $page ) {
		$modules = $this->modules[ $page->get_page_slug() ];

		foreach ( $modules as $module ) {
			$page->add_module( $module );
		}
	}
}
