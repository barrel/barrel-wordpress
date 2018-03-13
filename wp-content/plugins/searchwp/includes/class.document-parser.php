<?php

global $wp_filesystem;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/** @noinspection PhpIncludeInspection */
include_once ABSPATH . 'wp-admin/includes/file.php';

/**
 * Class SearchWPDebug is responsible for various debugging operations
 */
class SearchWPDocumentParser {

	private $id;
	private $post;
	private $filename;
	private $mime_type;
	private $stored_content;
	private $content;

	private $indexer;

	// TODO: ugh, this is kind of the best way to handle this but there has to be something better
	private $mimes = array(
		'pdf' => array(
			'application/pdf',
		),
		'text' => array(
			'text/plain',
			'text/csv',
			'text/tab-separated-values',
			'text/calendar',
			'text/css',
			'text/html',
		),
		'richtext' => array(
			'text/richtext',
			'application/rtf',
		),
		'msoffice_word' => array(
			// 'application/msword', // .doc is not supported at this time
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-word.document.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'application/vnd.ms-word.template.macroEnabled.12',
			'application/vnd.oasis.opendocument.text',
		),
		'msoffice_excel' => array(
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.ms-excel.addin.macroEnabled.12',
			'application/vnd.oasis.opendocument.spreadsheet',
			'application/vnd.oasis.opendocument.chart',
			'application/vnd.oasis.opendocument.database',
			'application/vnd.oasis.opendocument.formula',
		),
		'msoffice_powerpoint' => array(
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.template',
			'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'application/vnd.ms-powerpoint.slide.macroEnabled.12',
			'application/vnd.oasis.opendocument.presentation',
			'application/vnd.oasis.opendocument.graphics',
		),
	);

	/**
	 * SearchWPDocumentParser constructor.
	 *
	 * @param $file_id
	 */
	function __construct( $file_id ) {
		$file_id = absint( $file_id );

		$this->indexer = new SearchWPIndexer();

		$this->id = $file_id;
		$this->post = get_post( $this->id );
		$this->mime_type = $this->post->post_mime_type;
		$this->filename = get_attached_file( $this->id );

		// as of 2.6.2 stored PDF content is not removed when the index is purged
		// so as to save all of the time it takes to parse that content (also
		// to be considerate of PDF content that was painstakingly manually populated)
		$this->stored_content = $this->get_stored_document_content();

		// allow filtration of mimes
		foreach ( $this->mimes as $mime_type_group => $mimes ) {
			$this->mimes[ $mime_type_group ] = apply_filters( "searchwp_mimes_{$mime_type_group}", $mimes );
		}
	}

	/**
	 * Retrieve stored document content
	 *
	 * @return mixed|string
	 */
	function get_stored_document_content() {
		$stored_content = get_post_meta( $this->id, SEARCHWP_PREFIX . 'content', true );

		if ( empty( $stored_content ) ) {
			$stored_content = '';
		}

		return $stored_content;
	}

	/**
	 * Extract content from the file
	 *
	 * @return string
	 */
	function extract_document_content() {
		$content = false;

		$mime_class = $this->get_mime_class();

		if ( false === $mime_class ) {
			// It's not a file type that contains text we can parse (do NOT return false else it will be flagged for review)
			return '';
		}

		switch( $mime_class ) {
			case 'pdf':
				$content = $this->extract_pdf_content();
				break;

			case 'text':
				$content = $this->extract_text_content();
				break;

			case 'richtext':
				$content = $this->extract_rich_text_content();
				break;

			case 'msoffice_word':
				if ( 'application/msword' === $this->mime_type ) {
					// it's a .doc (as opposed to .docx)
					$content = $this->extract_msoffice_doc_text();
				} else {
					$content = $this->extract_msoffice_docx_text();
				}
				break;

			case 'msoffice_excel':
				$content = $this->extract_msoffice_excel_text();
				break;

			case 'msoffice_powerpoint':
				$content = $this->extract_msoffice_powerpoint_text();
				break;

			default:

		}

		$this->content = $content;

		return $this->content;
	}

