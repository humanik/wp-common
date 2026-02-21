<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Support;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Exception\InvalidJson;
use WP_UnitTestCase;

/**
 * Tests for the DataObject class.
 */
class DataObjectTest extends WP_UnitTestCase {

	/**
	 * Test that from_array creates an instance with correct properties.
	 */
	public function test_from_array_creates_instance(): void {
		$data = SampleData::fromArray(
			[
				'name' => 'Alice',
				'age'  => 30,
			]
		);

		$this->assertInstanceOf( SampleData::class, $data );
		$this->assertSame( 'Alice', $data->name );
		$this->assertSame( 30, $data->age );
	}

	/**
	 * Test that from_json creates an instance from a JSON string.
	 */
	public function test_from_json_creates_instance(): void {
		$json = '{"name":"Bob","age":25}';
		$data = SampleData::fromJson( $json );

		$this->assertInstanceOf( SampleData::class, $data );
		$this->assertSame( 'Bob', $data->name );
		$this->assertSame( 25, $data->age );
	}

	/**
	 * Test that toArray returns an array representation.
	 */
	public function test_to_array_returns_array_representation(): void {
		$data  = SampleData::fromArray(
			[
				'name' => 'Charlie',
				'age'  => 40,
			]
		);
		$array = $data->toArray();

		$this->assertSame(
			[
				'name' => 'Charlie',
				'age'  => 40,
			],
			$array
		);
	}

	/**
	 * Test that toJson returns a valid JSON string.
	 */
	public function test_to_json_returns_json_string(): void {
		$data = SampleData::fromArray(
			[
				'name' => 'Dana',
				'age'  => 35,
			]
		);
		$json = $data->toJson();

		$this->assertSame( '{"name":"Dana","age":35}', $json );
	}

	/**
	 * Test that toJson accepts encoding options.
	 */
	public function test_to_json_accepts_options(): void {
		$data = SampleData::fromArray(
			[
				'name' => 'Eve',
				'age'  => 28,
			]
		);
		$json = $data->toJson( JSON_PRETTY_PRINT );

		$this->assertStringContainsString( "\n", $json );
		$this->assertSame(
			[
				'name' => 'Eve',
				'age'  => 28,
			],
			json_decode( $json, true ),
		);
	}

	/**
	 * Test round-trip through array serialization.
	 */
	public function test_round_trip_array(): void {
		$input  = [
			'name' => 'Frank',
			'age'  => 50,
		];
		$output = SampleData::fromArray( $input )->toArray();

		$this->assertSame( $input, $output );
	}

	/**
	 * Test round-trip through JSON serialization.
	 */
	public function test_round_trip_json(): void {
		$json   = '{"name":"Grace","age":22}';
		$output = SampleData::fromJson( $json )->toJson();

		$this->assertSame( $json, $output );
	}

	/**
	 * Test that from_array throws on invalid data.
	 */
	public function test_from_array_with_invalid_data_throws(): void {
		$this->expectException( MappingError::class );

		SampleData::fromArray( [ 'name' => 'Valid' ] );
	}

	/**
	 * Test that from_json throws on malformed JSON.
	 */
	public function test_from_json_with_invalid_json_throws(): void {
		$this->expectException( InvalidJson::class );

		SampleData::fromJson( '{invalid json' );
	}

	/**
	 * Test nested DataObject serialization and deserialization.
	 */
	public function test_nested_data_object_round_trip(): void {
		$input = [
			'child' => [
				'name' => 'Nested',
				'age'  => 10,
			],
			'label' => 'wrapper',
		];

		$wrapper = SampleWrapper::fromArray( $input );

		$this->assertInstanceOf( SampleWrapper::class, $wrapper );
		$this->assertInstanceOf( SampleData::class, $wrapper->child );
		$this->assertSame( 'Nested', $wrapper->child->name );
		$this->assertSame( 10, $wrapper->child->age );
		$this->assertSame( 'wrapper', $wrapper->label );

		$this->assertSame( $input, $wrapper->toArray() );
	}
}
