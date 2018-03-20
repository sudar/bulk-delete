<?php

namespace BulkWP\BulkDelete\Core\Base;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Base class for all Bulk Delete pages that will have metaboxes.
 *
 * @since 6.0.0
 */
abstract class MetaboxPage extends BasePage {

	/**
	 * Item Type. Possible values 'posts', 'pages', 'users' etc.
	 *
	 * @var string
	 */
	protected $item_type;

	/**
	 * Metaboxes registered to this page.
	 *
	 * @var \BulkWP\BulkDelete\Core\Base\BaseMetabox[]
	 */
	protected $metaboxes = array();

	/**
	 * Register the metaboxes after the page is registered.
	 */
	public function register() {
		parent::register();

		if ( $this->has_metaboxes() ) {
			$this->register_metaboxes();
		}
	}

	/**
	 * Add a metabox to page.
	 *
	 * @param \BulkWP\BulkDelete\Core\Base\BaseMetabox $metabox Metabox to add.
	 */
	public function add_metabox( $metabox ) {
		if ( in_array( $metabox, $this->metaboxes, true ) ) {
			return;
		}

		$this->metaboxes[] = $metabox;
	}

	protected function register_hooks() {
		parent::register_hooks();

		add_action( 'admin_print_scripts-' . $this->hook_suffix, array( $this, 'enqueue_assets' ) );
		add_action( "load-{$this->hook_suffix}", array( $this, 'on_load_page' ) );
	}

	/**
	 * Enqueue Scripts and Styles.
	 */
	public function enqueue_assets() {
		global $wp_scripts;

		/**
		 * Runs just before enqueuing scripts and styles in all Bulk WP admin pages.
		 *
		 * This action is primarily for registering or deregistering additional scripts or styles.
		 *
		 * @since 5.5.1
		 */
		do_action( 'bd_before_admin_enqueue_scripts' );

		wp_enqueue_script( 'jquery-ui-timepicker', $this->get_plugin_dir_url() . 'assets/js/jquery-ui-timepicker-addon.min.js', array(
			'jquery-ui-slider',
			'jquery-ui-datepicker'
		), '1.5.4', true );
		wp_enqueue_style( 'jquery-ui-timepicker', $this->get_plugin_dir_url() . 'assets/css/jquery-ui-timepicker-addon.min.css', array(), '1.5.4' );

		wp_enqueue_script( 'select2', $this->get_plugin_dir_url() . 'assets/js/select2.min.js', array( 'jquery' ), '4.0.0', true );
		wp_enqueue_style( 'select2', $this->get_plugin_dir_url() . 'assets/css/select2.min.css', array(), '4.0.0' );

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'bulk-delete', $this->get_plugin_dir_url() . 'assets/js/bulk-delete' . $postfix . '.js', array(
			'jquery-ui-timepicker',
			'jquery-ui-tooltip',
			'postbox'
		), \Bulk_Delete::VERSION, true );
		wp_enqueue_style( 'bulk-delete', $this->get_plugin_dir_url() . 'assets/css/bulk-delete' . $postfix . '.css', array( 'select2' ), \Bulk_Delete::VERSION );

		$ui  = $wp_scripts->query( 'jquery-ui-core' );
		$url = "//ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
		wp_enqueue_style( 'jquery-ui-smoothness', $url, false, $ui->ver );

		/**
		 * Filter JavaScript array.
		 *
		 * This filter can be used to extend the array that is passed to JavaScript
		 *
		 * @since 5.4
		 */
		$translation_array = apply_filters( 'bd_javascript_array', array(
			'msg'            => array(),
			'validators'     => array(),
			'dt_iterators'   => array(),
			'pre_action_msg' => array(),
			'error_msg'      => array(),
			'pro_iterators'  => array(),
		) );
		wp_localize_script( 'bulk-delete', 'BulkWP', $translation_array ); // TODO: Change JavaScript variable to BulkWP.BulkDelete.

		/**
		 * Runs just after enqueuing scripts and styles in all Bulk WP admin pages.
		 *
		 * This action is primarily for registering additional scripts or styles.
		 *
		 * @since 5.5.1
		 */
		do_action( 'bd_after_admin_enqueue_scripts' );
	}

	/**
	 * Trigger the add_meta_boxes hooks to allow meta boxes to be added when the page is loaded.
	 */
	public function on_load_page() {
		do_action( 'add_meta_boxes_' . $this->hook_suffix, null );
	}

	/**
	 * Add additional nonce fields that are related to metaboxes.
	 */
	protected function render_nonce_fields() {
		parent::render_nonce_fields();

		// Used to save closed meta boxes and their order.
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	}

	/**
	 * Render meta boxes in body.
	 */
	protected function render_body() {
		do_meta_boxes( '', 'advanced', null );
	}

	/**
	 * Render footer.
	 */
	protected function render_footer() {
		parent::render_footer();

		/**
		 * Runs just before displaying the footer text in the admin page.
		 *
		 * This action is primarily for adding extra content in the footer of admin page.
		 *
		 * @since 5.5.4
		 */
		do_action( "bd_admin_footer_for_{$this->item_type}" );
	}

	/**
	 * Does this page has metaboxes?
	 *
	 * @return bool True if page has metaboxes, False otherwise.
	 */
	protected function has_metaboxes() {
		return ! empty( $this->metaboxes );
	}

	/**
	 * Load all the registered metaboxes.
	 */
	protected function register_metaboxes() {
		foreach ( $this->metaboxes as $metabox ) {
			$metabox->register( $this->hook_suffix, $this->page_slug );
			$this->actions[] = $metabox->get_action();
		}
	}
}
