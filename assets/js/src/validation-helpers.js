/**
 * Common Validation helper functions.
 *
 * @since 6.0.0
 */

/*global BulkWP*/

/**
 * No need to validate anything.
 *
 * @returns {boolean} Returns true always.
 */
BulkWP.noValidation = function() {
	return true;
};

/**
 * Validate enhanced dropdowns.
 *
 * @param that Reference to the button.
 * @returns {boolean} True if validation succeeds, False otherwise.
 */
BulkWP.validateEnhancedDropdown = function ( that ) {
	var value = jQuery( that ).parent().prev().children().find( ".enhanced-dropdown" ).val();

	return ( value !== null && value !== '-1' );
};

BulkWP.validateSelect2 = function(that) {
	if ( null !== jQuery( that ).parent().prev().children().find( ".select2-taxonomy[multiple]" ).val() ) {
		return true;
	} else {
		return false;
	}
};

/**
 * Validate textboxes.
 *
 * @param that Reference to the button.
 * @returns {boolean} True if validation succeeds, False otherwise.
 */
BulkWP.validateTextbox = function(that) {
	return ( "" !== jQuery(that).parent().prev().children().find(":input[type=number].validate, :text.validate").val() );
};

/**
 * Validate checkboxes.
 *
 * @param that Reference to the button.
 * @returns {boolean} True if validation succeeds, False otherwise.
 */
BulkWP.validateCheckbox = function(that) {
	return ( jQuery(that).parent().prev().find("input:checkbox.validate").is ( ":checked" ) );
};
