/**
 * JavaScript for delete posts by comments module of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.1.0
 */

/*global jQuery, BulkWP*/
BulkWP.validateCommentsCount = function(that) {
    return ("" !== jQuery(that).parent().prev().children().find(":input.comments_count_num").val());
};
