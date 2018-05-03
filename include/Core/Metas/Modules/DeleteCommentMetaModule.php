<?php
namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\BulkDelete\Core\Metas\MetasModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Comment Meta.
 *
 * @since 6.0.0
 */
class DeleteCommentMetaModule extends MetasModule {
	protected function initialize() {
		$this->field_slug    = 'meta_comment';
		$this->meta_box_slug = 'bd-meta-comment';
		$this->action        = 'delete_meta_comment';
		$this->cron_hook     = 'do-bulk-delete-comment-meta';
		$this->messages      = array(
			'box_label' => __( 'Bulk Delete Comment Meta', 'bulk-delete' ),
			'scheduled' => __( 'Comment meta fields from the comments with the selected criteria are scheduled for deletion.', 'bulk-delete' ),
			'cron_name' => __( 'Delete Comment Meta', 'bulk-delete' ),
		);
	}

	/**
	 * Render the Modules.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<!-- Comment Meta box start-->
		<fieldset class="options">
			<h4><?php _e( 'Select the post type whose comment meta fields you want to delete', 'bulk-delete' ); ?></h4>
			<table class="optiontable">
				<?php $this->render_post_type_as_radios(); ?>
			</table>

			<h4><?php _e( 'Choose your comment meta field settings', 'bulk-delete' ); ?></h4>
			<table class="optiontable">
				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" value="false" type="radio" checked>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on comment meta key name only', 'bulk-delete' ); ?></label>
					</td>
				</tr>

				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" value="true" type="radio" disabled>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on comment meta key name and value', 'bulk-delete' ); ?></label>
						<span class="bd-cm-pro" style="color:red; vertical-align: middle;">
							<?php _e( 'Only available in Pro Addon', 'bulk-delete' ); ?> <a href = "http://bulkwp.com/addons/bulk-delete-comment-meta/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-m-c" target="_blank">Buy now</a>
						</span>
					</td>
				</tr>

				<tr>
					<td>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_meta_key"><?php _e( 'Comment Meta Key ', 'bulk-delete' ); ?></label>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_meta_key" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_meta_key" placeholder="<?php _e( 'Meta Key', 'bulk-delete' ); ?>">
					</td>
				</tr>
			</table>

			<?php
			/**
			 * Add more fields to the delete comment meta field form.
			 * This hook can be used to add more fields to the delete comment meta field form.
			 *
			 * @since 5.4
			 */
			do_action( 'bd_delete_comment_meta_form' );
			?>
			<table class="optiontable">
				<tr>
					<td colspan="2">
						<h4><?php _e( 'Choose your deletion options', 'bulk-delete' ); ?></h4>
					</td>
				</tr>

				<?php $this->render_restrict_settings(); ?>
				<?php $this->render_limit_settings(); ?>
				<?php $this->render_cron_settings(); ?>

			</table>
		</fieldset>

		<?php $this->render_submit_button(); ?>

		<!-- Comment Meta box end-->
		<?php
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_type'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_post_type', 'post' ) );

		$options['use_value'] = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_use_value', false );
		$options['meta_key']  = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_meta_key', '' ) );

		return $options;
	}

	public function delete( $options ) {
		$args = array(
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
					'column' => 'comment_date',
					$op      => "{$days} day ago",
				),
			);
		}

		if ( $options['use_value'] ) {
			$args['meta_query'] = apply_filters( 'bd_delete_comment_meta_query', array(), $options );
		} else {
			$args['meta_key'] = $options['meta_key'];
		}

		$meta_deleted = 0;
		$comments     = get_comments( $args );

		foreach ( $comments as $comment ) {
			if ( delete_comment_meta( $comment->comment_ID, $options['meta_key'] ) ) {
				$meta_deleted ++;
			}
		}

		return $meta_deleted;
	}

	public function filter_js_array( $js_array ) {
		$js_array['dt_iterators'][]              = '_' . $this->field_slug;
		$js_array['validators'][ $this->action ] = 'noValidation';

		$js_array['pre_action_msg'][ $this->action ] = 'deleteCMWarning';
		$js_array['msg']['deleteCMWarning']          = __( 'Are you sure you want to delete all the comment meta fields that match the selected filters?', 'bulk-delete' );

		return $js_array;
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of posts deleted */
		return _n( 'Deleted comment meta field from %d comment', 'Deleted comment meta field from %d comments', $items_deleted, 'bulk-delete' );
	}
}
