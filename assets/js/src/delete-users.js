/**
 * JavaScript for delete users module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, BulkWP*/
BulkWP.validateUserMeta = function() {
    return (jQuery('#smbd_u_meta_value').val() !== '');
};

BulkWP.validateUserRole = function(that) {
    return (null !== jQuery(that).parent().prev().find(".enhanced-role-dropdown").val());
};
