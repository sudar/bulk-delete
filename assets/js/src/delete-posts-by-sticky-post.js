/**
 * Add select2 functionality..
 */
/* global BulkWP */
jQuery( document ).ready( function () {
	/**
	 * Normal select2.
	 */
	jQuery( '.select2-sticky-post' ).select2( {
		width: '300px'
	} );
} );
BulkWP.validateStickyPostSelect2 = function(that) {
	if (null !== jQuery(that).parent().prev().children().find(".select2-sticky-post[multiple]").val()) {
		return true;
	} else {
		return false;
	}
};