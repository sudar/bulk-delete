/**
 * Delete Posts by Stick Post Module.
 *
 * Part of Bulk Delete plugin.
 */
/*global BulkWP */
jQuery( document ).ready( function () {
	var stickyAction = jQuery( "input[name='smbd_sticky_post_sticky_action']" ),
		deleteAction = stickyAction.parents( 'tr' ).next(),
		deleteActionRadio = deleteAction.find('[type="radio"]'),
		deleteAttachmentAction = deleteAction.next(),
		deleteAttachmentCheckBox = deleteAttachmentAction.find('[type="checkbox"]'),
		deleteButton = jQuery( "button[value='delete_posts_by_sticky_post']" );

	deleteButton.html( 'Remove Sticky &raquo;' );
	deleteAction.hide();
	deleteAttachmentAction.hide();

	stickyAction.change( function () {
		if ( 'delete' === stickyAction.filter( ':checked' ).val() ) {
			deleteButton.html( 'Bulk Delete &raquo;' );
			deleteAction.show();
			deleteAttachmentAction.show();
		} else {
			deleteButton.html( 'Remove Sticky &raquo;' );
			deleteAction.hide();
			deleteAttachmentAction.hide();
		}
	} );

	deleteActionRadio.change( function () {
		if( "true" === deleteActionRadio.filter(':checked').val() ){
			deleteAttachmentCheckBox.removeAttr('disabled');
		} else {
			deleteAttachmentCheckBox.attr('disabled', 'true');
		}
	});
} );

/**
 * Validate that at least one post was selected.
 *
 * @returns {boolean} True if at least one post was selected, False otherwise.
 */
BulkWP.validateStickyPost = function () {
	return jQuery( "input[name='smbd_sticky_post[]']:checked" ).length > 0;
};

BulkWP.DeletePostsByStickyPostPreAction = function () {
	var stickyAction = jQuery( "input[name='smbd_sticky_post_sticky_action']:checked" ).val();

	if ( 'unsticky' === stickyAction ) {
		return 'unstickyPostsWarning';
	} else {
		return 'deletePostsWarning';
	}
};
