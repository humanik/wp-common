<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Data;

use CuyZ\Valinor\Mapper\MappingError;
use Illuminate\Support\Collection;
use JsonException;
use WP_UnitTestCase;

/**
 * Tests for the DataObject class.
 */
class DataObjectTest extends WP_UnitTestCase {

	/**
	 * Test that from_array creates an instance with correct properties.
	 */
	public function test_from_array_creates_instance(): void {
		$data = SampleData::from(
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
		$data  = SampleData::from(
			[
				'name' => 'Charlie',
				'age'  => 40,
			]
		);
		$array = $data->toArray();

		$this->assertSame(
			[
				'name'    => 'Charlie',
				'age'     => 40,
				'surname' => null,
			],
			$array
		);
	}

	/**
	 * Test that toJson returns a valid JSON string.
	 */
	public function test_to_json_returns_json_string(): void {
		$data = SampleData::from(
			[
				'name' => 'Dana',
				'age'  => 35,
			]
		);
		$json = $data->toJson();

		$this->assertSame( '{"name":"Dana","age":35,"surname":null}', $json );
	}

	/**
	 * Test that toJson accepts encoding options.
	 */
	public function test_to_json_accepts_options(): void {
		$data = SampleData::from(
			[
				'name' => 'Eve',
				'age'  => 28,
			]
		);
		$json = $data->toJson( JSON_PRETTY_PRINT );

		$this->assertStringContainsString( "\n", $json );
		$this->assertSame(
			[
				'name'    => 'Eve',
				'age'     => 28,
				'surname' => null,
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
		$output = SampleData::from( $input )->toArray();

		$this->assertSame(
			[
				'name'    => 'Frank',
				'age'     => 50,
				'surname' => null,
			],
			$output
		);
	}

	/**
	 * Test round-trip through JSON serialization.
	 */
	public function test_round_trip_json(): void {
		$json   = '{"name":"Grace","age":22}';
		$output = SampleData::fromJson( $json )->toJson();

		$this->assertSame( '{"name":"Grace","age":22,"surname":null}', $output );
	}

	/**
	 * Test that collect returns a Collection of typed instances.
	 */
	public function test_collect_returns_collection_of_instances(): void {
		$items = [
			[
				'name' => 'Alice',
				'age'  => 30,
			],
			[
				'name' => 'Bob',
				'age'  => 25,
			],
			[
				'name' => 'Charlie',
				'age'  => 40,
			],
		];

		$collection = SampleData::collect( $items );

		$this->assertInstanceOf( Collection::class, $collection );
		$this->assertCount( 3, $collection );
		$this->assertContainsOnlyInstancesOf( SampleData::class, $collection );
		$this->assertSame( 'Alice', $collection[0]->name );
		$this->assertSame( 25, $collection[1]->age );
		$this->assertSame( 'Charlie', $collection[2]->name );
	}

	/**
	 * Test that collect returns an empty Collection for empty input.
	 */
	public function test_collect_with_empty_array_returns_empty_collection(): void {
		$collection = SampleData::collect( [] );

		$this->assertInstanceOf( Collection::class, $collection );
		$this->assertTrue( $collection->isEmpty() );
	}

	/**
	 * Test that from_array throws on invalid data.
	 */
	public function test_from_array_with_invalid_data_throws(): void {
		$this->expectException( MappingError::class );

		SampleData::from( [ 'name' => 'Valid' ] );
	}

	/**
	 * Test that from_json throws on malformed JSON.
	 */
	public function test_from_json_with_invalid_json_throws(): void {
		$this->expectException( JsonException::class );

		SampleData::fromJson( '{invalid json' );
	}

	/**
	 * Test that a DataObject instance validates against its JSON schema.
	 */
	public function test_validates_against_json_schema(): void {
		$data = SampleData::from(
			[
				'name' => 'Alice',
				'age'  => 30,
			]
		);

		$this->assertTrue( rest_validate_value_from_schema( $data, SampleData::jsonSchema() ) );
	}

	/**
	 * Test that a nested DataObject instance validates against its JSON schema.
	 */
	public function test_nested_validates_against_json_schema(): void {
		$data = SampleWrapper::from(
			[
				'child' => [
					'name' => 'Nested',
					'age'  => 10,
				],
				'label' => 'wrapper',
			]
		);

		$this->assertTrue( rest_validate_value_from_schema( $data, SampleWrapper::jsonSchema() ) );
	}

	/**
	 * Test that a collection DataObject instance validates against its JSON schema.
	 */
	public function test_collection_validates_against_json_schema(): void {
		$data = SampleCollection::from(
			[
				'name'  => 'group',
				'items' => [
					[
						'name' => 'Alice',
						'age'  => 30,
					],
					[
						'name'    => 'Bob',
						'age'     => 25,
						'surname' => 'Smith',
					],
				],
			]
		);

		$this->assertTrue( rest_validate_value_from_schema( $data, SampleCollection::jsonSchema() ) );
	}

	/**
	 * Test nested DataObject serialization and deserialization.
	 */
	public function test_nested_data_object_round_trip(): void {
		$input = [
			'child' => [
				'name'    => 'Nested',
				'age'     => 10,
				'surname' => 'Doe',
			],
			'label' => 'wrapper',
		];

		$wrapper = SampleWrapper::from( $input );

		$this->assertInstanceOf( SampleWrapper::class, $wrapper );
		$this->assertInstanceOf( SampleData::class, $wrapper->child );
		$this->assertSame( 'Nested', $wrapper->child->name );
		$this->assertSame( 10, $wrapper->child->age );
		$this->assertSame( 'wrapper', $wrapper->label );

		$this->assertSame( $input, $wrapper->toArray() );
	}
}
