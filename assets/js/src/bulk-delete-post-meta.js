/**
 * JavaScript for "Bulk Delete Comment Meta" module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, document*/
jQuery( document ).ready( function () {
	jQuery( 'input[name="smbd_comment_meta_use_value"]' ).change( function () {
		if ( 'true' === jQuery( this ).val() ) {
			jQuery( '#smbd_comment_meta_filters' ).show();
		} else {
			jQuery( '#smbd_comment_meta_filters' ).hide();
		}
	} );
} );
