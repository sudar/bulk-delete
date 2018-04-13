<?php
/**
 * Plugin Name: Bulk Delete
 * Plugin Script: bulk-delete.php
 * Plugin URI: https://bulkwp.com
 * Description: Bulk delete users and posts from selected categories, tags, post types, custom taxonomies or by post status like drafts, scheduled posts, revisions etc.
 * Version: 5.6.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Author: Sudar
 * Author URI: https://sudarmuthu.com/
 * Text Domain: bulk-delete
 * Domain Path: languages/
 * === RELEASE NOTES ===
 * Check readme file for full release notes.
 */

/**
 * Copyright 2009  Sudar Muthu  (email : sudar@sudarmuthu.com)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// TODO: Do PHP Version check and proceed only if PHP 5.3 or above.
require_once 'include/deprecated/old-bulk-delete.php';
require_once 'load-bulk-delete.php';
bulk_delete_load( __FILE__ );
