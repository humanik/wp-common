<?php

declare(strict_types=1);

namespace Humanik\WP\Data;

use Attribute;

/**
 * Attribute for annotating DataObject constructor parameters with schema metadata.
 */
#[Attribute( Attribute::TARGET_PARAMETER )]
class Field {
	public function __construct(
		public readonly string $title,
		public readonly ?string $description = null,
	) {}
}