	/**
	 * Extract text from this PowerPoint file
	 *
	 * @since 2.8
	 *
	 * @return string
	 */
	private function extract_msoffice_powerpoint_text() {
		// we can't use $this->get_file_content_from_package() because each slide is it's own file in the package

		do_action( 'searchwp_log', 'Extracting text from PowerPoint ' . $this->id );

		if ( ! class_exists( 'ZipArchive' ) ) {
			do_action( 'searchwp_log', 'PowerPoint parsing failed: ZipArchive not available' );
			return '';
		}

		$zip_handle = new ZipArchive;
		$output_text = '';

		if ( false !== strpos( $this->mime_type, 'opendocument' ) ) {
			$output_text = $this->get_file_content_from_package( 'content.xml' );
		} else {
			if ( true === $zip_handle->open( $this->filename ) ) {

				$slide_number = 1; // Loop through slide files

				while ( false !== ( $xml_index = $zip_handle->locateName( 'ppt/slides/slide' . absint( $slide_number ) . '.xml' ) ) ) {
					$xml_datas = $zip_handle->getFromIndex( $xml_index );
					$output_text .= ' ' . $this->get_xml_content( $xml_datas );
					$slide_number++;
				}

				$zip_handle->close();
			} else {
				do_action( 'searchwp_log', 'PowerPoint parsing failed: unable to open PowerPoint file ' . $this->id );
			}
		}

		return sanitize_text_field( $output_text );
	}

	/**
	 * Extract text from this Excel document
	 *
	 * @since 2.8
	 *
	 * @return string
	 */
	private function extract_msoffice_excel_text() {
		do_action( 'searchwp_log', 'Extracting text from Excel ' . $this->id );

		if ( false !== strpos( $this->mime_type, 'opendocument' ) ) {
			$content = $this->get_file_content_from_package( 'content.xml' );
		} else {
			$content = $this->get_file_content_from_package( 'xl/sharedStrings.xml' ); // this is stored in the .docx zip
		}

		return $content;
	}

	/**
	 * Use DOMDocument to get partially cleaned XML content
	 *
	 * @since 2.8
	 *
	 * @param string $data
	 *
	 * @return mixed|string
	 */
	private function get_xml_content( $data = '' ) {
		if ( ! class_exists( 'DOMDocument' ) ) {
			do_action( 'searchwp_log', 'Document parsing ERROR: DOMDocument not found' );
			return '';
		}

		$xml_handle = new DOMDocument();
		$xml_handle->loadXML( $data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING );

		return $this->indexer->clean_content( $xml_handle->saveXML(), true );
	}

	/**
	 * Retrieve embedded file content from MSOffice package
	 *
	 * @since 2.8
	 *
	 * @param $stored_xml_filename
	 *
	 * @return string
	 */
	private function get_file_content_from_package( $stored_xml_filename ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			do_action( 'searchwp_log', 'Document parsing failed: ZipArchive not available' );
			return '';
		}

		$output_text = '';

		$zip_handle = new ZipArchive;

		if ( true === $zip_handle->open( $this->filename ) ) {
			if ( false !== ( $xml_index = $zip_handle->locateName( $stored_xml_filename ) ) ) {
				$xml_datas = $zip_handle->getFromIndex( $xml_index );
				$output_text = $this->get_xml_content( $xml_datas );
			}
			$zip_handle->close();
		} else {
			do_action( 'searchwp_log', 'Document parsing failed: unable to open file ' . $this->id );
		}

