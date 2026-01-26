<?php

declare(strict_types=1);

namespace Humanik\WP\Support;

use Illuminate\Support\LazyCollection;

/**
 * CLI helpers.
 */
class Stdin {
	/**
	 * Yield stdin lines as a generator.
	 *
	 * @param  resource|null  $stream  Input stream (defaults to STDIN).
	 * @return \Illuminate\Support\LazyCollection<array-key,string> A lazy collection of stdin lines.
	 */
	public static function lines( mixed $stream = null ): LazyCollection {
		$stream = \is_resource( $stream ) ? $stream : \STDIN;

		return LazyCollection::make(
			static function () use ( $stream ) {
				while ( ! feof( $stream ) ) {
					$line = fgets( $stream );
					if ( false === $line ) {
						break;
					}
					yield $line;
				}
			}
		);
	}

	/**
	 * Check if stdin has content available without blocking.
	 *
	 * @param  resource|null  $stream  Input stream (defaults to STDIN).
	 */
	public static function has_content( mixed $stream = null ): bool {
		$stream = \is_resource( $stream ) ? $stream : \STDIN;

		if ( \function_exists( 'posix_isatty' ) && \posix_isatty( $stream ) ) {
			return false;
		}

		$read   = [ $stream ];
		$write  = null;
		$except = null;
		$ready  = \stream_select( $read, $write, $except, 0, 0 );

		return false !== $ready && $ready > 0;
	}
}
