<?php

/**
 * RTF document parser forked from https://github.com/henck/rtf-html-php
 *
 * Original documentation:
 *
 * @author     Alexander van Oostenrijk
 * @author     Jonathan Christopher
 *
 * @copyright  2014 Alexander van Oostenrijk
 *
 * @license    GNU
 * @version    1
 * @link       http://www.independent-software.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_RtfReader
 */
class SearchWP_RtfReader {

	private $root = null;
	private $rtf;
	private $pos;
	private $char;
	private $len;
	private $group;
	private $output;
	private $states;
	private $state;

	/**
	 * SearchWP_RtfReader constructor.
	 */
	function __construct() {
		$this->rtf      = '';
		$this->pos      = 0;
		$this->len      = 0;
		$this->group    = null;
		$this->root     = null;
	}

	/**
	 * Parse RTF string
	 *
	 * @param $rtf
	 *
	 * @return bool
	 */
	function parse( $rtf ) {

		$this->rtf = utf8_encode( $rtf );
		$this->len = strlen( $this->rtf );
		
		try {

			while( $this->pos < $this->len ) {

				// Read the next character
				$this->get_char();

				if ( "\n" === $this->char || "\r" === $this->char ) {
					continue;
				}

				// Determine character type
				switch ( $this->char ) {
					case '{':
						$this->parse_start_group();
						break;
					case '}':
						$this->parse_end_group();
						break;
					case '\\':
						$this->parse_control();
						break;
					default:
						$this->parse_text();
						break;
				}
			}
			
			return $this->format();

		} catch ( Exception $ex ) {
			return false;
		}
	}

	/**
	 * Extract plain text from the parsed RTF
	 *
	 * @return string
	 */
	private function format() {
		$this->output = '';
		$this->states = array();
		$this->state = new SearchWP_RtfReader_State();
		
		array_push( $this->states, $this->state );
		
		$this->format_group( $this->root );
		
		return $this->output;
	}

	/**
	 * Format the current group
	 *
	 * @param $group
	 */
	protected function format_group( $group ) {

		if ( ! method_exists( $group, 'get_type' ) ) {
			return;
		}

		if ( ! method_exists( $group, 'is_destination' ) ) {
			return;
		}

		// Skip formatting
		if ( in_array( 
			$group->get_type(), 
			array( 'fonttbl', 'colortbl', 'stylesheet', 'info' ),
			true
		) ) {
			return;
		}
		
		// Skip pictures
		if ( 'pict' === substr( $group->get_type(), 0, 4 ) ) {
			return;
		}
		
		// Skip destinations
		if ( $group->is_destination() ) {
			return;
		}
		
		// Push a state onto the stack
		$this->state = clone $this->state;
		array_push( $this->states, $this->state );
		
		foreach ( $group->children as $child ) {
			switch ( get_class( $child ) ) {
				case 'SearchWP_RtfReader_RtfGroup':
					$this->format_group( $child );
					break;
				case 'SearchWP_RtfReader_RtfControlSymbol':
					$this->format_control_symbol( $child );
					break;
				case 'SearchWP_RtfReader_RtfText':
					$this->format_text( $child );
					break;
			}
		}
		
		// Pop state from stack
		array_pop( $this->states );
		$this->state = $this->states[ count( $this->states ) - 1 ];
	}

	/**
	 * Format the control symbol and append it to the output
	 *
	 * @param $symbol
	 */
	protected function format_control_symbol( $symbol ) {
		if ( "'" === $symbol->symbol ) {
			$this->output .= ' ' . htmlentities( chr( $symbol->parameter), ENT_QUOTES, 'UTF-8' ) . ' ';
		}
	}

	/**
	 * Append the text to the output
	 *
	 * @param $text
	 */
	protected function format_text( $text ) {
		$this->output .= ' ' . $text->text . ' ';
	}

	/**
	 * Get the current character
	 */
	protected function get_char() {
		$this->char = $this->rtf[ $this->pos++ ];
	}

	/**
	 * Parse the start group
	 */
	protected function parse_start_group() {
		$group = new SearchWP_RtfReader_RtfGroup();

		if ( null !== $this->group ) {
			$group->parent = $this->group;
		}

		if ( null === $this->root ) {
			$this->group = $this->root = $group;
		} else {
			array_push( $this->group->children, $group );
			$this->group = $group;
		}
	}

	/**
	 * Parse the end group
	 */
	protected function parse_end_group() {
		// Retrieve state of document from stack.
		$this->group = $this->group->parent;
	}

	/**
	 * Parse the control
	 */
	protected function parse_control() {
		// Beginning of an RTF control word or control symbol.
		// Look ahead by one character to see if it starts with
		// a letter (control world) or another symbol (control symbol):
		
		$this->get_char();
		$this->pos--;
		
		if ( $this->is_letter() ) {
			$this->parse_control_word();
		} else {
			$this->parse_control_symbol();
		}
	}

