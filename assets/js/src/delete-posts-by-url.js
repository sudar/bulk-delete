/**
 * JavaScript for delete posts by url module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, BulkWP*/
BulkWP.validateUrl = function(that) {
    if (jQuery(that).parent().prev().children('table').find("textarea").val() !== '') {
        return true;
    } else {
        return false;
    }
};
