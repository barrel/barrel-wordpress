<?php

// in order to accommodate for PHP 5.2 this needs to be abstracted to it's own file and conditionally included

if ( ! defined( 'ABSPATH' ) || ! defined( 'SEARCHWP_VERSION' ) ) {
	exit;
}

class SearchWP_PdfParser {

	function init() {
		$parser = new \Smalot\PdfParser\Parser();
		return $parser;
	}

}
