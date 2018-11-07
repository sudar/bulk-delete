/**
 * JavaScript for "Bulk Delete Comment Meta" module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, document*/
jQuery( document ).ready( function () {
	jQuery("select.string").hide();
	jQuery( 'input[name="smbd_comment_meta_use_value"]' ).change( function () {
		if ( 'true' === jQuery( this ).val() ) {
			jQuery( '#smbd_comment_meta_filters' ).show();
		} else {
			jQuery( '#smbd_comment_meta_filters' ).hide();
		}
	} );
	
	jQuery( 'select[name="smbd_comment_meta_type"]' ).change( function () {
		if ('string' === jQuery(this).val()) {
			jQuery("select.numeric").hide();
			jQuery("select.string").show();
		} else {
			jQuery("select.string").hide();
			jQuery("select.numeric").show();
		}
	} );
} );
