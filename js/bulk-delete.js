/**
 * JavaScript for Bulk Delete Plugin
 *
 * http://sudarmuthu.com/wordpress/bulk-delete
 *
 * @author: Sudar <http://sudarmuthu.com>
 * 
 */

/*global BULK_DELETE, jQuery, document*/
jQuery(document).ready(function () {
    // hide all terms
    function hideAllTerms() {
        jQuery('table.terms').hide();
        jQuery('input.terms').attr('checked', false);
    }

    /**
     * Toggle closing of different sections
     */
    jQuery('.postbox h3').click(function () {
        jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
    });

    // invoke the date time picker
    jQuery('#smbd_cats_cron_start').datetimepicker({
        timeFormat: 'HH:mm:ss'
    });

    jQuery('#smbd_tags_cron_start').datetimepicker({
        timeFormat: 'HH:mm:ss'
    });

    jQuery('#smbd_taxs_cron_start').datetimepicker({
        timeFormat: 'HH:mm:ss'
    });

    jQuery('#smbd_pages_cron_start').datetimepicker({
        timeFormat: 'HH:mm:ss'
    });

    jQuery('#smbd_post_status_cron_start').datetimepicker({
        timeFormat: 'HH:mm:ss'
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

    hideAllTerms();
});

// TODO: Bring these global functions inside the jQuery document selection callback
/**
 * Check All Check boxes
 */
function bd_checkAll(form) {
    for (i = 0, n = form.elements.length; i < n; i++) {
        if (form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick', 2))) {
            if (form.elements[i].checked == true) {
                form.elements[i].checked = false;
            } else {
                form.elements[i].checked = true;
            }
        }
    }
}

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

function toggle_limit_restrict(el) {
    if (jQuery("#smbd_" + el + "_limit").is(":checked")) {
        jQuery("#smbd_" + el + "_limit_to").removeAttr('disabled');
    } else {
        jQuery("#smbd_" + el + "_limit_to").attr('disabled', 'true');
    }
}

/**
 * Validate Form
 */
function bd_validateForm(form) {
    var valid = false;
    for (i = 0, n = form.elements.length; i < n; i++) {
        if (form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick', 2))) {
            if (form.elements[i].checked == true) {
                valid = true;
                break;
            }
        }
    }

    if (valid) {
        return confirm(BULK_DELETE.msg.deletewarning);
    } else {
        alert(BULK_DELETE.msg.selectone);
        return false;
    }
}
