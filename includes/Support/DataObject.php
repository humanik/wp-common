<?php

declare(strict_types=1);

namespace Humanik\WP\Support;

use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\Normalizer\Normalizer;
use CuyZ\Valinor\NormalizerBuilder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

use function Humanik\WP\make;

/**
 * @implements Arrayable<string,mixed>
 */
abstract class DataObject implements Arrayable, Jsonable {
	private static ?TreeMapper $mapper = null;

	/** @var Normalizer<array<mixed>|scalar|null>|null */
	private static ?Normalizer $normalizer = null;

	/**
	 * @param array<mixed> $data
	 */
	public static function fromArray( array $data ): static {
		return static::map( static::class, $data );
	}

	/**
	 * @param class-string<static> $signature
	 */
	protected static function map( string $signature, mixed $data ): static {
		return self::get_mapper()->map( $signature, $data );
	}

	/**
	 * @param array<mixed> $items
	 * @return Collection<int,static>
	 */
	public static function collect( array $items ): Collection {
		Assert::allIsArray( $items );

		return Collection::make( $items )
			->map( static::fromArray( ... ) )
			->values();
	}

	public static function fromJson( string $json ): static {
		$array = \json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		Assert::isArray( $array );

		return self::fromArray( $array );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array {
		/** @var array<string,mixed> */
		return (array) self::get_normalizer()->normalize( $this );
	}

	public function toJson( $options = 0 ): string {
		return (string) \wp_json_encode( $this->toArray(), $options );
	}

	private static function get_mapper(): TreeMapper {
		self::$mapper ??= make( MapperBuilder::class )->mapper();

		return self::$mapper;
	}

	/**
	 * @return Normalizer<array<mixed>|scalar|null>
	 */
	private static function get_normalizer(): Normalizer {
		self::$normalizer ??= make( NormalizerBuilder::class )->normalizer( Format::array() );

		return self::$normalizer;
	}
}
