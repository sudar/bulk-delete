<?php
/**
 * Contains deprecated code and functions
 *
 * @package Bulk Delete
 * @subpackage deprecated
 * @author Sudar
 * @since 5.0
 */

// Before v5.0, the main plugin object is available as a global variable.
// Some old addons still use this.
// This is present right now for compatibility reason and
// may be removed in subsequent versions of the plugin
global $Bulk_Delete;
$Bulk_Delete = BULK_DELETE();
?>
