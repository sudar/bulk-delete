/**
 * JavaScript for Delete Users module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, BulkWP*/
jQuery( document ).ready( function () {
	var reassignSelectBoxes = jQuery( ".reassign-user" ),
		contentDeleteRadios = jQuery( ".post-reassign" );

	reassignSelectBoxes.select2(
		{
			width: '200px'
		}
	);

	reassignSelectBoxes.each( function () {
		jQuery( this ).attr( 'disabled', 'true' );
	} );

	contentDeleteRadios.change( function () {
		var reassignSelectBox = jQuery( this ).parents( 'tr' ).find( '.reassign-user' );

		if ( "true" === jQuery( this ).val() ) {
			reassignSelectBox.removeAttr( 'disabled' );
		} else {
			reassignSelectBox.attr( 'disabled', 'true' );
		}
	} );
} );

BulkWP.validateUserMeta = function () {
	return (jQuery( '#smbd_u_meta_value' ).val() !== '');
};

BulkWP.validateUserRole = function ( that ) {
	return (null !== jQuery( that ).parent().prev().find( ".enhanced-role-dropdown" ).val());
};
