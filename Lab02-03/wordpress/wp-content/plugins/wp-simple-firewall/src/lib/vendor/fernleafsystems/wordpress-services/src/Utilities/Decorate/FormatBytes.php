<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Services\Utilities\Decorate;

class FormatBytes {

	/**
	 * https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
	 * @param int|string $bytes
	 */
	public static function Format( $bytes, int $precision = 2, string $separator = ' ' ) :string {
		return \implode( $separator, self::FormatParts( $bytes, $precision ) );
	}

	/**
	 * @param string|int $bytes
	 * @return array{value: float, unit: string}
	 */
	public static function FormatParts( $bytes, int $precision = 2 ) :array {
		$units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];

		$bytes = \max( $bytes, 0 );
		$pow = \floor( ( $bytes ? \log( $bytes ) : 0 )/\log( 1024 ) );
		$pow = \min( $pow, \count( $units ) - 1 );

		// Uncomment one of the following alternatives
		$bytes /= \pow( 1024, $pow );
		// $bytes /= (1 << (10 * $pow));

		return [
			'value' => \round( $bytes, $precision ),
			'unit'  => $units[ $pow ]
		];
	}
}