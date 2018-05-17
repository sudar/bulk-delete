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