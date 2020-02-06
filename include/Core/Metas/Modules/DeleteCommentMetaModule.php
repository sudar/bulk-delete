<?php

namespace BulkWP\BulkDelete\Core\Metas\Modules;

use BulkWP\BulkDelete\Core\Metas\MetasModule;
use BulkWP\BulkDelete\Core\Metas\QueryOverriders\DateQueryOverrider;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Comment Meta Module.
 *
 * @since 6.0.0
 */
class DeleteCommentMetaModule extends MetasModule {
	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function initialize() {
		$this->field_slug    = 'comment_meta';
		$this->meta_box_slug = 'bd-comment-meta';
		$this->action        = 'delete_comment_meta';
		$this->cron_hook     = 'do-bulk-delete-comment-meta';
		$this->messages      = array(
			'box_label'         => __( 'Bulk Delete Comment Meta', 'bulk-delete' ),
			'scheduled'         => __( 'Comment meta fields from the comments with the selected criteria are scheduled for deletion.', 'bulk-delete' ),
			'cron_label'        => __( 'Delete Comment Meta', 'bulk-delete' ),
			'confirm_deletion'  => __( 'Are you sure you want to delete all the comment meta fields that match the selected filters?', 'bulk-delete' ),
			'confirm_scheduled' => __( 'Are you sure you want to schedule deletion for all the comment meta fields that match the selected filters?', 'bulk-delete' ),
			'validation_error'  => __( 'Please enter meta key', 'bulk-delete' ),
			/* translators: 1 Number of comments deleted */
			'deleted_one'       => __( 'Deleted comment meta field from %d comment', 'bulk-delete' ),
			/* translators: 1 Number of comments deleted */
			'deleted_multiple'  => __( 'Deleted comment meta field from %d comments', 'bulk-delete' ),
		);

		$this->register_cron_hooks();
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function register( $hook_suffix, $page_slug ) {
		parent::register( $hook_suffix, $page_slug );

		add_filter( 'bd_delete_comment_meta_options', array( $this, 'process_filtering_options' ), 10, 2 );
	}

	/**
	 * Register additional module specific hooks that are needed in cron jobs.
	 *
	 * During a cron request, the register method is not called. So these hooks should be registered separately.
	 *
	 * @since 6.0.2
	 */
	protected function register_cron_hooks() {
		add_filter( 'bd_delete_comment_meta_query', array( $this, 'change_meta_query' ), 10, 2 );
	}

	/**
	 * Render the Delete Comment Meta box.
	 */
	public function render() {
		?>
		<!-- Comment Meta box start-->
		<fieldset class="options">
			<h4><?php _e( 'Select the post type whose comment meta fields you want to delete', 'bulk-delete' ); ?></h4>
			<table class="optiontable">
				<?php $this->render_post_type_with_status( false ); ?>
			</table>

			<h4><?php _e( 'Choose your comment meta field settings', 'bulk-delete' ); ?></h4>
			<table class="optiontable">
				<tr>
					<td>
						<input name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" class="use-value" value="false" type="radio" checked>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on comment meta key name only', 'bulk-delete' ); ?></label>
					</td>
				</tr>

				<tr>
					<td>
						<input type="radio" class="use-value" value="true" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value">

						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_use_value"><?php echo __( 'Delete based on comment meta key name and value', 'bulk-delete' ); ?></label>
					</td>
				</tr>

				<tr>
					<td>
						<label for="smbd_<?php echo esc_attr( $this->field_slug ); ?>_meta_key"><?php _e( 'Comment Meta Key ', 'bulk-delete' ); ?></label>
						<input type="text" name="smbd_<?php echo esc_attr( $this->field_slug ); ?>_meta_key" id="smbd_<?php echo esc_attr( $this->field_slug ); ?>_meta_key" placeholder="<?php _e( 'Meta Key', 'bulk-delete' ); ?>" class="validate">
					</td>
				</tr>

				<?php
				$this->render_meta_value_filter(
					[
						'class' => 'value-filters visually-hidden',
						'label' => __( 'Comment Meta Value', 'bulk-delete' ),
					]
				);
				?>
			</table>

			<table class="optiontable">
				<tr>
					<td colspan="2">
						<h4><?php _e( 'Choose your deletion options', 'bulk-delete' ); ?></h4>
					</td>
				</tr>

				<?php $this->render_restrict_settings( 'comments' ); ?>
				<?php $this->render_limit_settings(); ?>
				<?php $this->render_cron_settings(); ?>

			</table>
		</fieldset>

		<?php $this->render_submit_button(); ?>

		<!-- Comment Meta box end-->
		<?php
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function convert_user_input_to_options( $request, $options ) {
		$options['post_type'] = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug ) );

		$options['use_value'] = bd_array_get_bool( $request, 'smbd_' . $this->field_slug . '_use_value', false );
		$options['meta_key']  = esc_sql( bd_array_get( $request, 'smbd_' . $this->field_slug . '_meta_key', '' ) );

		/**
		 * Delete comment-meta delete options filter.
		 *
		 * This filter is for processing filtering options for deleting comment meta.
		 *
		 * @since 5.4
		 */
		return apply_filters( 'bd_delete_comment_meta_options', $options, $request );
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function do_delete( $options ) {
		$args = $this->get_post_type_and_status_args( $options['post_type'] );

		if ( $options['limit_to'] > 0 ) {
			$args['number'] = $options['limit_to'];
		}

		if ( $options['restrict'] ) {
			$args['date_query'] = array(
				array(
					'column'            => 'comment_date',
					$options['date_op'] => "{$options['days']} day ago",
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
		do_action( 'bd_after_meta_query' );

		foreach ( $comments as $comment ) {
			// Todo: Don't delete all meta rows if there are duplicate meta keys.
			// See https://github.com/sudar/bulk-delete/issues/515 for details.
			if ( delete_comment_meta( $comment->comment_ID, $options['meta_key'] ) ) {
				$meta_deleted ++;
			}
		}

		return $meta_deleted;
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	protected function append_to_js_array( $js_array ) {
		$js_array['validators'][ $this->action ] = 'validateTextbox';

		return $js_array;
	}

	/**
	 * Process additional delete options.
	 *
	 * This function was originally part of the Bulk Delete Comment Meta add-on.
	 *
	 * @since 0.1 of Bulk Delete Comment Meta add-on
	 *
	 * @param array $delete_options Delete options array.
	 * @param array $post           The POST array.
	 *
	 * @return array Processed delete options array.
	 */
	public function process_filtering_options( $delete_options, $post ) {
		if ( 'true' === bd_array_get( $post, 'smbd_' . $this->field_slug . '_use_value', 'false' ) ) {
			$delete_options['meta_op']                = bd_array_get( $post, 'smbd_' . $this->field_slug . '_operator', '=' );
			$delete_options['meta_type']              = bd_array_get( $post, 'smbd_' . $this->field_slug . '_type', 'CHAR' );
			$delete_options['meta_value']             = bd_array_get( $post, 'smbd_' . $this->field_slug . '_value', '' );
			$delete_options['relative_date']          = bd_array_get( $post, 'smbd_' . $this->field_slug . '_relative_date', '' );
			$delete_options['date_unit']              = bd_array_get( $post, 'smbd_' . $this->field_slug . '_date_unit', '' );
			$delete_options['date_type']              = bd_array_get( $post, 'smbd_' . $this->field_slug . '_date_type', '' );
			$delete_options['meta_value_date_format'] = bd_array_get( $post, 'smbd_' . $this->field_slug . '_date_format' );
			$delete_options['whose_meta']             = 'comment';
		}

		return $delete_options;
	}

	/**
	 * Change the meta query.
	 *
	 * This function was originally part of the Bulk Delete Comment Meta add-on.
	 *
	 * @since 0.1 of Bulk Delete Comment Meta add-on
	 *
	 * @param array $meta_query     Meta query.
	 * @param array $delete_options List of options chosen by the user.
	 *
	 * @return array Modified meta query.
	 */
	public function change_meta_query( $meta_query, $delete_options ) {
		$query_vars = array(
			'key'     => $delete_options['meta_key'],
			'compare' => $delete_options['meta_op'],
		);
		if ( array_key_exists( 'meta_type', $delete_options ) ) {
			$query_vars['type'] = $delete_options['meta_type'];
		}
		if ( in_array( $delete_options['meta_op'], array( 'EXISTS', 'NOT EXISTS' ), true ) ) {
			$meta_query = array( $query_vars );

			return $meta_query;
		}
		if ( 'DATE' === $delete_options['meta_type'] ) {
			$bd_date_handler = new DateQueryOverrider();
			$meta_query      = $bd_date_handler->get_query( $delete_options );

			return $meta_query;
		}
		switch ( $delete_options['meta_op'] ) {
			case 'IN':
				$meta_value = explode( ',', $delete_options['meta_value'] );
				break;
			case 'BETWEEN':
				$meta_value = explode( ',', $delete_options['meta_value'] );
				break;
			default:
				$meta_value = $delete_options['meta_value'];
		}

		$query_vars['value'] = $meta_value;
		$meta_query          = array( $query_vars );

		return $meta_query;
	}

	/**
	 * Hook handler.
	 *
	 * This function was originally part of the Bulk Delete Comment Meta add-on.
	 *
	 * @since 0.1 of Bulk Delete Comment Meta add-on
	 *
	 * @param array $delete_options Delete options array.
	 */
	public function do_delete_comment_meta( $delete_options ) {
		do_action( 'bd_before_scheduler', $this->messages['cron_label'] );
		$count = $this->delete( $delete_options );
		do_action( 'bd_after_scheduler', $this->messages['cron_label'], $count );
	}
}
