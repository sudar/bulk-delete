/*! Bulk Delete - v5.6.1 %>
 * https://bulkwp.com
 * Copyright (c) 2018; * Licensed GPLv2+ */
/*global BulkWP, postboxes, pagenow */
jQuery(document).ready(function () {
	// Start Jetpack.
	BulkWP.jetpack();

	BulkWP.enableHelpTooltips( jQuery( '.bd-help' ) );

	jQuery( '.user_restrict_to_no_posts_filter' ).change( function() {
		var $this = jQuery(this),
			filterEnabled = $this.is( ':checked' ),
		    $filterItems = $this.parents( 'table' ).children().find( '.user_restrict_to_no_posts_filter_items' );

		if ( filterEnabled ) {
			$filterItems.removeClass( 'visually-hidden' );
		} else {
			$filterItems.addClass( 'visually-hidden' );
		}
	} );

	/**
	 * Enable Postbox handling
	 */
	postboxes.add_postbox_toggles(pagenow);

	/**
	 * Toggle the date restrict fields
	 */
	function toggle_date_restrict(el) {
		if (jQuery("#smbd" + el + "_restrict").is(":checked")) {
			jQuery("#smbd" + el + "_op").removeAttr('disabled');
			jQuery("#smbd" + el + "_days").removeAttr('disabled');
		} else {
			jQuery("#smbd" + el + "_op").attr('disabled', 'true');
			jQuery("#smbd" + el + "_days").attr('disabled', 'true');
		}
	}

	/**
	 * Toggle limit restrict fields
	 */
	function toggle_limit_restrict(el) {
		if (jQuery("#smbd" + el + "_limit").is(":checked")) {
			jQuery("#smbd" + el + "_limit_to").removeAttr('disabled');
		} else {
			jQuery("#smbd" + el + "_limit_to").attr('disabled', 'true');
		}
	}

	/**
	 * Toggle user login restrict fields
	 */
	function toggle_login_restrict(el) {
		if (jQuery("#smbd" + el + "_login_restrict").is(":checked")) {
			jQuery("#smbd" + el + "_login_days").removeAttr('disabled');
		} else {
			jQuery("#smbd" + el + "_login_days").attr('disabled', 'true');
		}
	}

	/**
	 * Toggle user registered restrict fields
	 */
	function toggle_registered_restrict(el) {
		if (jQuery("#smbd" + el + "_registered_restrict").is(":checked")) {
			jQuery("#smbd" + el + "_registered_days").removeAttr('disabled');
		} else {
			jQuery("#smbd" + el + "_registered_days").attr('disabled', 'true');
		}
	}

    /**
     * Toggle Post type dropdown.
     */
    function toggle_post_type_dropdown( el ) {
        // TODO: Check why the element is not toggling even when display:none is added by JS.
        if ( jQuery( "#smbd" + el + "_no_posts" ).is( ":checked" ) ) {
            jQuery( "tr#smbd" + el + "-post-type-dropdown" ).show();
        } else {
            jQuery( "tr#smbd" + el + "-post-type-dropdown" ).hide();
        }
    }

	// hide all terms
	function hideAllTerms() {
		jQuery('table.terms').hide();
		jQuery('input.terms').attr('checked', false);
	}
	// call it for the first time
	hideAllTerms();

	// taxonomy click handling
	jQuery('.custom-tax').change(function () {
		var $this = jQuery(this),
		$tax = $this.val(),
		$terms = jQuery('table.terms_' + $tax);

		if ($this.is(':checked')) {
			hideAllTerms();
			$terms.show('slow');
		}
	});

	// date time picker
	jQuery.each(BulkWP.dt_iterators, function (index, value) {
		// invoke the date time picker
		jQuery('#smbd' + value + '_cron_start').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss'
		});

		jQuery('#smbd' + value + '_restrict').change(function () {
			toggle_date_restrict(value);
		});

		jQuery('#smbd' + value + '_limit').change(function () {
			toggle_limit_restrict(value);
		});

		jQuery('#smbd' + value + '_login_restrict').change(function () {
			toggle_login_restrict(value);
		});

		jQuery('#smbd' + value + '_registered_restrict').change(function () {
			toggle_registered_restrict(value);
		});

		jQuery( '#smbd' + value + '_no_posts' ).change( function () {
			toggle_post_type_dropdown( value );
		});
	});

	jQuery.each( BulkWP.pro_iterators, function ( index, value) {
		jQuery('.bd-' + value.replace( '_', '-' ) + '-pro').hide();
		jQuery('#smbd_' + value + '_cron_freq, #smbd_' + value + '_cron_start, #smbd_' + value + '_cron').removeAttr('disabled');
	} );

	// Validate user action
	jQuery('button[name="bd_action"]').click(function () {
		var currentButton = jQuery(this).val(),
		valid = false,
		msg_key = "deletePostsWarning",
			error_key = "selectPostOption";

		if (currentButton in BulkWP.validators) {
			valid = BulkWP[BulkWP.validators[currentButton]](this);
		} else {
			if (jQuery(this).parent().prev().children('table').find(":checkbox:checked[value!='true']").size() > 0) { // monstrous selector
				valid = true;
			}
		}

		if (valid) {
			if (currentButton in BulkWP.pre_action_msg) {
				msg_key = BulkWP.pre_action_msg[currentButton];
			}

			return confirm(BulkWP.msg[msg_key]);
		} else {
			if (currentButton in BulkWP.error_msg) {
				error_key = BulkWP.error_msg[currentButton];
			}

			alert(BulkWP.msg[error_key]);
		}

		return false;
	});

	/**
	 * Validation functions
	 */
	BulkWP.noValidation = function() {
		return true;
	};

	BulkWP.validateSelect2 = function(that) {
		if (null !== jQuery(that).parent().prev().children().find(".select2[multiple]").val()) {
			return true;
		} else {
			return false;
		}
	};

	BulkWP.validateUrl = function(that) {
		if (jQuery(that).parent().prev().children('table').find("textarea").val() !== '') {
			return true;
		} else {
			return false;
		}
	};

	BulkWP.validateUserMeta = function() {
		if (jQuery('#smbd_u_meta_value').val() !== '') {
			return true;
		} else {
			return false;
		}
	};
});

