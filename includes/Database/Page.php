<?php

declare(strict_types=1);

namespace Humanik\WP\Database;

/**
 * @property string $name
 * @property string $title
 * @property string $content
 * @property string $excerpt
 * @property string $date
 * @property array<mixed> $data
 * @property list<string> $books
 */
class Page extends Post {
	public static function get_post_type(): string {
		return 'page';
	}
}
