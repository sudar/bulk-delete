/**
 * JavaScript for Bulk Delete Plugin
 *
 * http://sudarmuthu.com/wordpress/bulk-delete
 *
 * @author: Sudar <http://sudarmuthu.com>
 * 
 */

/*jslint browser: true, devel: true*/
/*global BULK_DELETE, jQuery, document, postboxes, pagenow*/
jQuery(document).ready(function () {

    // hide all terms
    function hideAllTerms() {
        jQuery('table.terms').hide();
        jQuery('input.terms').attr('checked', false);
    }

    // call it for the first time
    hideAllTerms();

    /**
     * Toggle the date restrict fields
     */
    function toggle_date_restrict(el) {
        if (jQuery("#smbd_" + el + "_restrict").is(":checked")) {
            jQuery("#smbd_" + el + "_op").removeAttr('disabled');
            jQuery("#smbd_" + el + "_days").removeAttr('disabled');
        } else {
            jQuery("#smbd_" + el + "_op").attr('disabled', 'true');
            jQuery("#smbd_" + el + "_days").attr('disabled', 'true');
        }
    }

    /**
     * Toggle limit restrict fields
     */
    function toggle_limit_restrict(el) {
        if (jQuery("#smbd_" + el + "_limit").is(":checked")) {
            jQuery("#smbd_" + el + "_limit_to").removeAttr('disabled');
        } else {
            jQuery("#smbd_" + el + "_limit_to").attr('disabled', 'true');
        }
    }

    // for post boxes
    postboxes.add_postbox_toggles(pagenow);

    jQuery.each(['cats', 'tags', 'taxs', 'pages', 'post_status'], function (index, value) {
        // invoke the date time picker
        jQuery('#smbd_' + value + '_cron_start').datetimepicker({
            timeFormat: 'HH:mm:ss'
        });

        jQuery('#smbd_' + value + '_restrict').change(function () {
            toggle_date_restrict(value);
        });

        jQuery('#smbd_' + value + '_limit').change(function () {
            toggle_limit_restrict(value);
        });

    });

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

    // Handle clicking of submit buttons
    jQuery('button').click(function () {
        var current_button = jQuery(this).val(),
            valid = false;

        if (jQuery(this).val() === 'bulk-delete-specific') {
            if (jQuery(this).parent().prev().children('table').find("textarea").val() !== '') {
                valid = true;
            } else {
                // not valid
                alert(BULK_DELETE.error.enterurl);
            }
        } else {
            if (jQuery(this).parent().prev().children('table').find(":checkbox:checked[value!='true']").size() > 0) {
                // monstrous selector
                valid = true;
            } else {
                // not valid
                alert(BULK_DELETE.error.selectone);
            }
        }

        if (valid) {
            return confirm(BULK_DELETE.msg.deletewarning);
        }

        return false;
    });

    // Handle selection of all checkboxes of cats
    jQuery('#smbd_cats_all').change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery('input[name="smbd_cats[]"]').attr('checked', true);
        } else {
            jQuery('input[name="smbd_cats[]"]').attr('checked', false);
        }
    });

    // Handle selection of all checkboxes of tags
    jQuery('#smbd_tags_all').change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery('input[name="smbd_tags[]"]').attr('checked', true);
        } else {
            jQuery('input[name="smbd_tags[]"]').attr('checked', false);
        }
    });
});
