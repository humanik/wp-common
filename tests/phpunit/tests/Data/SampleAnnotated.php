<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Data;

use Humanik\WP\Data\DataObject;
use Humanik\WP\Data\Field;

class SampleAnnotated extends DataObject {
	public function __construct(
		#[Field( 'Full Name', description: 'The person\'s full name' )]
		public readonly string $name,
		#[Field( 'Age' )]
		public readonly int $age,
		public readonly ?string $nickname = null,
	) {}
}
