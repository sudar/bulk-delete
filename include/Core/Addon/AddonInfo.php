<?php

namespace BulkWP\BulkDelete\Core\Addon;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Data about an add-on.
 *
 * This is a `Record` class that only contains data about a particular add-on.
 * `Info` suffix is generally considered bad, but this is an exception since the suffix makes sense here.
 *
 * @since 6.0.0
 */
class AddonInfo {
	protected $name;
	protected $code;
	protected $version;

	/**
	 * Construct AddonInfo from an array.
	 *
	 * @param array $details Details about the add-on.
	 */
	public function __construct( $details = array() ) {
		if ( ! is_array( $details ) ) {
			return;
		}

		$keys = array(
			'name',
			'code',
			'version',
		);

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $details ) ) {
				$this->{$key} = $details[ $key ];
			}
		}
	}

	public function get_name() {
		return $this->name;
	}
}
