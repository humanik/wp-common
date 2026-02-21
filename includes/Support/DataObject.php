<?php

declare(strict_types=1);

namespace Humanik\WP\Support;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

abstract class DataObject {
	private static ?TreeMapper $mapper = null;

	/**
	 * @param array<mixed> $data
	 */
	public static function from_array( array $data ): static {
		return self::get_mapper()->map( static::class, $data );
	}

	public static function from_json( string $json ): static {
		return self::get_mapper()->map( static::class, Source::json( $json ) );
	}

	private static function get_mapper(): TreeMapper {
		self::$mapper ??= ( new MapperBuilder() )->mapper();

		return self::$mapper;
	}
}
