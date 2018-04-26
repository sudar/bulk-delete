<?php

namespace BulkWP\BulkDelete\Core\Base;

use BulkWP\BulkDelete\Core\Base\Mixin\Renderer;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Encapsulates the Bulk Delete Meta box Module Logic.
 *
 * All Bulk Delete Meta box Modules should extend this class.
 * This class extends Renderer Mixin class since Bulk Delete still supports PHP 5.3.
 * Once PHP 5.3 support is dropped, Renderer will be implemented as a Trait and this class will `use` it.
 *
 * @since 6.0.0
 */
abstract class BaseModule extends Renderer {
	/**
	 * Item Type. Possible values 'posts', 'pages', 'users' etc.
	 *
	 * @var string
	 */
	protected $item_type;

	/**
	 * The hook suffix of the screen where this meta box would be shown.
	 *
	 * @var string
	 */
	protected $page_hook_suffix;

	/**
	 * Slug of the page where this module will be shown.
	 *
	 * @var string
	 */
	protected $page_slug;

	/**
	 * Slug for the form fields.
	 *
	 * @var string
	 */
	protected $field_slug;

	/**
	 * Slug of the meta box.
	 *
	 * @var string
	 */
	protected $meta_box_slug;

	/**
	 * Action in which the delete operation should be performed.
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * Hook for scheduler.
	 *
	 * @var string
	 */
	protected $cron_hook;

	/**
	 * Url of the scheduler addon.
	 *
	 * @var string
	 */
	protected $scheduler_url;

	/**
	 * Messages shown to the user.
	 *
	 * @var array
	 */
	protected $messages = array(
		'box_label' => '',
	);

	/**
	 * Initialize and setup variables.
	 *
	 * @return void
	 */
	abstract protected function initialize();

	/**
	 * Render the Modules.
	 *
	 * @return void
	 */
	abstract public function render();

	/**
	 * Process user input and create metabox options.
	 *
	 * @param array $request Request array.
	 * @param array $options User options.
	 *
	 * @return array User options.
	 */
	abstract protected function convert_user_input_to_options( $request, $options );

	/**
	 * Perform the deletion.
	 *
	 * @param array $options Array of Delete options.
	 *
	 * @return int Number of items that were deleted.
	 */
	abstract public function delete( $options );

	/**
	 * Get Success Message.
	 *
	 * @param int $items_deleted Number of items that were deleted.
	 *
	 * @return string Success message.
	 */
	abstract protected function get_success_message( $items_deleted );

	abstract protected function get_cron_action_name();

	/**
	 * Create new instances of Modules.
	 */
	public function __construct() {
		$this->initialize();
	}

	/**
	 * Register.
	 *
	 * @param string $hook_suffix Page Hook Suffix.
	 * @param string $page_slug   Page slug.
	 */
	public function register( $hook_suffix, $page_slug ) {
		$this->page_hook_suffix = $hook_suffix;
		$this->page_slug        = $page_slug;

		add_action( "add_meta_boxes_{$this->page_hook_suffix}", array( $this, 'setup_metabox' ) );

		add_action( 'bd_' . $this->action, array( $this, 'process' ) );
		add_filter( 'bd_javascript_array', array( $this, 'filter_js_array' ) );
	}

	/**
	 * Setup the meta box.
	 */
	public function setup_metabox() {
		add_meta_box(
			$this->meta_box_slug,
			$this->messages['box_label'],
			array( $this, 'render_box' ),
			$this->page_hook_suffix,
			'advanced'
		);
	}

	/**
	 * Render the meta box.
	 */
	public function render_box() {
		if ( $this->is_hidden() ) {
			printf(
				/* translators: 1 module url */
				__( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ),
				'admin.php?page=' . $this->page_slug
			);

			return;
		}

		$this->render();
	}

	/**
	 * Is the current meta box hidden by user.
	 *
	 * @return bool True, if hidden. False, otherwise.
	 */
	protected function is_hidden() {
		$current_user    = wp_get_current_user();
		$user_meta_field = $this->get_hidden_box_user_meta_field();
		$hidden_boxes    = get_user_meta( $current_user->ID, $user_meta_field, true );

		return is_array( $hidden_boxes ) && in_array( $this->meta_box_slug, $hidden_boxes, true );
	}

	/**
	 * Get the user meta field that stores the status of the hidden meta boxes.
	 *
	 * @return string Name of the User Meta field.
	 */
	protected function get_hidden_box_user_meta_field() {
		if ( 'posts' === $this->item_type ) {
			return 'metaboxhidden_toplevel_page_bulk-delete-posts';
		} else {
			return 'metaboxhidden_bulk-wp_page_' . $this->page_slug;
		}
	}

	/**
	 * Filter the js array.
	 *
	 * This function will be overridden by the child classes.
	 *
	 * @param array $js_array JavaScript Array.
	 *
	 * @return array Modified JavaScript Array
	 */
	public function filter_js_array( $js_array ) {
		return $js_array;
	}

	/**
	 * Render filtering table header.
	 */
	protected function render_filtering_table_header() {
		bd_render_filtering_table_header();
	}

	/**
	 * Render restrict settings.
	 */
	protected function render_restrict_settings() {
		bd_render_restrict_settings( $this->field_slug, $this->item_type );
	}

