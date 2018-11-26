/**
 * JavaScript for "Bulk Delete Comment Meta" module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global BulkWP, jQuery, document*/
jQuery( document ).ready( function () {
	jQuery("select.string, select.date").attr( 'disabled', 'true');
	jQuery("select.string, select.date").hide();
	jQuery("span.date-fields, span.custom-date-fields, tr.date-format-fields").hide();
	
	jQuery( 'input.use-value' ).change( function () {
		if ( 'true' === jQuery( this ).val() ) {
			jQuery(this).parents('table').next().show();
		} else {
			jQuery(this).parents('table').next().hide();
		}
	} );
	
	jQuery( "select.meta-type" ).change( function () {
		var currentRowParent = jQuery(this).parents('tr'),
			nextRowParent = currentRowParent.next(),
			numDateOpDropdowns = currentRowParent.find("select.numeric, select.date"),
			strDateOpDropdowns = currentRowParent.find("select.string, select.date"),
			numStrOpDropdwons  = currentRowParent.find("select.string, select.numeric"),
			dateFieldsSpans  = currentRowParent.find('span.date-fields'),
			dateFields  = dateFieldsSpans.find('select, input'),
			metaValueTextBox = currentRowParent.find(':text');

		if ('string' === jQuery(this).val()) {
			numDateOpDropdowns.attr( 'disabled', 'true' );
			numDateOpDropdowns.hide();
			currentRowParent.find("select.string").removeAttr( 'disabled' );
			currentRowParent.find("select.string").show();
			disableAllDateFields(dateFields, nextRowParent);
			hideAllDateFields(dateFieldsSpans, nextRowParent);
			metaValueTextBox.datepicker('destroy');
		} else if ('numeric' === jQuery(this).val()) {
			strDateOpDropdowns.attr( 'disabled', 'true');
			strDateOpDropdowns.hide();
			currentRowParent.find("select.numeric").removeAttr( 'disabled' );
			currentRowParent.find("select.numeric").show();
			disableAllDateFields(dateFields, nextRowParent);
			hideAllDateFields(dateFieldsSpans, nextRowParent);
			metaValueTextBox.datepicker('destroy');
		} else if( 'date' === jQuery(this).val() ) {
			numStrOpDropdwons.attr( 'disabled', 'true');
			numStrOpDropdwons.hide();
			currentRowParent.find("select.date").removeAttr( 'disabled' );
			currentRowParent.find("select.date").show();
			enableAllDateFields(dateFields, nextRowParent);
			showAllDateFields(dateFieldsSpans, nextRowParent);
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
		var currentRowParent        = jQuery(this).parents('tr'),
		    dateFormatTextBoxRow    = currentRowParent.next(),
			metaValueTextBox = currentRowParent.find(':text'),
			dateFieldsSpans  = currentRowParent.find('span.date-fields, span.custom-date-fields'),
			dateFields  = dateFieldsSpans.find('select, input');
		if ( -1 === ['EXISTS', 'NOT EXISTS'].indexOf( jQuery(this).val() ) ){
			metaValueTextBox.removeAttr('disabled');
			metaValueTextBox.show();
			if ( 'date' === currentRowParent.find('select.meta-type').val() ) {
				enableAllDateFields(dateFields, dateFormatTextBoxRow);
				showAllDateFields(dateFieldsSpans, dateFormatTextBoxRow);
			}
		} else {
			metaValueTextBox.attr('disabled', 'true');
			metaValueTextBox.hide();
			disableAllDateFields(dateFields, dateFormatTextBoxRow);
			hideAllDateFields(dateFieldsSpans, dateFormatTextBoxRow);
		}
	} );

	function enableAllDateFields(elements, row){
		elements.removeAttr('disabled');
		row.find(':text').removeAttr('disabled');
	}

	function disableAllDateFields(elements, row){
		elements.attr('disabled', 'true');
		row.find(':text').attr('disabled', 'true');
	}

	function showAllDateFields(elements, row){
		elements.show();
		row.show();
	}

	function hideAllDateFields(elements, row){
		elements.hide();
		row.hide();
	}
} );

BulkWP.validateMetaKey = function(that) {
	return ('' !== jQuery(that).parent().prev().children().find("input.meta-key").val());
};
