<?php
/**
 * Customize the footer text in plugin pages
 *
 * @package Bulk Delete
 * @subpackage admin
 * @author Sudar
 * @since 4.5
 */

/**
 * Add rating links to the admin dashboard
 *
 * @since	    4.5
 * @param       string $footer_text The existing footer text
 * @return      string
 */
function bd_add_rating_link( $footer_text ) {
    $rating_text = sprintf( __( 'Thank you for using <a href = "%1$s">Bulk Delete</a> plugin! Kindly <a href = "%2$s">rate us</a> at <a href = "%2$s">WordPress.org</a>', 'bulk-delete' ),
        'http://sudarmuthu.com/wordpress/bulk-delete',
        'http://wordpress.org/support/view/plugin-reviews/bulk-delete?filter=5#postform'
    );

    $rating_text = apply_filters( 'bd_rating_link', $rating_text );
    return str_replace( '</span>', '', $footer_text ) . ' | ' . $rating_text . '</span>';
}

/**
 * Modify admin footer in Bulk Delete plugin pages
 *
 * @since	    4.5
 */
function bd_modify_admin_footer() {
    add_filter( 'admin_footer_text', 'bd_add_rating_link' );
}

// Modify admin footer
add_action( 'bd_admin_footer_posts_page', 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_pages_page', 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_users_page', 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_cron_page', 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_info_page', 'bd_modify_admin_footer' );
?>
