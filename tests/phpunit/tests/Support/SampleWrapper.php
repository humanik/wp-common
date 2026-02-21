<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Support;

use Humanik\WP\Support\DataObject;

class SampleWrapper extends DataObject {
	public function __construct(
		public readonly SampleData $child,
		public readonly string $label,
	) {}
}
