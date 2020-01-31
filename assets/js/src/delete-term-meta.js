/**
 * Add support for deleting term meta.
 *
 * @since 6.1.0
 */

/*global ajaxurl*/
jQuery( document ).ready( function () {
	var $taxonomyDropdown = jQuery( 'select[name="smbd_term_meta_taxonomy"]' ),
		$termDropdown = jQuery( 'select[name="smbd_term_meta_term"]' ),
		$metaDropdown = jQuery( 'select[name="smbd_term_meta_key"]' );

	/**
	 * Load Taxonomy terms when taxonomy changes.
	 */
	function loadTerms() {
		var selectedTaxonomy = $taxonomyDropdown.val();

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
		var selectedTermId = $termDropdown.val();

		jQuery.ajax( {
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: {
				term_id: selectedTermId,
				action: 'bd_load_term_metas'
			},
			success: function ( data ) {
				$metaDropdown.empty();
				$metaDropdown.select2( { data: data } );
			},
			error: function () {
				alert( "We are not able to load the term meta" );
			}
		} );
	}

	$taxonomyDropdown.change( loadTerms );
	loadTerms();

	$termDropdown.change( loadTermMetas );
} );
