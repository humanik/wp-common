<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Data;

use Humanik\WP\Data\DataObject;

class SampleData extends DataObject {
	public function __construct(
		public readonly string $name,
		public readonly int $age,
		public readonly ?string $surname = null,
	) {}
}
