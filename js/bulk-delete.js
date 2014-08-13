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

    // for post boxes
    postboxes.add_postbox_toggles(pagenow);

    jQuery.each(['_cats', '_tags', '_taxs', '_pages', '_post_status', '_types', '_cf', '_title', '_dup_title', '_post_by_role', 'u_userrole', '_feedback'], function (index, value) {
        // invoke the date time picker
        jQuery('#smbd' + value + '_cron_start').datetimepicker({
            timeFormat: 'HH:mm:ss'
        });

        jQuery('#smbd' + value + '_restrict').change(function () {
            toggle_date_restrict(value);
        });

        jQuery('#smbd' + value + '_limit').change(function () {
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

        if (jQuery(this).val() === 'delete_posts_by_url') {
            if (jQuery(this).parent().prev().children('table').find("textarea").val() !== '') {
                valid = true;
            } else {
                // not valid
                alert(BULK_DELETE.error.enterurl);
            }
        } else if (jQuery(this).val() === 'delete_posts_by_custom_field') {
            if (jQuery('#smbd_cf_key').val() !== '') {
                valid = true;
            } else {
                // not valid
                alert(BULK_DELETE.error.enter_cf_key);
            }

        } else if (jQuery(this).val() === 'delete_posts_by_title') {

            if (jQuery('#smbd_title_value').val() !== '') {
                valid = true;
            } else {
                // not valid
                alert(BULK_DELETE.error.enter_title);
            }

        } else if (jQuery(this).val() === 'delete_posts_by_duplicate_title') {
            // nothing to check for duplicate title
            valid = true;
        } else if (jQuery(this).val() === 'delete_jetpack_messages') {
            // nothing to check for jetpack messages
            valid = true;
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
            if (current_button.lastIndexOf('delete_users_by_role', 0) === 0) {
                return confirm(BULK_DELETE.msg.deletewarningusers);
            } else {
                return confirm(BULK_DELETE.msg.deletewarning);
            }
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
