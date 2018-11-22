/**
 * JavaScript for "Bulk Delete Comment Meta" module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, document*/
jQuery( document ).ready( function () {
	jQuery("select.string, select.date").attr( 'disabled', 'true');
	jQuery("select.string, select.date").hide();
	jQuery("span.date-fields, span.custom-date-fields, tr.date-format-fields").hide();
	
	jQuery( 'input[name="smbd_comment_meta_use_value"]' ).change( function () {
		if ( 'true' === jQuery( this ).val() ) {
			jQuery( '#smbd_comment_meta_filters' ).show();
		} else {
			jQuery( '#smbd_comment_meta_filters' ).hide();
		}
	} );
	
	jQuery( "select.meta-type" ).change( function () {
		var currentRowParent = jQuery(this).parents('tr'),
			nextRowParent = currentRowParent.next(),
			metaValueTextBox = currentRowParent.find(':text');

		if ('string' === jQuery(this).val()) {
			currentRowParent.find("select.numeric, select.date").attr( 'disabled', 'true' );
			currentRowParent.find("select.numeric, select.date").hide();
			currentRowParent.find("select.string").removeAttr( 'disabled' );
			currentRowParent.find("select.string").show();
			currentRowParent.find("span.date-fields, span.custom-date-fields").hide();
			nextRowParent.hide();
			metaValueTextBox.datepicker('destroy');
		} else if ('numeric' === jQuery(this).val()) {
			currentRowParent.find("select.string, select.date").attr( 'disabled', 'true');
			currentRowParent.find("select.string, select.date").hide();
			currentRowParent.find("select.numeric").removeAttr( 'disabled' );
			currentRowParent.find("select.numeric").show();
			currentRowParent.find("span.date-fields, span.custom-date-fields").hide();
			nextRowParent.hide();
			metaValueTextBox.datepicker('destroy');
		} else {
			currentRowParent.find("select.string, select.numeric").attr( 'disabled', 'true');
			currentRowParent.find("select.string, select.numeric").hide();
			currentRowParent.find("select.date").removeAttr( 'disabled' );
			currentRowParent.find("select.date").show();
			currentRowParent.find("span.date-fields").show();
			nextRowParent.show();
			metaValueTextBox.datepicker( {
				dateFormat: "yy-mm-dd"
			} );
		}
	} );

	jQuery( "select.relative-date-fields").change( function() {
		var currentCustomFields = jQuery(this).parents('tr').find("span.custom-date-fields");
		if( 'custom' === jQuery(this).val() ) {
			currentCustomFields.show();
		} else {
			currentCustomFields.hide();
		}
	} );

	jQuery( "select.numeric, select.string, select.date").change( function() {
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
