<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Data;

use WP_UnitTestCase;

/**
 * Tests for the JsonSchema class.
 */
class JsonSchemaTest extends WP_UnitTestCase {

	/**
	 * Test scalar, nullable types and required properties.
	 */
	public function test_scalar_and_nullable_types(): void {
		$this->assertSame(
			[
				'type'       => 'object',
				'properties' => [
					'name'    => [ 'type' => 'string' ],
					'age'     => [ 'type' => 'integer' ],
					'surname' => [
						'type'    => [ 'string', 'null' ],
						'default' => null,
					],
				],
				'required'   => [ 'name', 'age' ],
			],
			SampleData::jsonSchema()
		);
	}

	/**
	 * Test nested DataObject produces nested object schema.
	 */
	public function test_nested_data_object(): void {
		$this->assertSame(
			[
				'type'       => 'object',
				'properties' => [
					'child' => [
						'type'       => 'object',
						'properties' => [
							'name'    => [ 'type' => 'string' ],
							'age'     => [ 'type' => 'integer' ],
							'surname' => [
								'type'    => [ 'string', 'null' ],
								'default' => null,
							],
						],
						'required'   => [ 'name', 'age' ],
					],
					'label' => [ 'type' => 'string' ],
				],
				'required'   => [ 'child', 'label' ],
			],
			SampleWrapper::jsonSchema()
		);
	}

	/**
	 * Test array of DataObjects produces typed array schema.
	 */
	public function test_array_of_data_objects(): void {
		$this->assertSame(
			[
				'type'       => 'object',
				'properties' => [
					'name'  => [ 'type' => 'string' ],
					'items' => [
						'type'  => 'array',
						'items' => [
							'type'       => 'object',
							'properties' => [
								'name'    => [ 'type' => 'string' ],
								'age'     => [ 'type' => 'integer' ],
								'surname' => [
									'type'    => [ 'string', 'null' ],
									'default' => null,
								],
							],
							'required'   => [ 'name', 'age' ],
						],
					],
				],
				'required'   => [ 'name', 'items' ],
			],
			SampleCollection::jsonSchema()
		);
	}

	/**
	 * Test as_list wraps schema in array type.
	 */
	public function test_as_list_wraps_in_array(): void {
		$this->assertSame(
			[
				'type'  => 'array',
				'items' => [
					'type'       => 'object',
					'properties' => [
						'name'    => [ 'type' => 'string' ],
						'age'     => [ 'type' => 'integer' ],
						'surname' => [
							'type'    => [ 'string', 'null' ],
							'default' => null,
						],
					],
					'required'   => [ 'name', 'age' ],
				],
			],
			SampleData::jsonSchema( as_list: true )
		);
	}

	/**
	 * Test Field attribute adds title and description to schema.
	 */
	public function test_field_attribute(): void {
		$this->assertSame(
			[
				'type'       => 'object',
				'properties' => [
					'name'     => [
						'type'        => 'string',
						'title'       => 'Full Name',
						'description' => 'The person\'s full name',
					],
					'age'      => [
						'type'  => 'integer',
						'title' => 'Age',
					],
					'nickname' => [
						'type'    => [ 'string', 'null' ],
						'default' => null,
					],
				],
				'required'   => [ 'name', 'age' ],
			],
			SampleAnnotated::jsonSchema()
		);
	}
}
