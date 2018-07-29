/**
 * Delete Posts by Post Type Module of Bulk Delete Plugin.
 *
 * Ideally the select2 for post type should be here.
 * But because of a wired issue in select2 this has been moved to select2.js file.
 *
 * @since 6.0.0
 */
/* global BulkWP */

/**
 * Validation for Post Type select2.
 */
BulkWP.validatePostTypeSelect2 = function(that) {
	if (null !== jQuery(that).parent().prev().children().find(".select2-post[multiple]").val()) {
		return true;
	} else {
		return false;
	}
};
