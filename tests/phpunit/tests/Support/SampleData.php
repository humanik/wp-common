<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Support;

use Humanik\WP\Support\DataObject;

class SampleData extends DataObject {
	public function __construct(
		public readonly string $name,
		public readonly int $age,
		public readonly ?string $surname = null,
	) {}
}
