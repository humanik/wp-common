<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Data;

use Humanik\WP\Data\DataObject;

class SampleCollection extends DataObject {
	/**
	 * @param list<SampleData> $items
	 */
	public function __construct(
		public readonly string $name,
		public readonly array $items,
	) {}
}