		return sanitize_text_field( $output_text );
	}

	/**
	 * Extract text from this .docx
	 *
	 * @since 2.8
	 *
	 * @return bool|mixed|string
	 */
	private function extract_msoffice_docx_text() {
		do_action( 'searchwp_log', 'Extracting text from Word (.docx) ' . $this->id );

		if ( false !== strpos( $this->mime_type, 'opendocument' ) ) {
			$content = $this->get_file_content_from_package( 'content.xml' );
		} else {
			$content = $this->get_file_content_from_package( 'word/document.xml' ); // this is stored in the .docx zip
		}

		return $content;
	}

	/**
	 * Extract content from this .doc
	 *
	 * @return mixed|string
	 */
	private function extract_msoffice_doc_text() {
		do_action( 'searchwp_log', 'Extracting text from Word (.doc) ' . $this->id );
		do_action( 'searchwp_log', 'Document parsing ERROR: .doc not supported, convert to .docx ' . $this->id );

		return ''; // TODO: Unfortunately doesn't look possible without COM library usage
	}

	/**
	 * Determine which supported mime class the file is in
	 *
	 * @return bool|int|string
	 */
	private function get_mime_class() {
		$mime_class = '';
		$found = false;

		foreach ( $this->mimes as $mime_class => $mime_types ) {
			if ( in_array( $this->mime_type, $mime_types, true ) ) {
				$found = true;
				break;
			}
		}

		if ( ! $found ) {
			do_action( 'searchwp_log', 'Document parsing ERROR: mime type not found ' . $this->id );
		}

		return $found ? $mime_class : false;
	}

	/**
	 * Extract text content from file
	 *
	 * @return mixed|string
	 */
	private function extract_text_content() {
		$text_content = $this->wp_filesystem_get_contents();

		return sanitize_text_field( $text_content );
	}

	/**
	 * Use the WP_Filesystem() to retrieve the contents of this file
	 *
	 * @since 2.8
	 *
	 * @return string
	 */
	private function wp_filesystem_get_contents() {
		global $wp_filesystem;

		WP_Filesystem();

		if ( ! method_exists( $wp_filesystem, 'exists' ) || ! method_exists( $wp_filesystem, 'get_contents' ) ) {
			do_action( 'searchwp_log', 'Document parsing ERROR: $wp_filesystem methods missing ' . $this->id );
			return '';
		}

		/** @noinspection PhpUndefinedMethodInspection */
		$text_content = $wp_filesystem->exists( $this->filename ) ? $wp_filesystem->get_contents( $this->filename ) : '';

		return $text_content;
	}

	/**
	 * Extract plain text from this RTF document
	 *
	 * @since 2.8
	 *
	 * @return bool|mixed|string
	 */
	private function extract_rich_text_content() {
		$rtf_content = $this->wp_filesystem_get_contents();

		// load the RTF parser
		if ( file_exists( SWP()->dir . '/includes/class.parser.rtf.php' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once( SWP()->dir . '/includes/class.parser.rtf.php' );
		}

		if ( ! class_exists( 'SearchWP_RtfReader' ) ) {
			do_action( 'searchwp_log', 'Document parsing ERROR: SearchWP_RtfReader not found ' . $this->id );
			return '';
		}

		$reader = new SearchWP_RtfReader();
		$content = $reader->parse( $rtf_content );

		if ( ! $content ) {
			do_action( 'searchwp_log', 'RTF content failed parsing ' . $this->id );
			return false;
		}

		return $this->indexer->clean_content( $content, true );
	}

	/**
	 * Extract PDF content from the file
	 *
	 * @return string
	 */
	private function extract_pdf_content() {
		$pdf_content = apply_filters( 'searchwp_external_pdf_processing', $this->stored_content, $this->filename, $this->id );

		// only try to extract content if the external processing has not provided the PDF content we're looking for
		if ( ! file_exists( $this->filename ) || ! empty( $pdf_content ) ) {
			do_action( 'searchwp_log', 'PDF content externally populated ' . $this->id );
			return $pdf_content;
		}

		$pdf_content = $this->extract_pdf_text( $this->post->ID );

		return $pdf_content;
	}

	/**
	 * Extract plain text from PDF
	 *
	 * @since 2.5
	 *
	 * @param $post_id integer The post ID of the PDF in the Media library
	 *
	 * @return string The contents of the PDF
	 */
	function extract_pdf_text( $post_id = 0 ) {
		global $wp_filesystem, $searchwp;

		// this method was abstracted from class.indexer.php in version 2.8
		// so we might need to properly set the post ID
		if ( empty( $post_id ) ) {
			$post_id = $this->id;
		}

		$pdf_post = get_post( absint( $post_id ) );

		// make sure it's a PDF
		if ( 'application/pdf' !== $pdf_post->post_mime_type ) {
			return '';
		}

		// grab the filename of the PDF
		$filename = get_attached_file( absint( $post_id ) );

		// make sure the file exists locally
		if ( ! file_exists( $filename ) ) {
			do_action( 'searchwp_log', 'Document parsing ERROR: file does not exist ' . $this->id );
			return '';
		}

		if ( apply_filters( 'searchwp_skip_vendor_libs', false ) ) {
			// skip loading any files and just return an empty string
			do_action( 'searchwp_log', 'Skip PDF content extraction: vendor libraries skipped ' . $this->id );
			return '';
		}

		// PdfParser runs only on 5.3+ but SearchWP runs on 5.2+
		// PdfParser also uses mb_check_encoding() without checking for it
		if ( version_compare( PHP_VERSION, '5.3', '>=' ) && function_exists( 'mb_check_encoding' ) ) {

			/** @noinspection PhpIncludeInspection */
			include_once( $searchwp->dir . '/vendor/pdfparser-bootloader.php' );

			// a wrapper class was conditionally included if we're running PHP 5.3+ so let's try that
			if ( class_exists( 'SearchWP_PdfParser' ) ) {

				/** @noinspection PhpIncludeInspection */
				include_once( $searchwp->dir . '/vendor/pdfparser/vendor/autoload.php' );

				// try PdfParser first
				$parser = new SearchWP_PdfParser();
				$parser = $parser->init();
				try {
					$pdf = $parser->parseFile( $filename );
					$pdfContent = $pdf->getText();
				} catch (Exception $e) {
					do_action( 'searchwp_log', 'PDF parsing failed: ' . sanitize_text_field( $e->getMessage() ) );
					return false;
				}
			}
		} else {
			if ( ! function_exists( 'mb_check_encoding' ) ) {
				do_action( 'searchwp_log', 'Unable to load PDF parser, please install mbstring' );
			}
		}

		// try PDF2Text
		if ( empty( $pdfContent ) ) {
			if ( ! class_exists( 'PDF2Text' ) ) {
				/** @noinspection PhpIncludeInspection */
				include_once( $searchwp->dir . '/vendor/class.pdf2text.php' );
			}
			$pdfParser = new PDF2Text();
			$pdfParser->setFilename( $filename );
			$pdfParser->decodePDF();
			$pdfContent = $pdfParser->output();
		}

		// check to see if the first pass produced nothing or concatenated strings
		$pdfContent = trim( preg_replace( '/\\n|\\R/uiU', ' ', $pdfContent ) );
		$fullContentLength = strlen( $pdfContent );
		$numberOfSpaces = count( preg_split( '/(\\s{1,})/iu', $pdfContent ) );
		$spaces_percentage = floatval( apply_filters( 'searchwp_pdf_spaces_to_content_percentage', 5 ) );
		if ( empty( $pdfContent ) || ( ( $numberOfSpaces / $fullContentLength ) * 100 < $spaces_percentage ) ) {
			WP_Filesystem();

			if ( method_exists( $wp_filesystem, 'exists' ) && method_exists( $wp_filesystem, 'get_contents' ) ) {
				$filecontent = $wp_filesystem->exists( $filename ) ? $wp_filesystem->get_contents( $filename ) : '';
			} else {
				$filecontent = '';
			}

			if ( false !== strpos( $filecontent, 'trailer' ) ) {
				if ( ! class_exists( 'pdf_readstream' ) ) {
					/** @noinspection PhpIncludeInspection */
					include_once( $searchwp->dir . '/vendor/class.pdfreadstream.php' );
				}
				$pdfContent = '';
				$pdf = new pdf( get_attached_file( $this->post->ID ) );
				$pages = $pdf->get_pages();
				if ( ! empty( $pages ) ) {
					foreach ( $pages as $page ) {
						if ( method_exists( $page, 'get_text' ) ) {
							$pdfContent .= $page->get_text();
						}
					}
				}
			} else {
				// empty out the content so wacky concatenations are not indexed
				$pdfContent = false;
			}
		}

		return $pdfContent;
	}

	/**
	 * Extract PDF meta (PHP 5.3+)
	 *
	 * @since 2.5
	 *
	 * @param $post_id integer The post ID of the PDF in the Media library
	 *
	 * @return array The metadata of the PDF
	 */
	function extract_pdf_metadata( $post_id = 0 ) {
		global $searchwp;

		// this method was abstracted from class.indexer.php in version 2.8
		// so we might need to properly set the post ID
		if ( empty( $post_id ) ) {
			$post_id = $this->id;
		}

		$pdf_post = get_post( absint( $post_id ) );

		// make sure it's a PDF
		if ( 'application/pdf' !== $pdf_post->post_mime_type ) {
			return '';
		}

		// grab the filename of the PDF
		$filename = get_attached_file( absint( $post_id ) );

		// make sure the file exists locally
		if ( ! file_exists( $filename ) ) {
			return '';
		}

		$details = array();

		if ( apply_filters( 'searchwp_skip_vendor_libs', false ) ) {
			// skip loading any files and just return an empty array
			return $details;
		}

		// PdfParser runs only on 5.3+ but SearchWP runs on 5.2+
		if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {

			/** @noinspection PhpIncludeInspection */
			include_once( $searchwp->dir . '/vendor/pdfparser-bootloader.php' );

			// a wrapper class was conditionally included if we're running PHP 5.3+ so let's try that
			if ( class_exists( 'SearchWP_PdfParser' ) ) {

				/** @noinspection PhpIncludeInspection */
				include_once( $searchwp->dir . '/vendor/pdfparser/vendor/autoload.php' );

				// try PdfParser first
				$parser = new SearchWP_PdfParser();
				$parser = $parser->init();

				try {
					$pdf = $parser->parseFile( $filename );
					$details = $pdf->getDetails();
				} catch (Exception $e) {
					do_action( 'searchwp_log', 'PDF metadata parsing failed: ' . $e->getMessage() );
					return false;
				}
			}
		}

		return $details;
	}
}
