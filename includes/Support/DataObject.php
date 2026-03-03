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
abstract class DataObject implements Arrayable, Jsonable, \JsonSerializable {
	private static ?TreeMapper $mapper      = null;
	private static ?JsonSchema $json_schema = null;

	/** @var Normalizer<array<mixed>|scalar|null>|null */
	private static ?Normalizer $normalizer = null;

	public static function from( mixed $data ): static {
		if ( \is_string( $data ) ) {
			return self::fromJson( $data );
		}

		return static::fromArray( (array) $data );
	}

	/**
	 * @param array<mixed> $data
	 */
	public static function fromArray( array $data ): static {
		return static::map( static::class, $data );
	}

	public static function fromJson( string $json ): static {
		$array = \json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		Assert::isArray( $array );

		return self::fromArray( $array );
	}

	/**
	 * @param array<mixed> $items
	 * @return Collection<int,static>
	 */
	public static function collect( array $items ): Collection {
		return Collection::make( $items )
			->map( static::from( ... ) )
			->values();
	}

	/**
	 * @return array<string,mixed>
	 */
	public static function jsonSchema(): array {
		self::$json_schema ??= new JsonSchema();

		return self::$json_schema->parse( static::class );
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

	/**
	 * @return array<string,mixed>
	 */
	public function jsonSerialize(): array {
		return $this->toArray();
	}

	/**
	 * @param class-string<static> $signature
	 */
	protected static function map( string $signature, mixed $data ): static {
		return self::get_mapper()->map( $signature, $data );
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
