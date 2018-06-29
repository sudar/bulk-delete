/**
 * Add select2 functionality..
 */
 /*global ajaxurl*/
jQuery( document ).ready( function () {
	
	jQuery( 'input[name=smbd_meta_term_taxonomy]' ).change(function(){
		jQuery( '.select2-terms' ).select2( {
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				delay: 250,
				data: function ( params ) {
					return {
						q: params.term,
						taxonomy: jQuery('input[name=smbd_meta_term_taxonomy]:checked').val(),
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
	});

	jQuery( '.select2-terms' ).change(function(){
		jQuery( '.select2-term-meta' ).select2( {
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				delay: 250,
				data: function () {
					return {
						term_id: jQuery('.select2-terms').val(),
						action: 'bd_load_taxonomy_term_meta'
					};
				},
				processResults: function ( data ) {
					var options = [];

					if ( data ) {
						jQuery.each( data, function ( index, dataPair ) {
							options.push( { id: dataPair[ 0 ], text: dataPair[ 0 ] } );
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
	});
	
} );