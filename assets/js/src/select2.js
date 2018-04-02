/**
 * Add select2 functionality..
 */
/*global ajaxurl*/
jQuery( document ).ready( function () {
	/**
	 * Normal select2.
	 */
	jQuery( '.select2' ).select2( {
		width: '300px'
	} );

	/**
	 * AJAX Select2.
	 */
	jQuery( '.select2-ajax' ).select2( {
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
		minimumInputLength: 2, // the minimum of symbols to input before perform a search
		width: '300px'
	} );
} );
