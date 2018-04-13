<?php
/**
 * Old version of Bulk_Delete.
 *
 * This class is deprecated since 6.0.0. But included here for backward compatibility.
 * Don't depend on functionality from this class.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Main Bulk_Delete class.
 *
 * @since 5.0 Singleton
 * @since 6.0.0 Deprecated.
 */
final class Bulk_Delete {

	/**
	 * The one true Bulk_Delete instance.
	 *
	 * @var Bulk_Delete
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

	// version
	const VERSION                   = '5.6.1';

	// page slugs
	const POSTS_PAGE_SLUG           = 'bulk-delete-posts';
	const PAGES_PAGE_SLUG           = 'bulk-delete-pages';
	const CRON_PAGE_SLUG            = 'bulk-delete-cron';
	const ADDON_PAGE_SLUG           = 'bulk-delete-addon';

	// JS constants
	const JS_HANDLE                 = 'bulk-delete';
	const CSS_HANDLE                = 'bulk-delete';

	// Cron hooks
	const CRON_HOOK_CATEGORY        = 'do-bulk-delete-cat';
	const CRON_HOOK_POST_STATUS     = 'do-bulk-delete-post-status';
	const CRON_HOOK_TAG             = 'do-bulk-delete-tag';
	const CRON_HOOK_TAXONOMY        = 'do-bulk-delete-taxonomy';
	const CRON_HOOK_POST_TYPE       = 'do-bulk-delete-post-type';
	const CRON_HOOK_CUSTOM_FIELD    = 'do-bulk-delete-custom-field';
	const CRON_HOOK_TITLE           = 'do-bulk-delete-by-title';
	const CRON_HOOK_DUPLICATE_TITLE = 'do-bulk-delete-by-duplicate-title';
	const CRON_HOOK_POST_BY_ROLE    = 'do-bulk-delete-posts-by-role';

	const CRON_HOOK_PAGES_STATUS    = 'do-bulk-delete-pages-by-status';

	// meta boxes for delete posts
	const BOX_POST_STATUS           = 'bd_by_post_status';
	const BOX_CATEGORY              = 'bd_by_category';
	const BOX_TAG                   = 'bd_by_tag';
	const BOX_TAX                   = 'bd_by_tax';
	const BOX_POST_TYPE             = 'bd_by_post_type';
	const BOX_URL                   = 'bd_by_url';
	const BOX_POST_REVISION         = 'bd_by_post_revision';
	const BOX_CUSTOM_FIELD          = 'bd_by_custom_field';
	const BOX_TITLE                 = 'bd_by_title';
	const BOX_DUPLICATE_TITLE       = 'bd_by_duplicate_title';
	const BOX_POST_FROM_TRASH       = 'bd_posts_from_trash';
	const BOX_POST_BY_ROLE          = 'bd_post_by_user_role';

	// meta boxes for delete pages
	const BOX_PAGE_STATUS           = 'bd_by_page_status';
	const BOX_PAGE_FROM_TRASH       = 'bd_pages_from_trash';

	// Settings constants
	const SETTING_OPTION_GROUP      = 'bd_settings';
	const SETTING_OPTION_NAME       = 'bd_licenses';
	const SETTING_SECTION_ID        = 'bd_license_section';

	// Transient keys
	const LICENSE_CACHE_KEY_PREFIX  = 'bd-license_';

	const MAX_SELECT2_LIMIT  = 50;

	// path variables
	// Ideally these should be constants, but because of PHP's limitations, these are static variables
	public static $PLUGIN_DIR;
	public static $PLUGIN_FILE;

	// Instance variables
	public $translations;
	public $posts_page;
	public $pages_page;
	public $cron_page;
	public $addon_page;
	public $settings_page;
	public $meta_page;
	public $misc_page;
	public $display_activate_license_form = false;

	// Deprecated.
	// Will be removed in v6.0
	const CRON_HOOK_USER_ROLE = 'do-bulk-delete-users-by-role';
	public $users_page;

	/**
	 * Main Bulk_Delete Instance.
	 *
	 * Insures that only one instance of Bulk_Delete exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 5.0
	 * @static
	 * @staticvar array $instance
	 *
	 * @see BULK_DELETE()
	 *
	 * @return Bulk_Delete The one true instance of Bulk_Delete
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Bulk_Delete ) ) {
			self::$instance = new Bulk_Delete();
		}

		return self::$instance;
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
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bulk-delete' ), '5.0' );
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
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bulk-delete' ), '5.0' );
	}

	/**
	 * Set path to main plugin file.
	 *
	 * @param string $plugin_file Path to main plugin file.
	 */
	public function set_plugin_file( $plugin_file ) {
		$this->plugin_file = $plugin_file;

		self::$PLUGIN_DIR = plugin_dir_path( $plugin_file );
		self::$PLUGIN_FILE = $plugin_file;
	}

	/**
	 * Get path to main plugin file.
	 *
	 * @return string Plugin file.
	 */
	public function get_plugin_file() {
		return $this->plugin_file;
	}
}

/**
 * The main function responsible for returning the one true Bulk_Delete
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: `<?php $bulk_delete = BULK_DELETE(); ?>`
 *
 * @since 5.0
 *
 * @return Bulk_Delete The one true Bulk_Delete Instance
 */
function BULK_DELETE() {
	return Bulk_Delete::get_instance();
}

function bd_setup_backward_compatibility( $plugin_file ) {
	$bd = BULK_DELETE();
	$bd->set_plugin_file( $plugin_file );
}
add_action( 'bd_loaded', 'bd_setup_backward_compatibility' );