BulkWP.jetpack = function() {
	jQuery('.bd-feedback-pro').hide();

	jQuery('#smbd_feedback_cron_freq, #smbd_feedback_cron_start, #smbd_feedback_cron').removeAttr('disabled');
	jQuery('#smbd_feedback_use_filter').removeAttr('disabled');

	// enable filters
	jQuery('input[name="smbd_feedback_use_filter"]').change(function() {
		if('true' === jQuery(this).val()) {
			// using filters
			jQuery('#jetpack-filters').show();
		} else {
			jQuery('#jetpack-filters').hide();
		}
	});

	// enable individual filters
	jQuery.each(['name', 'email', 'ip'], function (index, value) {
		jQuery('#smbd_feedback_author_' + value + '_filter').change(function() {
			if(jQuery(this).is(':checked')) {
				jQuery('#smbd_feedback_author_' + value + '_op').removeAttr('disabled');
				jQuery('#smbd_feedback_author_' + value + '_value').removeAttr('disabled');
			} else {
				jQuery('#smbd_feedback_author_' + value + '_op').attr('disabled', 'true');
				jQuery('#smbd_feedback_author_' + value + '_value').attr('disabled', 'true');
			}
		});
	});
};

BulkWP.enableHelpTooltips = function ( $selector ) {
	$selector.tooltip({
		content: function() {
			return jQuery(this).prop('title');
		},
		position: {
			my: 'center top',
			at: 'center bottom+10',
			collision: 'flipfit'
		},
		hide: {
			duration: 200
		},
		show: {
			duration: 200
		}
	});
};

/*global ajaxurl*/
jQuery( document ).ready( function () {
	/**
	 * Normal select2.
	 */
	jQuery( '.select2-taxonomy' ).select2( {
		width: '300px'
	} );

	/**
	 * Enable AJAX for Taxonomy Select2.
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
		minimumInputLength: 2, // the minimum of symbols to input before perform a search
		width: '300px'
	} );

	jQuery( '.select2-post' ).select2( {
		width: '300px',
		templateSelection: formatLabel
	} );
} );

function formatLabel (state) {
  if (!state.id) {
    return state.text;
  }
  var parentLabel = state.element.parentElement.label;
  var $state = jQuery(
  	    '<span>' + parentLabel + '-' + state.text + '</span>'
  );
  return $state;
};
//# sourceMappingURL=bulk-delete.js.map