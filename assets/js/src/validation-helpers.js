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

/**
 * Validate textboxes.
 *
 * @param that Reference to the button.
 * @returns {boolean} True if validation succeeds, False otherwise.
 */
BulkWP.validateTextbox = function(that) {
	return ( "" !== jQuery(that).parent().prev().children().find(":input[type=number], :text").val() );
};
