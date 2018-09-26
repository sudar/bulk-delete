/**
 * JavaScript for "jetpack" module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, BulkWP*/
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
