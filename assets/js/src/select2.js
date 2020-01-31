/**
 * Add select2 functionality..
 */

/*global*/
jQuery( document ).ready( function () {
	/**
	 * Normal select2.
	 */
	jQuery( '.select2-taxonomy, .enhanced-dropdown, .enhanced-role-dropdown' ).select2( {
		width: '300px'
	} );

	/**
	 * Select 2 for posts types with status.
	 *
	 * The label of the selected item is modified to include the optgroup label.
	 */
	jQuery( '.enhanced-post-types-with-status' ).select2( {
		width: '400px',
		templateSelection: function (state) {
			if ( ! state.id ) {
				return state.text;
			}

			return jQuery(
				'<span>' + state.element.parentElement.label + ' - ' + state.text + '</span>'
			);
		}
	});
} );
