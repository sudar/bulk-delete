/**
 * Add select2 functionality..
 */

/*global ajaxurl*/
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

	/**
	 * Enable AJAX for Taxonomy Select2.
	 *
	 * TODO: See if this is used anywhere. Else remove it.
	 */
	jQuery( '.select2-taxonomy-ajax' ).select2( {
		ajax: {
			url: ajaxurl,
			dataType: 'json',
			delay: 250,
			data: function ( params ) {
				return {
					q: params.term,
					taxonomy: jQuery( this ).attr( 'data-taxonomy' ),
					action: 'bd_load_taxonomy_term'
				};
			},
			processResults: function ( data ) {
				var options = [];

				if ( data ) {
					jQuery.each( data, function ( index, dataPair ) {
						options.push( { id: dataPair[ 0 ], text: dataPair[ 1 ] } );
					} );
				}

				return {
					results: options
				};
			},
			cache: true
		},
		minimumInputLength: 2, // the minimum of symbols to input before perform a search.
		width: '300px'
	} );
} );
