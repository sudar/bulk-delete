/**
 * Delete Terms modules.
 */
jQuery( document ).ready( function () {
	jQuery( '.enhanced-taxonomy-list' ).select2( {
		width: '300px'
	} );
} );

/*global BulkWP */

/**
 * Validate that term name is not left blank.
 *
 * @returns {boolean} True if term name is not blank, False otherwise.
 */
BulkWP.validateTermName = function() {
	return (jQuery('input[name="smbd_terms_by_name_value"]').val() !== '');
};

/**
 * Validate that post count is not left blank.
 *
 * @returns {boolean} True if post count is not blank, False otherwise.
 */
BulkWP.validatePostCount = function() {
	return (jQuery('input[name="smbd_terms_by_post_count"]').val() !== '');
};
