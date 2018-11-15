/**
 * JavaScript for "Bulk Delete Comment Meta" module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, document*/
jQuery( document ).ready( function () {
	jQuery("select.string").attr( 'disabled', 'true');
	jQuery("select.string").hide();
	jQuery( 'input[name="smbd_comment_meta_use_value"]' ).change( function () {
		if ( 'true' === jQuery( this ).val() ) {
			jQuery( '#smbd_comment_meta_filters' ).show();
		} else {
			jQuery( '#smbd_comment_meta_filters' ).hide();
		}
	} );
	
	jQuery( "select.meta-type" ).change( function () {
		if ('string' === jQuery(this).val()) {
			jQuery("select.numeric").attr( 'disabled', 'true' );
			jQuery("select.numeric").hide();
			jQuery("select.string").removeAttr( 'disabled' );
			jQuery("select.string").show();
		} else {
			jQuery("select.string").attr( 'disabled', 'true');
			jQuery("select.string").hide();
			jQuery("select.numeric").removeAttr( 'disabled' );
			jQuery("select.numeric").show();
		}
	} );

	jQuery( "select.numeric, select.string").change( function() {
		var metaValueTextBox = jQuery(this).parents('tr').find(':text');
		if ( -1 === ['EXISTS', 'NOT EXISTS'].indexOf( jQuery(this).val() ) ){
			metaValueTextBox.removeAttr('disabled');
			metaValueTextBox.show();
		} else {
			metaValueTextBox.attr('disabled', 'true');
			metaValueTextBox.hide();
		}
	} );
} );
