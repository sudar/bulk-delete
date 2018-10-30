/**
 * JavaScript for delete users module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, BulkWP*/
jQuery( document ).ready( function () {
    var reassignSelectBox = jQuery(".reassign_user");
    var contentDeleteRadio;
    reassignSelectBox.each( function() {
        contentDeleteRadio = jQuery(this).parents('tr').find('[type="radio"]');
    });
    
    reassignSelectBox.each( function() {
        jQuery(this).attr('disabled', 'true');
    });

    contentDeleteRadio.change( function () {
        if( "true" === contentDeleteRadio.filter(':checked').val() ){
            reassignSelectBox.removeAttr('disabled');
		} else {
            reassignSelectBox.attr('disabled', 'true');
		}
	});

});

BulkWP.validateUserMeta = function() {
    return (jQuery('#smbd_u_meta_value').val() !== '');
};

BulkWP.validateUserRole = function(that) {
    return (null !== jQuery(that).parent().prev().find(".enhanced-role-dropdown").val());
};
