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
	
	jQuery( 'input.use-value' ).change( function () {
		if ( 'true' === jQuery( this ).val() ) {
			jQuery(this).parents('table').next().show();
		} else {
			jQuery(this).parents('table').next().hide();
		}
	} );
	
	jQuery( "select.meta-type" ).change( function () {
		var currentRowParent = jQuery(this).parents('tr'),
			dateFormatTextBoxRow = currentRowParent.next(),
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
			hideElements(dateFieldsSpans);
			disableElements(dateFormatTextBoxRow);
			hideElements(dateFormatTextBoxRow);
			hideElements(dateCustomFields);
			resetElements(strOpDropdown);
			enableElements(strOpDropdown);
			showElements(strOpDropdown);
			resetElements(metaValueTextBox);
			showElements(metaValueTextBox);
			metaValueTextBox.datepicker('destroy');
		} else if ('numeric' === jQuery(this).val()) {
			disableElements(strDateOpDropdowns);
			hideElements(strDateOpDropdowns);
			disableElements(dateFields);
			hideElements(dateFieldsSpans);
			disableElements(dateFormatTextBoxRow);
			hideElements(dateFormatTextBoxRow);
			hideElements(dateCustomFields);
			resetElements(NumOpDropdown);
			enableElements(NumOpDropdown);
			showElements(NumOpDropdown);
			resetElements(metaValueTextBox);
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
			showElements(dateFieldsSpans);
			enableElements(dateFormatTextBoxRow);
			showElements(dateFormatTextBoxRow);
			resetElements(metaValueTextBox);
			showElements(metaValueTextBox);
			metaValueTextBox.datepicker( {
				dateFormat: "yy-mm-dd"
			} );
		}
	} );

	jQuery( "select.relative-date-fields").change( function() {
		var currentCustomFieldsSpan = jQuery(this).parents('tr').find("span.custom-date-fields"),
			currentCustomFields = currentCustomFieldsSpan.find('select, input');
		if( 'custom' === jQuery(this).val() ) {
			resetElements(currentCustomFields);
			enableElements(currentCustomFields);
			currentCustomFieldsSpan.show();
		} else {
			disableElements(currentCustomFields);
			currentCustomFieldsSpan.hide();
		}
	} );

	jQuery( "select.numeric, select.string, select.date").change( function() {
		var currentRowParent      = jQuery(this).parents('tr'),
		    dateFormatTextBoxRow  = currentRowParent.next(),
			metaValueTextBox      = currentRowParent.find(':text'),
			dateFieldsSpans       = currentRowParent.find('span.date-fields'),
			dateFields            = dateFieldsSpans.find('select'),
			dateCustomFieldsSpans = currentRowParent.find('span.custom-date-fields'),
			dateCustomFields      = dateCustomFieldsSpans.find('select, input');
		if ( -1 === ['EXISTS', 'NOT EXISTS'].indexOf( jQuery(this).val() ) ){
			resetElements(metaValueTextBox);
			enableElements(metaValueTextBox);
			showElements(metaValueTextBox);
			disableElements(dateCustomFields);
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
			disableElements(dateCustomFields);
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
		elements.each( function() {
			if(jQuery(this).is('input')) {
				jQuery(this).val('');
			} else if(jQuery(this).is('select')) {
				jQuery(this).prop('selectedIndex', 0);
			}
		});
	}
} );
