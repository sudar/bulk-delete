/**
 * JavaScript helpers for Meta Value Filter component.
 * Used in the following modules
 *
 * Bulk Delete Comment Meta
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.1.0
 */

/*global jQuery, document*/
jQuery( document ).ready( function () {
	jQuery( "select.meta-operators-char, select.meta-operators-date" ).attr( 'disabled', 'true' ).hide();
	jQuery( "span.date-fields, span.custom-date-fields, tr.date-format-fields" ).hide();

	jQuery( "select.meta-type" ).change( function () {
		var $this = jQuery( this ),
			metaType = $this.val(),
			filterContainer = $this.parents( 'tr' ),
			dateFormatContainer = filterContainer.next(),
			numericDropdown = filterContainer.find( "select.meta-operators-numeric" ),
			charDropdown = filterContainer.find( "select.meta-operators-char" ),
			dateDropdown = filterContainer.find( "select.meta-operators-date" ),
			dateFields = filterContainer.find( 'span.date-fields' ),
			relativeDateDropdown = dateFields.find( 'select' ),
			dateCustomFields = filterContainer.find( 'span.custom-date-fields' ),
			metaValue = filterContainer.find( '.date-picker' );

		switch ( metaType ) {
			case 'numeric':

				resetDateFilters();

				disableElements( charDropdown );
				hideElements( charDropdown );

				resetElements( numericDropdown );
				enableElements( numericDropdown );
				showElements( numericDropdown );

				break;

			case 'char':

				resetDateFilters();

				disableElements( numericDropdown );
				hideElements( numericDropdown );

				resetElements( charDropdown );
				enableElements( charDropdown );
				showElements( charDropdown );

				break;

			case 'date':

				disableElements( numericDropdown );
				hideElements( numericDropdown );
				disableElements( charDropdown );
				hideElements( charDropdown );

				resetElements( dateDropdown );
				enableElements( dateDropdown );
				showElements( dateDropdown );

				showElements( dateFields );

				hideElements( dateCustomFields );
				resetElements( relativeDateDropdown );
				enableElements( relativeDateDropdown );

				enableElements( dateFormatContainer );
				showElements( dateFormatContainer );

				resetElements( metaValue );
				enableElements( metaValue );
				showElements( metaValue );
				metaValue.datepicker( { dateFormat: "yy-mm-dd" } );

				break;
		}

		function resetDateFilters() {
			disableElements( dateDropdown );
			hideElements( dateDropdown );

			hideElements( dateFields );
			hideElements( dateCustomFields );
			hideElements( dateFormatContainer );

			resetElements( metaValue );
			enableElements( metaValue );
			showElements( metaValue );
			metaValue.datepicker( 'destroy' );
		}
	} );

	jQuery( "select.relative-date-fields" ).change( function () {
		var currentCustomFieldsSpan = jQuery( this ).parents( 'tr' ).find( "span.custom-date-fields" ),
			currentCustomFields = currentCustomFieldsSpan.find( 'select, input' );

		if ( 'custom' === jQuery( this ).val() ) {
			resetElements( currentCustomFields );
			enableElements( currentCustomFields );
			currentCustomFieldsSpan.show();
		} else {
			disableElements( currentCustomFields );
			currentCustomFieldsSpan.hide();
		}
	} );

	jQuery( "select.meta-operators-numeric, select.meta-operators-char, select.meta-operators-date" ).change( function () {
		var currentRowParent = jQuery( this ).parents( 'tr' ),
			dateFormatTextBoxRow = currentRowParent.next(),
			metaValueTextBox = currentRowParent.find( ':text' ),
			dateFieldsSpans = currentRowParent.find( 'span.date-fields' ),
			dateFields = dateFieldsSpans.find( 'select' ),
			dateCustomFieldsSpans = currentRowParent.find( 'span.custom-date-fields' ),
			dateCustomFields = dateCustomFieldsSpans.find( 'select, input' );

		if ( -1 === ['EXISTS', 'NOT EXISTS'].indexOf( jQuery( this ).val() ) ) {
			resetElements( metaValueTextBox );
			enableElements( metaValueTextBox );
			showElements( metaValueTextBox );
			disableElements( dateCustomFields );
			hideElements( dateCustomFieldsSpans );

			if ( 'date' === currentRowParent.find( 'select.meta-type' ).val() ) {
				resetElements( dateFields );
				enableElements( dateFields );
				showElements( dateFieldsSpans );
				enableElements( dateFormatTextBoxRow );
				showElements( dateFormatTextBoxRow );
			}
		} else {
			disableElements( metaValueTextBox );
			hideElements( metaValueTextBox );
			disableElements( dateFields );
			hideElements( dateFieldsSpans );
			disableElements( dateCustomFields );
			hideElements( dateCustomFieldsSpans );
			disableElements( dateFormatTextBoxRow );
			hideElements( dateFormatTextBoxRow );
		}
	} );

	function disableElements( elements ) {
		elements.attr( 'disabled', 'true' );
	}

	function enableElements( elements ) {
		elements.removeAttr( 'disabled' );
	}

	function showElements( elements ) {
		elements.show();
	}

	function hideElements( elements ) {
		elements.hide();
	}

	function resetElements( elements ) {
		elements.each( function () {
			if ( jQuery( this ).is( 'input' ) ) {
				jQuery( this ).val( '' );
			} else if ( jQuery( this ).is( 'select' ) ) {
				jQuery( this ).prop( 'selectedIndex', 0 );
			}
		} );
	}
} );
