<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Support;

use Humanik\WP\Support\Stdin;
use WP_UnitTestCase;

/**
 * Tests for the Stdin helper.
 */
class StdinTest extends WP_UnitTestCase {

	public function test_lines_reads_lines_from_stream(): void {
		$stream = fopen( 'php://temp', 'r+' );
		fwrite( $stream, "first\nsecond\n" );
		rewind( $stream );

		$lines = Stdin::lines( $stream )->all();

		fclose( $stream );

		$this->assertSame( [ "first\n", "second\n" ], $lines );
	}

	public function test_lines_returns_empty_for_empty_stream(): void {
		$stream = fopen( 'php://temp', 'r+' );

		$lines = Stdin::lines( $stream )->all();

		fclose( $stream );

		$this->assertSame( [], $lines );
	}

	public function test_has_content_false_when_stream_empty(): void {
		if ( ! \function_exists( 'stream_socket_pair' ) ) {
			$this->markTestSkipped( 'stream_socket_pair is not available.' );
		}

		[ $read, $write ] = stream_socket_pair( STREAM_PF_UNIX, STREAM_SOCK_STREAM, 0 );

		$this->assertFalse( Stdin::has_content( $read ) );

		fclose( $write );
		fclose( $read );
	}

	public function test_has_content_true_when_stream_has_data(): void {
		if ( ! \function_exists( 'stream_socket_pair' ) ) {
			$this->markTestSkipped( 'stream_socket_pair is not available.' );
		}

		[ $read, $write ] = stream_socket_pair( STREAM_PF_UNIX, STREAM_SOCK_STREAM, 0 );

		fwrite( $write, 'data' );

		$this->assertTrue( Stdin::has_content( $read ) );

		fclose( $write );
		fclose( $read );
	}
}
