/**
 * Add select2 functionality..
 */
jQuery( document ).ready( function () {
	
	jQuery( '.sticky_force_delete' ).hide();

	jQuery("input[name='smbd_sticky_post_sticky_option']").change(function(){
		var sticky_option = jQuery("input[name='smbd_sticky_post_sticky_option']:checked").val();
		if( sticky_option === "show" ){
			jQuery( '.sticky_force_delete' ).show();
		}else{
			jQuery( '.sticky_force_delete' ).hide();
		}
	});

} );