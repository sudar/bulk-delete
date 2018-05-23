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
<?php
		$types = $this->get_post_types();
?>
        <h4><?php _e( 'Select the post type whose post meta fields you want to delete', 'bulk-delete' ); ?></h4>
        <table class="optiontable">
<?php
		foreach ( $types as $type => $post_data ) {
?>
            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_post_type" value = "<?php echo $type; ?>" type = "radio" class = "smbd_<?php echo esc_attr( $this->field_slug ); ?>_post_type" <?php checked( $type, 'post' ); ?>>
                    <label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_post_type"><?php echo $type; ?> </label>
                </td>
            </tr>
<?php
		}
?>
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
                <td>
                    <h4><?php _e( 'Choose your deletion options', 'bulk-delete' ); ?></h4>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_restrict" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_restrict" value = "true" type = "checkbox" >
                    <?php _e( 'Only restrict to posts which are ', 'bulk-delete' );?>
                    <select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_op" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_op" disabled>
                        <option value ="before"><?php _e( 'older than', 'bulk-delete' );?></option>
                        <option value ="after"><?php _e( 'posted within last', 'bulk-delete' );?></option>
                    </select>
                    <input type ="textbox" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_days" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_days" disabled value ="0" maxlength="4" size="4"><?php _e( 'days', 'bulk-delete' );?>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit" value = "true" type = "checkbox">
                    <?php _e( 'Only delete post meta field from first ', 'bulk-delete' );?>
                    <input type ="textbox" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit_to" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_limit_to" disabled value ="0" maxlength="4" size="4"><?php _e( 'posts.', 'bulk-delete' );?>
                    <?php _e( 'Use this option if there are more than 1000 posts and the script times out.', 'bulk-delete' ) ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron" value = "false" type = "radio" checked="checked"> <?php _e( 'Delete now', 'bulk-delete' ); ?>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron" value = "true" type = "radio" id = "smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron" disabled > <?php _e( 'Schedule', 'bulk-delete' ); ?>
                    <input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron_start" id = "smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron_start" value = "now" type = "text" disabled><?php _e( 'repeat ', 'bulk-delete' );?>
                    <select name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_cron_freq" id = "smbd_pm_cron_freq" disabled>
                        <option value = "-1"><?php _e( "Don't repeat", 'bulk-delete' ); ?></option>
<?php
		$schedules = wp_get_schedules();
		foreach ( $schedules as $key => $value ) {
?>
                        <option value = "<?php echo $key; ?>"><?php echo $value['display']; ?></option>
<?php
		}
?>
                    </select>
                    <span class="bd-pm-pro" style="color:red">
                        <?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/bulk-delete-post-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-m-p">Buy now</a>
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <?php _e( 'Enter time in Y-m-d H:i:s format or enter now to use current time', 'bulk-delete' );?>
                </td>
            </tr>

        </table>
        </fieldset>

        <p>
            <button type="submit" name="bd_action" value="delete_meta_post" class="button-primary"><?php _e( 'Bulk Delete ', 'bulk-delete' ) ?>&raquo;</button>
        </p>
        <!-- Post Meta box end-->
		<?php
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_type'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_post_type', 'post' ) );

		$options['use_value'] = bd_array_get( $request, 'smbd_' . $this->field_slug . '_use_value', 'use_key' );
		$options['meta_key']  = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_key', '' ) );

		return $options;
	}

	protected function do_delete( $options ) {
		$count = 0;
		$args  = array(
			'post_type' => $options['post_type'],
		);

		if ( $options['limit_to'] > 0 ) {
			$args['number'] = $options['limit_to'];
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
		$js_array['dt_iterators'][]                 = '_pm';
		$js_array['validators']['delete_meta_post'] = 'noValidation';

		$js_array['pre_action_msg']['delete_meta_post'] = 'deletePMWarning';
		$js_array['msg']['deletePMWarning']             = __( 'Are you sure you want to delete all the post meta fields that match the selected filters?', 'bulk-delete' );

		return $js_array;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted post meta field from %d post', 'Deleted post meta field from %d posts', $items_deleted, 'bulk-delete' );
	}
}
