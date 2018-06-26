<?php

namespace BulkWP\BulkDelete\Core\Addon;

use BulkWP\BulkDelete\Core\Base\BaseModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * A Module that upsells an add-on.
 *
 * Upseller Module is displayed for add-ons with a description and a link to buy them.
 * If an add-on is installed, then the Upseller Module is automatically deactivated.
 *
 * Methods that are not needed are left empty.
 *
 * @since 6.0.0
 */
class UpsellModule extends BaseModule {
	/**
	 * Details about the add-on.
	 *
	 * @var \BulkWP\BulkDelete\Core\Addon\AddonInfo
	 */
	protected $addon_info;

	/**
	 * Create the UpsellModule using add-on info.
	 *
	 * @param \BulkWP\BulkDelete\Core\Addon\AddonInfo $addon_info Addon Info.
	 */
	public function __construct( $addon_info ) {
		$this->addon_info = $addon_info;

		$this->meta_box_slug = $this->addon_info->get_slug();
		$this->messages      = array(
			'box_label' => $addon_info->get_upsell_title(),
		);
	}

	public function render() {
		?>

		<p>
			<?php echo $this->addon_info->get_upsell_message(); ?>
			<a href="<?php echo esc_url( $this->addon_info->get_buy_url() ); ?>"><?php _e( 'Buy Now', 'bulk-delete' ); ?></a>
		</p>

		<?php
	}

	protected function initialize() {
		// Empty by design.
	}

	protected function parse_common_filters( $request ) {
		// Empty by design.
	}

	protected function convert_user_input_to_options( $request, $options ) {
		// Empty by design.
	}

	protected function get_success_message( $items_deleted ) {
		// Empty by design.
	}

	protected function do_delete( $options ) {
		// Empty by design.
	}
}
