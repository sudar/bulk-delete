/**
 * JavaScript for Bulk Delete Plugin
 *
 * https://bulkwp.com
 *
 * @author: Sudar <http://sudarmuthu.com>
 */

/*global BulkWP, postboxes, pagenow */
jQuery(document).ready(function () {
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
	 * Change submit button text if scheduling deletion.
	 */
	jQuery( "input:radio.schedule-deletion" ).change( function () {
		var submitButton = jQuery( this ).parents( 'fieldset' ).next().find( 'button[name="bd_action"]' );

		var scheduledLable = 'Schedule Bulk Delete &raquo;';
		var label = 'Bulk Delete &raquo;';

		if ( submitButton.data( 'label' ) ) {
			label = submitButton.data( 'label' );
		}

		if ( submitButton.data( 'schedule-label' ) ) {
			scheduledLable = submitButton.data( 'schedule-label' );
		}

		if ( "true" === jQuery( this ).val() ) {
			submitButton.html( scheduledLable );
		} else {
			submitButton.html( label );
		}
	} );

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
	 * Toggle the date fields.
	 */
	function toggle_date_filter(el) {
		if ( 2 === jQuery("#smbd" + el + "_op").prop('selectedIndex') ) {
			// Enable and display published on date.
			jQuery("#smbd" + el + "_pub_date").prop('disabled', false);
			jQuery("#smbd" + el + "_pub_date").show();
			// Disable and hide other fields.
			jQuery("#smbd" + el + "_pub_date_start").prop('disabled', true);
			jQuery("#smbd" + el + "_pub_date_start").hide();
			jQuery("#smbd" + el + "_pub_date_end").prop('disabled', true);
			jQuery("#smbd" + el + "_pub_date_end").hide();
			jQuery("#smbd" + el + "_days").prop('disabled', true);
			jQuery("#smbd" + el + "_days_box").hide();
		} else if ( 3 === jQuery( "#smbd" + el + "_op" ).prop('selectedIndex') ){
			// Enable and display between date boxes.
			jQuery("#smbd" + el + "_pub_date_start").prop('disabled', false);
			jQuery("#smbd" + el + "_pub_date_start").show();
			jQuery("#smbd" + el + "_pub_date_end").prop('disabled', false);
			jQuery("#smbd" + el + "_pub_date_end").show();
			// Disable and hide other fields.
			jQuery("#smbd" + el + "_days").prop('disabled', true);
			jQuery("#smbd" + el + "_days_box").hide();
			jQuery("#smbd" + el + "_pub_date").prop('disabled', true);
			jQuery("#smbd" + el + "_pub_date").hide();
		} else {
			// Enable and display day box.
			jQuery("#smbd" + el + "_days").prop('disabled', false);
			jQuery("#smbd" + el + "_days_box").show();
			// Disable and hide other fields.
			jQuery("#smbd" + el + "_pub_date").prop('disabled', true);
			jQuery("#smbd" + el + "_pub_date").hide();
			jQuery("#smbd" + el + "_pub_date_start").prop('disabled', true);
			jQuery("#smbd" + el + "_pub_date_start").hide();
			jQuery("#smbd" + el + "_pub_date_end").prop('dsiabled', true);
			jQuery("#smbd" + el + "_pub_date_end").hide();
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
			jQuery("#smbd" + el + "_op").removeAttr('disabled');
		} else {
			jQuery("#smbd" + el + "_registered_days").attr('disabled', 'true');
			jQuery("#smbd" + el + "_op").attr('disabled', 'true');
		}
	}

	/**
	 * Toggle delete attachments
	 */
	function toggle_delete_attachments(el) {
		if ( "true" === jQuery('input[name="smbd' + el + '_force_delete"]:checked').val()) {
			jQuery("#smbd" + el + "_attachment").removeAttr('disabled');
		} else {
			jQuery("#smbd" + el + "_attachment").attr('disabled', 'true');
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

		jQuery('#smbd' + value + '_pub_date').datepicker( { dateFormat: 'yy-mm-dd' } );

		jQuery('#smbd' + value + '_pub_date_start').datepicker( { dateFormat: 'yy-mm-dd' } );

		jQuery('#smbd' + value + '_pub_date_end').datepicker( { dateFormat: 'yy-mm-dd' } );

		jQuery('#smbd' + value + '_op').change(function () {
			toggle_date_filter(value);
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

		jQuery('input[name="smbd' + value + '_force_delete"]').change(function () {
			toggle_delete_attachments(value);
		});

		jQuery( '#smbd' + value + '_no_posts' ).change( function () {
			toggle_post_type_dropdown( value );
		});
	});

	jQuery.each( BulkWP.pro_iterators, function ( index, value) {
		jQuery('.bd-' + value.replace( /_/g, '-' ) + '-pro').hide();

		// `<tr>` displays the documentation link when the pro add-on is installed.
		jQuery('tr.bd-' + value.replace( /_/g, '-' ) + '-pro').show();

		jQuery('#smbd_' + value + '_cron_freq, #smbd_' + value + '_cron_start, #smbd_' + value + '_cron').removeAttr('disabled');
	} );

	/**
	 * If the given string is a function, then run it and return result, otherwise return the string.
	 *
	 * @param mayBeFunction
	 * @param that
	 *
	 * @returns string
	 */
	function resolveFunction( mayBeFunction, that ) {
		if ( jQuery.isFunction( mayBeFunction ) ) {
			return BulkWP[ mayBeFunction ]( that );
		}

		return mayBeFunction;
	}

	// Validate user action.
	jQuery('button[name="bd_action"]').click(function () {
		var currentButton = jQuery(this).val(),
			deletionScheduled = false,
			valid = false,
			messageKey = "deletePostsWarning",
			errorKey = "selectPostOption";

		if ( "true" === jQuery( this ).parent().prev().find( 'input:radio.schedule-deletion:checked' ).val() ) {
			deletionScheduled = true;
		}

		if (currentButton in BulkWP.validators) {
			valid = BulkWP[BulkWP.validators[currentButton]](this);
		} else {
			if (jQuery(this).parent().prev().children('table').find(":checkbox:checked[value!='true']").size() > 0) { // monstrous selector
				valid = true;
			}
		}

		if ( ! valid ) {
			if ( currentButton in BulkWP.error_msg ) {
				errorKey = BulkWP.error_msg[ currentButton ];
			}

			alert( BulkWP.msg[ errorKey ] );
			return false;
		}

		if ( currentButton in BulkWP.pre_delete_msg ) {
			messageKey = resolveFunction( BulkWP.pre_delete_msg[ currentButton ], this );
		}

		// pre_action_msg is deprecated. This will be eventually removed.
		if ( currentButton in BulkWP.pre_action_msg ) {
			messageKey = resolveFunction( BulkWP.pre_action_msg[ currentButton ], this );
		}

		if ( deletionScheduled ) {
			if ( currentButton in BulkWP.pre_schedule_msg ) {
				messageKey = resolveFunction( BulkWP.pre_schedule_msg[ currentButton ], this );
			}
		}

		return confirm( BulkWP.msg[ messageKey ] );
	});
});