	/**
	 * Render delete settings.
	 */
	protected function render_delete_settings() {
		bd_render_delete_settings( $this->field_slug );
	}

	/**
	 * Render limit settings.
	 */
	protected function render_limit_settings() {
		bd_render_limit_settings( $this->field_slug, $this->item_type );
	}

	/**
	 * Render cron settings.
	 */
	protected function render_cron_settings() {
		bd_render_cron_settings( $this->field_slug, $this->scheduler_url );
	}

	/**
	 * Render submit button.
	 */
	protected function render_submit_button() {
		bd_render_submit_button( $this->action );
	}

	/**
	 * Helper function for processing deletion.
	 * Setups up cron and invokes the actual delete method.
	 *
	 * @param array $request Request array.
	 */
	public function process( $request ) {
		$options              = $this->parse_common_filters( $request );
		$options              = $this->convert_user_input_to_options( $request, $options );
		$options['cron_name'] = $this->get_cron_action_name();
		$cron_options         = $this->parse_cron_filters( $request );

		if ( $this->is_scheduled( $cron_options ) ) {
			$msg = $this->schedule_deletion( $cron_options, $options );
		} else {
			$items_deleted = $this->delete( $options );
			$msg           = sprintf( $this->get_success_message( $items_deleted ), $items_deleted );
		}

		add_settings_error(
			$this->page_slug,
			$this->action,
			$msg,
			'updated'
		);
	}

	/**
	 * Getter for cron_hook.
	 *
	 * @return string Cron Hook name.
	 */
	public function get_cron_hook() {
		return $this->cron_hook;
	}

	/**
	 * Getter for field slug.
	 *
	 * @return string Field Slug.
	 */
	public function get_field_slug() {
		return $this->field_slug;
	}

	/**
	 * Getter for action.
	 *
	 * @return string Modules action.
	 */
	public function get_action() {
		return $this->action;
	}

	/**
	 * Is the current deletion request a scheduled request?
	 *
	 * @param array $cron_options Request object.
	 *
	 * @return bool True if it is a scheduled request, False otherwise.
	 */
	protected function is_scheduled( $cron_options ) {
		return $cron_options['is_scheduled'];
	}

	/**
	 * Schedule Deletion of items.
	 *
	 * @param array $cron_options Cron options.
	 * @param array $options      Deletion option.
	 *
	 * @return string Message.
	 */
	protected function schedule_deletion( $cron_options, $options ) {
		if ( '-1' === $cron_options['frequency'] ) {
			wp_schedule_single_event( $cron_options['start_time'], $this->cron_hook, array( $options ) );
		} else {
			wp_schedule_event( $cron_options['start_time'], $cron_options['frequency'], $this->cron_hook, array( $options ) );
		}

		return $this->messages['scheduled'] . ' ' . $this->get_task_list_link();
	}

	/**
	 * Get the link to the page that lists all the scheduled tasks.
	 *
	 * @return string Link to scheduled tasks page.
	 */
	protected function get_task_list_link() {
		return sprintf(
			__( 'See the full list of <a href = "%s">scheduled tasks</a>', 'bulk-delete' ),
			get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=' . \Bulk_Delete::CRON_PAGE_SLUG
		);
	}

	/**
	 * Handle common filters.
	 *
	 * @param array $request Request array.
	 *
	 * @return array User options.
	 */
	protected function parse_common_filters( $request ) {
		$options = array();

		$options['restrict']     = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_restrict', false );
		$options['limit_to']     = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_limit_to', 0 ) );
		$options['force_delete'] = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_force_delete', false );

		$options['date_op'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_op' );
		$options['days']    = absint( bd_array_get( $request, 'smbd_' . $this->field_slug . '_days' ) );

		return $options;
	}

	/**
	 * Parse request and create cron options.
	 *
	 * @param array $request Request array.
	 *
	 * @return array Parsed cron option.
	 */
	protected function parse_cron_filters( $request ) {
		$cron_options = array(
			'is_scheduled' => false,
		);

		$scheduled = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_cron', false );

		if ( $scheduled ) {
			$cron_options['is_scheduled'] = true;
			$cron_options['frequency']    = sanitize_text_field( $request[ 'smbd_' . $this->field_slug . '_cron_freq' ] );
			$cron_options['start_time']   = bd_get_gmt_offseted_time( sanitize_text_field( $request[ 'smbd_' . $this->field_slug . '_cron_start' ] ) );
		}

		return $cron_options;
	}

	/**
	 * Get the threshold after which enhanced select should be used.
	 *
	 * @return int Threshold.
	 */
	protected function get_enhanced_select_threshold() {
		/**
		 * Filter the enhanced select threshold.
		 *
		 * @since 6.0.0
		 *
		 * @param int Threshold.
		 */
		return apply_filters( 'bd_enhanced_select_threshold', 1000 );
	}

	/**
	 * Get the class name for select2 dropdown based on the number of items present.
	 *
	 * @param int    $count      The number of items present.
	 * @param string $class_name Primary class name.
	 *
	 * @return string Class name.
	 */
	protected function enable_ajax_if_needed_to_dropdown_class_name( $count, $class_name ) {
		if ( $count >= $this->get_enhanced_select_threshold() ) {
			$class_name .= '-ajax';
		}

		return $class_name;
	}
}
