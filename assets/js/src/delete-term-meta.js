/**
 * Add support for deleting term meta.
 *
 * @since 6.1.0
 */

/*global ajaxurl*/
jQuery( document ).ready( function () {
	/**
	 * Load Taxonomy terms when taxonomy changes.
	 */
	function loadTerms ( taxonomyDropdown ) {
		taxonomyDropdown = typeof taxonomyDropdown !== 'undefined' ? taxonomyDropdown : this;

		var selectedTaxonomy = jQuery( taxonomyDropdown ).val();

		jQuery( '.enhanced-terms-dropdown' ).select2(
			{
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					delay: 250,
					data: function ( params ) {
						return {
							q: params.term,
							taxonomy: selectedTaxonomy,
							action: 'bd_load_taxonomy_terms'
						};
					},
					processResults: function ( data ) {
						var options = [];

						if ( data ) {
							jQuery.each(
								data, function ( index, dataPair ) {
									options.push( { id: dataPair[ 0 ], text: dataPair[ 1 ] } );
								}
							);
						}

						return {
							results: options
						};
					},
					cache: true
				},
				minimumInputLength: 2, // the minimum of symbols to input before perform a search.
				width: '300px'
			}
		);
	}

	/**
	 * Load Term metas when the term changes.
	 */
	function loadTermMetas() {
		var selectedTermId = jQuery( this ).val();

		jQuery( '.enhanced-term-meta-dropdown' ).select2(
			{
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					delay: 250,
					data: function () {
						return {
							term_id: selectedTermId,
							action: 'bd_load_term_metas'
						};
					},
					processResults: function ( data ) {
						var options = [];

						if ( data ) {
							jQuery.each(
								data, function ( index, dataPair ) {
									options.push( { id: dataPair[ 0 ], text: dataPair[ 0 ] } );
								}
							);
						}

						return {
							results: options
						};
					},
					cache: true
				},
				minimumInputLength: 2, // the minimum of symbols to input before perform a search.
				width: '300px'
			}
		);
	}

	jQuery( 'select[name="smbd_term_meta_taxonomy"]' ).change( loadTerms );
	loadTerms( 'select[name="smbd_term_meta_taxonomy"]' );

	jQuery( 'select[name="smbd_term_meta_term_id"]' ).change( loadTermMetas );
} );
