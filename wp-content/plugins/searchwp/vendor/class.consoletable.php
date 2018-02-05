<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'SEARCHWP_VERSION' ) ) {
	exit;
}

// This class has been edited to fit the confines of SearchWP

/**
 * This file is part of the PHPLucidFrame library.
 * The class makes you easy to build console style tables
 *
 * @package     PHPLucidFrame\Console
 * @since       PHPLucidFrame v 1.12.0
 * @copyright   Copyright (c), PHPLucidFrame.
 * @author      Sithu K. <cithukyaw@gmail.com>
 * @link        http://phplucidframe.github.io
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

class SearchWPConsoleTable {

	const HEADER_INDEX = -1;
	const HR = 'HR';

	/** @var array Array of table data */
	protected $data = array();

	/** @var boolean Border shown or not */
	protected $border = true;

	/** @var boolean All borders shown or not */
	protected $all_borders = false;

	/** @var integer Table padding */
	protected $padding = 1;

	/** @var integer Table left margin */
	protected $indent = 0;

	/** @var integer */
	private $row_index = -1;

	/** @var array */
	private $column_widths = array();

	/**
	 * Adds a column to the table header
	 *
	 * @param  mixed  Header cell content
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function add_header( $content = '' ) {
		$this->data[ self::HEADER_INDEX ][] = $content;

		return $this;
	}

	/**
	 * Set headers for the columns in one-line
	 *
	 * @param  array  Array of header cell content
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function set_headers( array $content ) {
		$this->data[ self::HEADER_INDEX ] = $content;

		return $this;
	}

	/**
	 * Get the row of header
	 */
	public function get_headers() {
		return isset( $this->data[ self::HEADER_INDEX ] ) ? $this->data[ self::HEADER_INDEX ] : null;
	}

	/**
	 * Adds a row to the table
	 *
	 * @param  array  $data The row data to add
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function add_row( array $data = null ) {
		$this->row_index++;

		if ( is_array( $data ) ) {
			foreach ( $data as $col => $content ) {
				$this->data[ $this->row_index ][ $col ] = $content;
			}
		}

		return $this;
	}

	/**
	 * Adds a column to the table
	 *
	 * @param  mixed    $content The data of the column
	 * @param  integer  $col     The column index to populate
	 * @param  integer  $row     If starting row is not zero, specify it here
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function add_column( $content, $col = null, $row = null ) {
		$row = null === $row ? $this->row_index : $row;
		if ( null === $col ) {
			$col = isset( $this->data[ $row ] ) ? count( $this->data[ $row ] ) : 0;
		}

		$this->data[ $row ][ $col ] = $content;

		return $this;
	}

	/**
	 * Show table border
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function show_border() {
		$this->border = true;

		return $this;
	}

	/**
	 * Hide table border
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function hide_border() {
		$this->border = false;

		return $this;
	}

	/**
	 * Show all table borders
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function show_all_borders() {
		$this->show_border();
		$this->all_borders = true;

		return $this;
	}

	/**
	 * Set padding for each cell
	 *
	 * @param  integer $value The integer value, defaults to 1
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function set_padding( $value = 1 ) {
		$this->padding = $value;

		return $this;
	}

	/**
	 * Set left indentation for the table
	 *
	 * @param  integer $value The integer value, defaults to 1
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function set_indent( $value = 0 ) {
		$this->indent = $value;

		return $this;
	}

	/**
	 * Add horizontal border line
	 *
	 * @return object SearchWPConsoleTable
	 */
	public function add_border_line() {
		$this->row_index++;
		$this->data[ $this->row_index ] = self::HR;

		return $this;
	}

	/**
	 * Print the table
	 *
	 * @return void
	 */
	// public function display() {
	// 	echo $this->get_table();
	// }

	/**
	 * Get the printable table content
	 * @return string
	 */
	public function get_table() {
		$this->calculate_column_width();

		$output = $this->border ? $this->get_border_line() : '';
		foreach ( $this->data as $y => $row ) {
			if ( self::HR === $row ) {
				if ( ! $this->all_borders ) {
					$output .= $this->get_border_line();
					unset( $this->data[ $y ] );
				}

				continue;
			}

			foreach ( $row as $x => $cell ) {
				$output .= $this->get_cell_output( $x, $row );
			}
			$output .= PHP_EOL;

			if ( self::HEADER_INDEX === $y ) {
				$output .= $this->get_border_line();
			} else {
				if ( $this->all_borders ) {
					$output .= $this->get_border_line();
				}
			}
		}

		if ( ! $this->all_borders ) {
			$output .= $this->border ? $this->get_border_line() : '';
		}

		return $output;
	}

	/**
	 * Get the printable border line
	 *
	 * @return string
	 */
	private function get_border_line() {
		$output = '';
		$column_count = count( $this->data[0] );
		for ( $col = 0; $col < $column_count; $col++ ) {
			$output .= $this->get_cell_output( $col );
		}

		if ( $this->border ) {
			$output .= '+';
		}

		$output .= PHP_EOL;

		return $output;
	}

	/**
	 * Get the printable cell content
	 *
	 * @param integer $index The column index
	 * @param array   $row   The table row
	 *
	 * @return string
	 */
	private function get_cell_output( $index, $row = null ) {
		$cell       = $row ? $row[ $index ] : '-';
		$width      = $this->column_widths[ $index ];
		$pad        = $row ? $width - strlen( $cell ) : $width;
		$padding    = str_repeat( $row ? ' ' : '-', $this->padding );

		$output = '';

		if ( 0 === $index ) {
			$output .= str_repeat( ' ', $this->indent );
		}

		if ( $this->border ) {
			$output .= $row ? '|' : '+';
		}

		$output .= $padding; # left padding
		$output .= str_pad( $cell, $width, $row ? ' ' : '-' ); # cell content
		$output .= $padding; # right padding

		if ( count( $row ) - 1 === $index && $this->border ) {
			$output .= $row ? '|' : '+';
		}

		return $output;
	}

	/**
	 * Calculate maximum width of each column
	 *
	 * @return array
	 */
	private function calculate_column_width() {
		foreach ( $this->data as $y => $row ) {
			if ( is_array( $row ) ) {
				foreach ( $row as $x => $col ) {
					if ( ! isset( $this->column_widths[ $x ] ) ) {
						$this->column_widths[ $x ] = strlen( $col );
					} else {
						if ( strlen( $col ) > $this->column_widths[ $x ] ) {
							$this->column_widths[ $x ] = strlen( $col );
						}
					}
				}
			}
		}

		return $this->column_widths;
	}
}
