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
			numStrOpDropdowns  = currentRowParent.find("select.string, select.numeric"),
			strOpDropdown = currentRowParent.find("select.string"),
			NumOpDropdown = currentRowParent.find("select.numeric"),
			dateOpDropdown = currentRowParent.find("select.date"),
			dateFieldsSpans  = currentRowParent.find('span.date-fields'),
			dateFields  = dateFieldsSpans.find('select'),
			dateCustomFields = currentRowParent.find('span.custom-date-fields'),
			metaValueTextBox = currentRowParent.find('.date-picker');

		if ('string' === jQuery(this).val()) {
			disableElements(numDateOpDropdowns);
			hideElements(numDateOpDropdowns);
			disableElements(dateFields);
			disableElements(nextRowParent);
			hideElements(dateFieldsSpans);
			hideElements(dateCustomFields);
			hideElements(nextRowParent);
			resetElements(strOpDropdown);
			enableElements(strOpDropdown);
			showElements(strOpDropdown);
			showElements(metaValueTextBox);
			metaValueTextBox.datepicker('destroy');
		} else if ('numeric' === jQuery(this).val()) {
			disableElements(strDateOpDropdowns);
			hideElements(strDateOpDropdowns);
			disableElements(dateFields);
			disableElements(nextRowParent);
			hideElements(dateFieldsSpans);
			hideElements(dateCustomFields);
			hideElements(nextRowParent);
			resetElements(NumOpDropdown);
			enableElements(NumOpDropdown);
			showElements(NumOpDropdown);
			showElements(metaValueTextBox);
			metaValueTextBox.datepicker('destroy');
		} else if( 'date' === jQuery(this).val() ) {
			disableElements(numStrOpDropdowns);
			hideElements(numStrOpDropdowns);
			hideElements(dateCustomFields);
			resetElements(dateOpDropdown);
			enableElements(dateOpDropdown);
			showElements(dateOpDropdown);
			resetElements(dateFields);
			enableElements(dateFields);			
			enableElements(nextRowParent);
			showElements(dateFieldsSpans);
			showElements(nextRowParent);
			showElements(metaValueTextBox);
			metaValueTextBox.datepicker( {
				dateFormat: "yy-mm-dd"
			} );
		}
	} );

	jQuery( "select.relative-date-fields").change( function() {
		var currentCustomFields = jQuery(this).parents('tr').find("span.custom-date-fields"),
			currentCustomSelect = currentCustomFields.find('select');
		if( 'custom' === jQuery(this).val() ) {
			resetElements(currentCustomSelect);
			currentCustomFields.show();
		} else {
			currentCustomFields.hide();
		}
	} );

	jQuery( "select.numeric, select.string, select.date").change( function() {
		var currentRowParent        = jQuery(this).parents('tr'),
		    dateFormatTextBoxRow    = currentRowParent.next(),
			metaValueTextBox        = currentRowParent.find(':text'),
			dateFieldsSpans         = currentRowParent.find('span.date-fields'),
			dateFields              = dateFieldsSpans.find('select'),
			dateCustomFieldsSpans   = currentRowParent.find('span.custom-date-fields');
		if ( -1 === ['EXISTS', 'NOT EXISTS'].indexOf( jQuery(this).val() ) ){
			enableElements(metaValueTextBox);
			showElements(metaValueTextBox);
			hideElements(dateCustomFieldsSpans);
			if ( 'date' === currentRowParent.find('select.meta-type').val() ) {
				resetElements(dateFields);
				enableElements(dateFields);
				showElements(dateFieldsSpans);
				enableElements(dateFormatTextBoxRow);
				showElements(dateFormatTextBoxRow);
			}
		} else {
			disableElements(metaValueTextBox);
			hideElements(metaValueTextBox);
			disableElements(dateFields);
			hideElements(dateFieldsSpans);
			hideElements(dateCustomFieldsSpans);
			disableElements(dateFormatTextBoxRow);
			hideElements(dateFormatTextBoxRow);
		}
	} );

	function disableElements(elements) {
		elements.attr('disabled', 'true');
	}

	function enableElements(elements) {
		elements.removeAttr('disabled');
	}

	function showElements(elements) {
		elements.show();
	}

	function hideElements(elements) {
		elements.hide();
	}

	function resetElements(elements) {
		elements.prop('selectedIndex', 0);
	}
} );

BulkWP.validateMetaKey = function(that) {
	return ('' !== jQuery(that).parent().prev().children().find("input.meta-key").val());
};
