/**
 * JavaScript for handling Delete comment meta module.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.1.0
 */

/*global jQuery, document*/
jQuery( document ).ready( function () {
	jQuery( 'input.use-value' ).change( function () {
		var useValue      = jQuery( this ).val(),
			$valueFilters = jQuery( this ).parents( 'table' ).find( '.value-filters' );

		if ( 'true' === useValue ) {
			$valueFilters.removeClass( 'visually-hidden' );
		} else {
			$valueFilters.addClass( 'visually-hidden' );
		}
	} );
} );
