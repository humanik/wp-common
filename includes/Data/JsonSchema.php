<?php

declare(strict_types=1);

namespace Humanik\WP\Data;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Generates JSON Schema from DataObject class definitions.
 */
class JsonSchema {
	/** @var array<class-string<DataObject>,array<string,mixed>> */
	private array $cache = [];

	/**
	 * Parse a DataObject class and return its JSON Schema representation.
	 *
	 * @param class-string<DataObject> $data_class
	 * @return array<string,mixed>
	 */
	public function parse( string $data_class ): array {
		if ( isset( $this->cache[ $data_class ] ) ) {
			return $this->cache[ $data_class ];
		}

		$reflection  = new ReflectionClass( $data_class );
		$constructor = $reflection->getConstructor();

		if ( ! $constructor ) {
			return [ 'type' => 'object' ];
		}

		$properties = [];
		$required   = [];

		foreach ( $constructor->getParameters() as $param ) {
			$properties[ $param->getName() ] = $this->parse_parameter( $param );

			if ( ! $param->isDefaultValueAvailable() ) {
				$required[] = $param->getName();
			}
		}

		$schema = [
			'type'       => 'object',
			'properties' => $properties,
		];

		if ( $required ) {
			$schema['required'] = $required;
		}

		$this->cache[ $data_class ] = $schema;

		return $schema;
	}

	/**
	 * Parse a constructor parameter into its JSON Schema representation.
	 *
	 * @return array<string,mixed>
	 */
	private function parse_parameter( ReflectionParameter $param ): array {
		$type = $param->getType();

		if ( ! $type instanceof ReflectionNamedType ) {
			return [];
		}

		$schema = $this->parse_type( $type, $param );

		$attributes = $param->getAttributes( Field::class );
		if ( $attributes ) {
			$field = $attributes[0]->newInstance();

			$schema['title'] = $field->title;

			if ( null !== $field->description ) {
				$schema['description'] = $field->description;
			}
		}

		if ( $param->isDefaultValueAvailable() ) {
			$schema['default'] = $param->getDefaultValue();
		}

		return $schema;
	}

	/**
	 * Map a PHP type to its JSON Schema representation.
	 *
	 * @return array<string,mixed>
	 */
	private function parse_type( ReflectionNamedType $type, ?ReflectionParameter $param = null ): array {
		$name   = $type->getName();
		$schema = $this->map_type( $name, $param );

		if ( $type->allowsNull() ) {
			$schema['type'] = [ $schema['type'], 'null' ];
		}

		return $schema;
	}

	/**
	 * Map a PHP type name to its JSON Schema representation.
	 *
	 * @return array<string,mixed>
	 */
	private function map_type( string $type_name, ?ReflectionParameter $param ): array {
		return match ( $type_name ) {
			'string' => [ 'type' => 'string' ],
			'int'    => [ 'type' => 'integer' ],
			'float'  => [ 'type' => 'number' ],
			'bool'   => [ 'type' => 'boolean' ],
			'array'  => $this->parse_array_type( $param ),
			default  => $this->parse_class_type( $type_name ),
		};
	}

	/**
	 * Parse an array type, checking docblock for element type.
	 *
	 * @return array<string,mixed>
	 */
	private function parse_array_type( ?ReflectionParameter $param ): array {
		$schema = [ 'type' => 'array' ];

		if ( ! $param ) {
			return $schema;
		}

		$item_class = $this->parse_array_item_type( $param );

		if ( $item_class ) {
			$schema['items'] = $this->parse( $item_class );
		}

		return $schema;
	}

	/**
	 * Parse docblock annotation to find array element type.
	 *
	 * Supports list<T>, array<T>, and array<key, T> syntax.
	 *
	 * @return class-string<DataObject>|null
	 */
	private function parse_array_item_type( ReflectionParameter $param ): ?string {
		$method = $param->getDeclaringFunction();
		/** @var string|false $docblock */
		$docblock = $method->getDocComment();

		if ( ! $docblock ) {
			return null;
		}

		$param_name = $param->getName();
		$pattern    = '/@param\s+(?:list|array)<(?:[^,>]+,\s*)?([^>]+)>\s+\$' . preg_quote( $param_name, '/' ) . '/';

		if ( ! preg_match( $pattern, $docblock, $matches ) ) {
			return null;
		}

		$type_name = trim( $matches[1] );

		return $this->resolve_class( $type_name, $param );
	}

	/**
	 * Parse a class type (DataObject subclass) into nested object schema.
	 *
	 * @return array<string,mixed>
	 */
	private function parse_class_type( string $class_name ): array {
		if ( is_subclass_of( $class_name, DataObject::class ) ) {
			return $this->parse( $class_name );
		}

		return [ 'type' => 'object' ];
	}

	/**
	 * Resolve a short class name to its fully qualified name.
	 *
	 * @return class-string<DataObject>|null
	 */
	private function resolve_class( string $name, ReflectionParameter $param ): ?string {
		$declaring_class = $param->getDeclaringClass();

		if ( ! $declaring_class ) {
			return null;
		}

		// Already fully qualified.
		if ( class_exists( $name ) && is_subclass_of( $name, DataObject::class ) ) {
			return $name;
		}

		// Resolve relative to declaring class namespace.
		$namespace = $declaring_class->getNamespaceName();
		$fqcn      = $namespace . '\\' . $name;

		if ( class_exists( $fqcn ) && is_subclass_of( $fqcn, DataObject::class ) ) {
			/** @var class-string<DataObject> */
			return $fqcn;
		}

		return null;
	}
}
