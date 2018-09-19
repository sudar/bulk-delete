<?php
namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\BulkDelete\Core\Metas\MetasModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Post Meta.
 *
 * @since 6.0.0
 */
class DeletePostMetaModule extends MetasModule {
	protected function initialize() {
		$this->field_slug    = 'pm'; // Ideally it should be `meta_post`. But we are keeping it as pm for backward compatibility.
		$this->meta_box_slug = 'bd-post-meta';
		$this->action        = 'delete_post_meta';
		$this->cron_hook     = 'do-bulk-delete-post-meta';
		$this->scheduler_url = 'http://bulkwp.com/addons/bulk-delete-post-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-m-p';
		$this->messages      = array(
			'box_label'  => __( 'Bulk Delete Post Meta', 'bulk-delete' ),
			'scheduled'  => __( 'Post meta fields from the posts with the selected criteria are scheduled for deletion.', 'bulk-delete' ),
			'cron_label' => __( 'Delete Post Meta', 'bulk-delete' ),
		);
	}

	/**
	 * Render the Modules.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<!-- Post Meta box start-->
		<fieldset class="options">
			<h4><?php _e( 'Select the post type whose post meta fields you want to delete', 'bulk-delete' ); ?></h4>

			<table class="optiontable">
				<?php $this->render_post_type_dropdown(); ?>
			</table>

			<h4><?php _e( 'Choose your post meta field settings', 'bulk-delete' ); ?></h4>
			<table class="optiontable">
				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" value="use_key" type="radio" checked>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on post meta key name only', 'bulk-delete' ); ?></label>
					</td>
				</tr>

				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" id="smdb_<?php echo esc_attr( $this->field_slug ); ?>_use_key_compare" value="use_key_compare" type="radio" disabled>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on post meta key name prefix or postfix', 'bulk-delete' ); ?></label>
						<span class="bd-pm-pro" style="color:red; vertical-align: middle;">
							<?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/bulk-delete-post-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-m-p" target="_blank">Buy now</a>
						</span>
					</td>
				</tr>

				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" value="use_value" type="radio" disabled>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on post meta key name and value', 'bulk-delete' ); ?></label>
						<span class="bd-pm-pro" style="color:red; vertical-align: middle;">
							<?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/bulk-delete-post-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-m-p" target="_blank">Buy now</a>
						</span>
					</td>
				</tr>

				<tr>
					<td>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key"><?php _e( 'Post Meta Key ', 'bulk-delete' ); ?></label>
						<select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key_prefix_postfix" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key_prefix_postfix" style="display: none;">
							<option value="starts_with">starts with</option>
							<option value="contains">contains</option>
							<option value="ends_with">ends with</option>
						</select>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_key" placeholder="<?php _e( 'Meta Key', 'bulk-delete' ); ?>">
					</td>
				</tr>
			</table>

		<?php
			/**
			 * Add more fields to the delete post meta field form.
			 * This hook can be used to add more fields to the delete post meta field form.
			 *
			 * @since 5.4
			 */
			do_action( 'bd_delete_post_meta_form' );
		?>

			<table class="optiontable">
				<tr>
					<td colspan="2">
						<h4><?php _e( 'Choose your deletion options', 'bulk-delete' ); ?></h4>
					</td>
				</tr>

				<?php $this->render_restrict_settings(); ?>
				<?php $this->render_limit_settings(); ?>
				<?php $scheduler_plugin = 'bulk-delete-post-meta/bulk-delete-post-meta.php';
				?>
				<?php $this->render_cron_settings( is_plugin_active( $scheduler_plugin ) ); ?>

			</table>
		</fieldset>

		<?php $this->render_submit_button(); ?>

		<!-- Post Meta box end-->
		<?php
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_type'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_post_type', 'post' ) );

		$options['use_value'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_use_value', 'use_key' );
		$options['meta_key']  = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_key', '' ) );

		/**
		 * Delete post-meta delete options filter.
		 *
		 * This filter is for processing filtering options for deleting post meta.
		 *
		 * @since 5.4
		 */
		return apply_filters( 'bd_delete_post_meta_options', $options, $request );
	}

	protected function do_delete( $options ) {
		$count = 0;
		$args  = array(
			'post_type' => $options['post_type'],
		);

		if ( $options['limit_to'] > 0 ) {
			$args['number'] = $options['limit_to'];
		} else {
			$args['nopaging'] = 'true';
		}

		$op   = $options['date_op'];
		$days = $options['days'];

		if ( $options['restrict'] ) {
			$args['date_query'] = array(
				array(
					'column' => 'post_date',
					$op      => "{$days} day ago",
				),
			);
		}

		if ( 'use_key' === $options['use_value'] ) {
			$options['meta_key'] = $options['meta_key'];
		} else {
			$options['meta_query'] = apply_filters( 'bd_delete_post_meta_query', array(), $options );
		}

		$post_ids = bd_query( $args );
		foreach ( $post_ids as $post_id ) {
			if ( isset( $options['meta_key'] ) && is_array( $options['meta_key'] ) ) {
				$is_post_id_counted = false;
				foreach ( $options['meta_key'] as $meta_key ) {
					if ( delete_post_meta( $post_id, $meta_key ) ) {
						if ( $is_post_id_counted ) {
							continue;
						}
						$count++;
						$is_post_id_counted = true;
					}
				}
			} else {
				if ( delete_post_meta( $post_id, $options['meta_key'] ) ) {
					$count++;
				}
			}
		}

		return $count;
	}

	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][]              = '_' . $this->field_slug;
		$js_array['validators'][ $this->action ] = 'noValidation';

		$js_array['pre_action_msg'][ $this->action ] = 'deletePMWarning';
		$js_array['msg']['deletePMWarning']          = __( 'Are you sure you want to delete all the post meta fields that match the selected filters?', 'bulk-delete' );

		return $js_array;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted post meta field from %d post', 'Deleted post meta field from %d posts', $items_deleted, 'bulk-delete' );
	}
}