	/**
	 * Parse plain text up to backslash or brace unless escaped
	 *
	 * @throws Exception
	 */
	protected function parse_text() {
		$text = '';
		do {
			$terminate = $escape = false;
			
			// Escape?
			if ( '\\' === $this->char ) {
				// Perform lookahead to see if this is really an escape
				$this->get_char();

				switch ( $this->char ) {
					case '\\':
						$text .= '\\';
						break;
					case '{':
						$text .= '{';
						break;
					case '}':
						$text .= '}';
						break;
					default:
						// Not an escape; roll back
						$this->pos = $this->pos - 2;
						$terminate = true;
						break;
				}
			} elseif ( '{' === $this->char || '}' === $this->char ) {
				$this->pos--;
				$terminate = true;
			}
			
			if ( ! $terminate && ! $escape ) {
				$text .= $this->char;
				$this->get_char();
			}

		} while ( ! $terminate && $this->pos < $this->len );
		
		$rtf_text = new SearchWP_RtfReader_RtfText();
		$rtf_text->text = $text;
		
		// If a group does not exist then it is not a valid RTF file; throw exception
		if ( null === $this->group ) {
			throw new Exception();
		}
		
		array_push( $this->group->children, $rtf_text );
	}
	
	/**
	 * Determines if the current char is a letter
	 * 
	 * @return bool
	 */
	protected function is_letter() {
		if ( ord( $this->char ) >= 65 && ord( $this->char ) <= 90) {
			return true;
		}

		if ( ord( $this->char) >= 97 && ord($this->char) <= 122 ) {
			return true;
		}
		
		return false;
	}

	/**
	 * Determines if char is a digit
	 * 
	 * @return bool
	 */
	protected function is_digit() {
		if ( ord( $this->char ) >= 48 && ord( $this->char ) <= 57 ) {
			return true;
		}
		
		return false;
	}

	/**
	 * Parse control word
	 */
	protected function parse_control_word() {
		$this->get_char();
		$word = '';
		
		while ( $this->is_letter() ) {
			$word .= $this->char;
			$this->get_char();
		}
		
		// Read parameter (if any) consisting of digits (may be negative)
		$parameter = null;
		$negative = false;
		if ( '-' === $this->char ) {
			$this->get_char();
			$negative = true;
		}
		
		while ( $this->is_digit() ) {
			if ( null === $parameter ) {
				$parameter = 0;
			}
			
			$parameter = $parameter * 10 + $this->char;
			$this->get_char();
		}
		
		if ( null === $parameter ) {
			$parameter = 1;
		}
		
		if ( $negative ) {
			$parameter = -$parameter;
		}
		
		// If this is \u then the parameter is followed by a character.
		// If the current character is a space then it is a delimiter.
		// If it is not a space then it is part of the next item in the text.
		if ( 'u' !== $word && ' ' !== $this->char ) {
			$this->pos--;
		}
		
		$rtf_word = new SearchWP_RtfReader_RtfControlWord();
		$rtf_word->word = $word;
		$rtf_word->parameter = $parameter;
		
		array_push( $this->group->children, $rtf_word );
	}

	/**
	 * Parse control symbol
	 */
	protected function parse_control_symbol() {
		$this->get_char();
		$symbol = $this->char;
		
		// Symbols have no parameter however if this is a single quote
		// then it is followed by a 2 digit hex code
		$parameter = 0;
		if ( "'" === $symbol ) {
			$this->get_char();
			$parameter = $this->char;
			$this->get_char();
			$parameter = hexdec( $parameter . $this->char );
		}
		
		$rtf_symbol = new SearchWP_RtfReader_RtfControlSymbol();
		$rtf_symbol->symbol = $symbol;
		$rtf_symbol->parameter = $parameter;
		
		array_push( $this->group->children, $rtf_symbol );
	}
}

/**
 * Class SearchWP_RtfReader_State
 */
class SearchWP_RtfReader_State {

	/**
	 * SearchWP_RtfReader_State constructor.
	 */
	function __construct() {}
}

/**
 * Class SearchWP_RtfReader_RtfText
 */
class SearchWP_RtfReader_RtfText {
	public $text;
}

/**
 * Class SearchWP_RtfReader_RtfControlWord
 */
class SearchWP_RtfReader_RtfControlWord {
	public $word;
	public $parameter;
}

/**
 * Class SearchWP_RtfReader_RtfControlSymbol
 */
class SearchWP_RtfReader_RtfControlSymbol {
	public $symbol;
	public $parameter = 0;
}

/**
 * Class SearchWP_RtfReader_RtfGroup
 */
class SearchWP_RtfReader_RtfGroup {

	public $parent;
	public $children;

	/**
	 * SearchWP_RtfReader_RtfGroup constructor.
	 */
	function __construct() {
		$this->parent   = null;
		$this->children = array();
	}

	/**
	 * Get child type
	 *
	 * @return null
	 */
	function get_type() {
		if ( 0 === count( $this->children ) ) {
			return null;
		}

		$child = $this->children[0];

		if ( 'SearchWP_RtfReader_RtfControlWord' !== get_class( $child ) ) {
			return null;
		}

		return $child->word;
	}

	/**
	 * Returns whether it's a destination
	 *
	 * @return bool
	 */
	function is_destination() {
		if ( 0 === count( $this->children ) ) {
			return false;
		}

		$child = $this->children[0];

		if ( 'SearchWP_RtfReader_RtfControlSymbol' !== get_class( $child ) ) {
			return false;
		}

		return '*' === $child->symbol;
	}
}