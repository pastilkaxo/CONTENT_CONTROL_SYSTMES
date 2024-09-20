<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

use FernleafSystems\Wordpress\Services\Services;

class TestFileWritable {

	public const TEST_STRING = '/** ODP TEST STRING %s */';

	/**
	 * @param string $path
	 * @return bool
	 * @throws \Exception
	 */
	public function run( $path ) {
		if ( empty( $path ) ) {
			throw new \Exception( 'File path is empty' );
		}

		$FS = Services::WpFs();
		if ( $FS->isDir( $path ) ) {
			throw new \Exception( 'Path is a directory and not file-writable' );
		}

		if ( $FS->exists( $path ) ) {
			$content = $FS->getFileContent( $path );
			if ( \is_null( $content ) ) {
				throw new \Exception( 'Could not read file contents' );
			}
		}
		else {
			$content = '';
		}

		{ // Insert test string and write to file
			$testString = sprintf( self::TEST_STRING, Services::WpGeneral()->getTimeStringForDisplay() );
			$aLines = \explode( "\n", $content );
			$aLines[] = $testString;
			$FS->putFileContent( $path, \implode( "\n", $aLines ) );
		}

		{ // Re-read file contents and test for string
			$content = $FS->getFileContent( $path );
			$isStringPresent = \strpos( $content, $testString ) !== false;
		}

		{ // Remove test string
			if ( $isStringPresent ) {
				$aLines = \explode( "\n", $content );
				\array_pop( $aLines );
				$FS->putFileContent( $path, \implode( "\n", $aLines ) );
			}
		}

		return $isStringPresent;
	}
}