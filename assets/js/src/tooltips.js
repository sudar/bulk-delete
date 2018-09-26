/**
 * JavaScript for Tooltips of Bulk Delete Plugin.
 *
 * @author Sudar <https://bulkwp.com>
 * @since 6.0.0
 */

/*global jQuery, BulkWP*/
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
