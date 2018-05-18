/* global BulkWP */
jQuery( document ).ready( function () {
	jQuery( '.select2-post' ).select2( {
		width: '300px',
		templateSelection: function (state) {
			if (!state.id) {
				return state.text;
			}

			var parentLabel = state.element.parentElement.label;
			var $state = jQuery(
				'<span>' + parentLabel + '-' + state.text + '</span>'
			);

			return $state;
		}
	});
});

BulkWP.validatePostTypeSelect2 = function(that) {
	if (null !== jQuery(that).parent().prev().children().find(".select2-post[multiple]").val()) {
		return true;
	} else {
		return false;
	}
};
