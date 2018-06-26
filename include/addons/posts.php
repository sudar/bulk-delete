<?php
/**
 * Post Addons related functions.
 *
 * @since      5.5
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Addon
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Register post related addons.
 *
 * @since 5.5
 */
function bd_register_post_addons() {
	$bd = BULK_DELETE();

	add_meta_box( Bulk_Delete::BOX_DUPLICATE_TITLE , __( 'By Duplicate Title'   , 'bulk-delete' ) , 'bd_render_delete_posts_by_duplicate_title_box' , $bd->posts_page , 'advanced' );
	add_meta_box( Bulk_Delete::BOX_POST_BY_ROLE    , __( 'By User Role'         , 'bulk-delete' ) , 'bd_render_delete_posts_by_user_role_box'       , $bd->posts_page , 'advanced' );
}
add_action( 'bd_add_meta_box_for_posts', 'bd_register_post_addons' );

/**
 * Render delete posts by duplicate title box.
 *
 * @since 5.5
 */
function bd_render_delete_posts_by_duplicate_title_box() {
	if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_DUPLICATE_TITLE ) ) {
		printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );

		return;
	}

	if ( ! class_exists( 'Bulk_Delete_Posts_By_Duplicate_Title' ) ) {
?>
		<!-- Duplicate Title box start-->
		<p>
			<span class = "bd-post-title-pro" style = "color:red">
				<?php _e( 'You need "Bulk Delete Posts by Duplicate Title" Addon, to delete post by duplicate title.', 'bulk-delete' ); ?>
				<a href = "http://bulkwp.com/addons/bulk-delete-posts-by-duplicate-title/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-dti">Buy now</a>
			</span>
		</p>
		<!-- Duplicate Title box end-->
<?php
	} else {
		Bulk_Delete_Posts_By_Duplicate_Title::render_delete_posts_by_duplicate_title_box();
	}
}

/**
 * Delete posts by user role.
 *
 * @since 5.5
 */
function bd_render_delete_posts_by_user_role_box() {
	if ( BD_Util::is_posts_box_hidden( Bulk_Delete::BOX_POST_BY_ROLE ) ) {
		printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete' ), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG );

		return;
	}
	if ( ! class_exists( 'Bulk_Delete_Posts_By_User_Role' ) ) {
?>
		<!-- Posts by user role start-->
		<p>
			<span class = "bd-post-by-role-pro" style = "color:red">
				<?php _e( 'You need "Bulk Delete Posts by User Role" Addon, to delete post based on User Role', 'bulk-delete' ); ?>
				<a href = "http://bulkwp.com/addons/bulk-delete-posts-by-user-role/?utm_source=wpadmin&utm_campaign=BulkDelete&utm_medium=buynow&utm_content=bd-ur">Buy now</a>
			</span>
		</p>
		<!-- Posts by user role end-->
<?php
	} else {
		Bulk_Delete_Posts_By_User_Role::render_delete_posts_by_user_role_box();
	}
}
