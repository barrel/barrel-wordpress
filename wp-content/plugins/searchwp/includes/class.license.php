<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_License is responsible for activating and deactivating the license.
 *
 * @since 3.0
 */
class SearchWP_License extends SearchWP_Settings_Implementation_License {

	/**
	 * SearchWP_License Constructor.
	 *
	 * @since 3.0
	 */
	public function __construct() {
		$this->key    = SWP()->license;
		$this->status = SWP()->status;
	}
}

new SearchWP_License();
