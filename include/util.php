<?php
/**
 * Utility functions for Bulk Delete Plugin
 *
 * http://sudarmuthu.com/wordpress/bulk-delete
 *
 * @author: Sudar <http://sudarmuthu.com>
 * 
 */

/**
 * Check whether a key is present. If present returns the value, else returns the default value
 *
 * @param <array> $array - Array whose key has to be checked
 * @param <string> $key - key that has to be checked
 * @param <string> $default - the default value that has to be used, if the key is not found (optional)
 *
 * @return <mixed> If present returns the value, else returns the default value
 * @author Sudar
 */
if (!function_exists('array_get')) {
    function array_get($array, $key, $default = NULL) {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
?>
