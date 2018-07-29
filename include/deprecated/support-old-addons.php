<?php
/**
 * Support Old add-ons.
 *
 * V6.0.0 changed the way add-ons and modules are handled.
 * This file contains code to add the backward compatibility layer for old add-ons.
 * This compatibility code would be eventually removed once all the add-ons have got upgraded.
 *
 * @since 6.0.0
 */
use BulkWP\BulkDelete\Deprecated\Addons\DeleteFromTrashModule;
use BulkWP\BulkDelete\Deprecated\Addons\DeletePostsByCustomFieldModule;
use BulkWP\BulkDelete\Deprecated\Addons\DeletePostsByDuplicateTitleModule;
use BulkWP\BulkDelete\Deprecated\Addons\DeletePostsByTitleModule;
use BulkWP\BulkDelete\Deprecated\Addons\DeletePostsByUserRoleModule;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Load deprecated post modules.
 *
 * Older version of some add-ons require this compatibility code to work properly.
 * This compatibility code will be eventually removed.
 *
 * @since 6.0.0
 *
 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage $page Page object.
 */
function bd_load_deprecated_post_modules( $page ) {
	$trash_module = new DeleteFromTrashModule();
	$trash_module->set_item_type( 'posts' );
	$trash_module->load_if_needed( $page );

	$custom_fields_module = new DeletePostsByCustomFieldModule();
	$custom_fields_module->load_if_needed( $page );

	$title_module = new DeletePostsByTitleModule();
	$title_module->load_if_needed( $page );

	$duplicate_title_module = new DeletePostsByDuplicateTitleModule();
	$duplicate_title_module->load_if_needed( $page );

	$user_role_module = new DeletePostsByUserRoleModule();
	$user_role_module->load_if_needed( $page );
}
add_action( 'bd_after_posts_modules', 'bd_load_deprecated_post_modules' );

/**
 * Load deprecated page modules.
 *
 * Older version of some add-ons require this compatibility code to work properly.
 * This compatibility code will be eventually removed.
 *
 * @since 6.0.0
 *
 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage $page Page object.
 */
function bd_load_deprecated_page_modules( $page ) {
	$trash_module = new DeleteFromTrashModule();
	$trash_module->set_item_type( 'pages' );
	$trash_module->load_if_needed( $page );
}
add_action( 'bd_after_pages_modules', 'bd_load_deprecated_page_modules' );

/**
 * Enable nonce checks for old post add-ons.
 *
 * This is needed only to do automatic nonce checks for old add-ons and will be eventually removed.
 *
 * @since 6.0.0
 *
 * @param array                                 $actions Actions.
 * @param \BulkWP\BulkDelete\Core\Base\BasePage $page    Page to which actions might be added.
 *
 * @return array List of modified actions.
 */
function bd_enable_nonce_check_for_old_post_addons( $actions, $page ) {
	if ( 'bulk-delete-posts' !== $page->get_page_slug() ) {
		return $actions;
	}

	if ( class_exists( '\Bulk_Delete_Posts_By_User' ) ) {
		$actions[] = 'delete_posts_by_user';
	}

	if ( class_exists( '\Bulk_Delete_Posts_By_Attachment' ) ) {
		$actions[] = 'delete_posts_by_attachment';
	}

	if ( class_exists( '\Bulk_Delete_Posts_By_Content' ) ) {
		$actions[] = 'delete_posts_by_content';
	}

	return $actions;
}
add_filter( 'bd_page_actions', 'bd_enable_nonce_check_for_old_post_addons', 10, 2 );
