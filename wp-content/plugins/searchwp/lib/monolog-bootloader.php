<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// In order to accommodate for PHP 5.2 this needs to be abstracted to it's own file and conditionally included

if ( ! defined( 'ABSPATH' ) || ! defined( 'SEARCHWP_VERSION' ) ) {
	exit;
}

include_once( SWP()->dir . '/vendor/autoload.php' );

class SearchWP_Monolog {

	protected $logger;

	function __construct( $logfile = '' ) {
		$pid = SWP()->get_pid();
		$full_pid = apply_filters( 'searchwp_debug_include_pid', false );
		if ( empty( $full_pid ) ) {
			$pid = substr( $pid, strlen( $pid ) - 5, strlen( $pid ) );
		}

		$stream = new StreamHandler( $logfile, Logger::DEBUG );

		// finally, create a formatter
		if ( class_exists( '\Monolog\Formatter\LineFormatter' ) ) {
			// the default date format is "Y-m-d H:i:s"
			$dateFormat = "Y-m-d H:i:s";
			// the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
			$output = "%datetime% [%channel%] %message%\n";

			$formatter = new \Monolog\Formatter\LineFormatter( $output, $dateFormat );

			$stream->setFormatter( $formatter );
		}

		$this->logger = new Logger( $pid );
		$this->logger->pushHandler( $stream );
	}

	function log( $message = '', $type = 'notice' ) {
		$message = sanitize_textarea_field( esc_html( $message ) );
		$message = str_replace( '=&gt;', '=>', $message ); // put back array identifiers
		$message = str_replace( '-&gt;', '->', $message ); // put back property identifiers
		$message = str_replace( '&#039;', "'", $message ); // put back apostrophe's

		$this->logger->debug( (string) $message );
	}

}
