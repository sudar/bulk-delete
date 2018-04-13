<?php

namespace BulkWP\BulkDelete\Core\Posts\Metabox;

use BulkWP\BulkDelete\Core\Posts\PostsMetabox;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Delete Posts by Cudtom Taxonomy Metabox.
 *
 * @since 6.0.0
 */
class DeletePostsByCustomTaxonomMetabox extends PostsMetabox {
	protected function initialize() {
		$this->item_type     = 'posts';
		$this->field_slug    = 'taxs';
		$this->meta_box_slug = 'bd_posts_by_taxonomy';
		$this->action        = 'bd_delete_posts_by_taxonomy';
		$this->cron_hook     = 'do-bulk-delete-taxonomy';
		$this->scheduler_url = 'http://bulkwp.com/addons/scheduler-for-deleting-posts-by-taxonomy/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=addonlist&utm_content=bd-stx';
		$this->messages      = array(
			'box_label' => __( 'By Custom Taxonomy', 'bulk-delete' ),
			'scheduled' => __( 'The selected posts are scheduled for deletion', 'bulk-delete' ),
		);
	}

	public function render() {
		$taxs =  get_taxonomies( array(
				'public'   => true,
				'_builtin' => false,
			), 'objects'
		);

		$terms_array = array();
		if ( count( $taxs ) > 0 ) {
			foreach ( $taxs as $tax ) {
				$terms = get_terms( $tax->name );
				if ( count( $terms ) > 0 ) {
					$terms_array[$tax->name] = $terms;
				}
			}
		}

		if ( count( $terms_array ) > 0 ) {
?>
        <!-- Custom tax Start-->
        <h4><?php _e( 'Select the post type from which you want to delete posts by custom taxonomy', 'bulk-delete' ); ?></h4>

        <fieldset class="options">
            <table class="optiontable">
				<?php $this->render_post_type_dropdown(); ?>
            </table>

            <h4><?php _e( 'Select the taxonomies from which you want to delete posts', 'bulk-delete' ) ?></h4>

            <table class="optiontable">
<?php
			foreach ( $terms_array as $tax => $terms ) {
?>
                <tr>
                    <td scope="row" >
                        <input name="smbd_taxs" value="<?php echo $tax; ?>" type="radio" class="custom-tax">
                    </td>
                    <td>
                        <label for="smbd_taxs"><?php echo $taxs[$tax]->labels->name; ?> </label>
                    </td>
                </tr>
<?php
			}
?>
            </table>

            <h4><?php _e( 'The selected taxonomy has the following terms. Select the terms from which you want to delete posts', 'bulk-delete' ) ?></h4>
            <p><?php _e( 'Note: The post count below for each term is the total number of posts in that term, irrespective of post type', 'bulk-delete' ); ?>.</p>
<?php
			foreach ( $terms_array as $tax => $terms ) {
?>
            <table class="optiontable terms_<?php echo $tax;?> terms">
<?php
				foreach ( $terms as $term ) {
?>
                    <tr>
                        <td scope="row" >
                            <input name="smbd_tax_terms[]" value="<?php echo $term->slug; ?>" type="checkbox" class="terms">
                        </td>
                        <td>
                            <label for="smbd_tax_terms"><?php echo $term->name; ?> (<?php echo $term->count . ' '; _e( 'Posts', 'bulk-delete' ); ?>)</label>
                        </td>
                    </tr>
<?php
				}
?>
            </table>
<?php
			}
?>
            <table class="optiontable">
				<?php
				$this->render_filtering_table_header();
				$this->render_restrict_settings();
				$this->render_delete_settings();
				$this->render_limit_settings();
				$this->render_cron_settings();
				?>
            </table>

            </fieldset>
<?php
			$this->render_submit_button();
		} else {
?>
            <h4><?php _e( "This WordPress installation doesn't have any non-empty custom taxonomies defined", 'bulk-delete' ) ?></h4>
<?php
		}
	}

	protected function convert_user_input_to_options( $request, $options ) {
		$options                       = array();
		$options['post_type']          = bd_array_get( $request, 'smbd_tax_post_type', 'post' );
		$options['selected_taxs']      = bd_array_get( $request, 'smbd_taxs' );
		$options['selected_tax_terms'] = bd_array_get( $request, 'smbd_tax_terms' );

		return $options;
	}

	public function delete( $delete_options ) {
		$delete_options = bd_convert_old_options_for_delete_post_by_status( $delete_options );
		$delete_options = apply_filters( 'bd_delete_options', $delete_options );

		$options        = array();
		$options        = bd_build_query_options( $delete_options, $options );
		$post_ids       = bd_query( $options );

		return $this->delete_posts_by_id( $post_ids, $delete_options['force_delete'] );
	}

	public static function delete_posts_by_taxonomy( $delete_options ) {
		// For compatibility reasons set default post type to 'post'
		$post_type = bd_array_get( $delete_options, 'post_type', 'post' );

		if ( array_key_exists( 'taxs_op', $delete_options ) ) {
			$delete_options['date_op'] = $delete_options['taxs_op'];
			$delete_options['days']    = $delete_options['taxs_days'];
		}

		$delete_options = apply_filters( 'bd_delete_options', $delete_options );

		$selected_taxs      = $delete_options['selected_taxs'];
		$selected_tax_terms = $delete_options['selected_tax_terms'];

		$options = array(
			'post_status' => 'publish',
			'post_type'   => $post_type,
			'tax_query'   => array(
				array(
					'taxonomy' => $selected_taxs,
					'terms'    => $selected_tax_terms,
					'field'    => 'slug',
				),
			),
		);

		$options  = bd_build_query_options( $delete_options, $options );
		$post_ids = bd_query( $options );
		foreach ( $post_ids as $post_id ) {
			// $force delete parameter to custom post types doesn't work
			if ( $delete_options['force_delete'] ) {
				wp_delete_post( $post_id, true );
			} else {
				wp_trash_post( $post_id );
			}
		}

		return count( $post_ids );
	}

	protected function get_success_message( $items_deleted ) {
		/* translators: 1 Number of pages deleted */
		return _n( 'Deleted %d post with the selected taxonomy', 'Deleted %d posts with the selected post taxonomy', $items_deleted, 'bulk-delete' );
	}
}
