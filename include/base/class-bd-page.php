<?php
/**
 * Base class for all Pages.
 *
 * @since   5.5
 * @author  Sudar
 * @package BulkDelete\Base\Page
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Base class for Pages.
 *
 * @abstract
 * @since 5.5
 */
abstract class BD_Page {
	/**
	 * @var string Page Slug.
	 */
	protected $page_slug;

	/**
	 * @var string Item Type. Possible values 'posts', 'pages', 'users' etc.
	 */
	protected $item_type;

	/**
	 * @var string Minimum capability needed for viewing this page.
	 */
	protected $capability = 'manage_options';

	/**
	 * @var string The screen variable for this page.
	 */
	protected $screen;

	/**
	 * @var array Labels used in this page.
	 */
	protected $label = array();

	/**
	 * @var array Messages shown to the user.
	 */
	protected $messages = array();

	/**
	 * Initialize and setup variables.
	 *
	 * @since 5.5
	 * @abstract
	 * @return void
	 */
	abstract protected function initialize();

	/**
	 * Use `factory()` method to create instance of this class.
	 * Don't create instances directly
	 *
	 * @since 5.5
	 *
	 * @see factory()
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Setup the module.
	 *
	 * @since 5.5
	 */
	protected function setup() {
		$this->initialize();
		$this->setup_hooks();
	}

	/**
	 * Setup hooks.
	 *
	 * @since 5.5
	 */
	protected function setup_hooks() {
		add_filter( 'bd_action_nonce_check', array( $this, 'nonce_check' ), 10, 2 );
		add_filter( 'bd_admin_help_tabs', array( $this, 'render_help_tab' ), 10, 2 );

		add_action( 'bd_after_primary_menus', array( $this, 'add_menu' ) );
		add_action( "bd_admin_footer_for_{$this->item_type}", array( $this, 'modify_admin_footer' ) );
	}

	/**
	 * Check for nonce before executing the action.
	 *
	 * @since 5.5
	 * @param bool   $result The current result.
	 * @param string $action Action name.
	 */
	public function nonce_check( $result, $action ) {
		$action_prefix = "delete_{$this->item_type}_";

		if ( $action_prefix === substr( $action, 0, strlen( $action_prefix ) )
			&& check_admin_referer( "bd-{$this->page_slug}", "bd-{$this->page_slug}-nonce" ) ) {
			return true;
		} else {
			return $result;
		}
	}

	/**
	 * Add menu.
	 *
	 * @since 5.5
	 */
	public function add_menu() {
		$bd = BULK_DELETE();

		$this->screen = add_submenu_page(
			Bulk_Delete::POSTS_PAGE_SLUG,
			$this->label['page_title'],
			$this->label['menu_title'],
			$this->capability,
			$this->page_slug,
			array( $this, 'render_page' )
		);

		add_action( "admin_print_scripts-{$this->screen}", array( $bd, 'add_script' ) );

		add_action( "load-{$this->screen}", array( $this, 'add_settings_panel' ) );
		add_action( "add_meta_boxes_{$this->screen}", array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Add settings Panel.
	 *
	 * @since 5.5
	 */
	public function add_settings_panel() {
		/**
		 * Add contextual help for admin screens.
		 *
		 * @since 5.1
		 */
		do_action( 'bd_add_contextual_help', $this->screen );

		// Trigger the add_meta_boxes hooks to allow meta boxes to be added
		do_action( 'add_meta_boxes_' . $this->screen, null );

		// Enqueue WordPress' script for handling the meta boxes
		wp_enqueue_script( 'postbox' );
	}

	/**
	 * Modify help tabs for the current page.
	 *
	 * @since 5.5
	 * @param array  $help_tabs Current list of help tabs.
	 * @param string $screen Current screen name.
	 * @return array Modified list of help tabs.
	 */
	public function render_help_tab( $help_tabs, $screen ) {
		if ( $this->screen == $screen ) {
			$help_tabs = $this->add_help_tab( $help_tabs );
		}

		return $help_tabs;
	}

	/**
	 * Add help tabs.
	 * Help tabs can be added by overriding this function in the child class.
	 *
	 * @since 5.5
	 * @param array $help_tabs Current list of help tabs.
	 * @return array List of help tabs.
	 */
	protected function add_help_tab( $help_tabs ) {
		return $help_tabs;
	}

	/**
	 * Register meta boxes.
	 *
	 * @since 5.5
	 */
	public function add_meta_boxes() {
		/**
		 * Add meta box in delete users page.
		 * This hook can be used for adding additional meta boxes in delete users page
		 *
		 * @since 5.3
		 */
		do_action( "bd_add_meta_box_for_{$this->item_type}", $this->screen, $this->page_slug  );
	}

	/**
	 * Render the page.
	 *
	 * @since 5.5
	 */
	public function render_page() {
?>
<div class="wrap">
    <h2><?php echo $this->label['page_title'];?></h2>
    <?php settings_errors(); ?>

    <form method = "post">
<?php
		wp_nonce_field( "bd-{$this->page_slug}", "bd-{$this->page_slug}-nonce" );

		// Used to save closed meta boxes and their order
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
?>
    <div id = "poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div class="notice notice-warning">
                <p><strong><?php echo $this->messages['warning_message']; ?></strong></p>
            </div>

            <div id="postbox-container-1" class="postbox-container">
                <iframe frameBorder="0" height = "1500" src = "http://sudarmuthu.com/projects/wordpress/bulk-delete/sidebar.php?color=<?php echo get_user_option( 'admin_color' ); ?>&version=<?php echo Bulk_Delete::VERSION; ?>"></iframe>
            </div>

            <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes( '', 'advanced', null ); ?>
            </div> <!-- #postbox-container-2 -->

        </div> <!-- #post-body -->
    </div><!-- #poststuff -->
    </form>
</div><!-- .wrap -->
<?php
		/**
		 * Runs just before displaying the footer text in the admin page.
		 *
		 * This action is primarily for adding extra content in the footer of admin page.
		 *
		 * @since 5.0
		 */
		do_action( "bd_admin_footer_for_{$this->item_type}" );
	}

	/**
	 * Modify admin footer in Bulk Delete plugin pages.
	 *
	 * @since     5.5
	 */
	public function modify_admin_footer() {
		add_filter( 'admin_footer_text', 'bd_add_rating_link' );
	}

	/**
	 * Getter for screen.
	 *
	 * @return string Current value of screen
	 */
	public function get_screen() {
		return $this->screen;
	}

	/**
	 * Getter for page_slug.
	 *
	 * @return string Current value of page_slug
	 */
	public function get_page_slug() {
		return $this->page_slug;
	}
}
?>
