<?php

declare(strict_types=1);

namespace Humanik\WP\Database;

use Humanik\WP\Database\Fields\PostFields;

/**
 * @property string $name
 * @property string $title
 * @property string $content
 * @property string $excerpt
 * @property string $date
 */
class Page extends PostModel {
	protected function configure_fields( PostFields $fields ): PostFields {
		$fields->column( 'name', store_key: 'post_name' );
		$fields->column( 'title', store_key: 'post_title' );
		$fields->column( 'content', store_key: 'post_content' );
		$fields->column( 'excerpt', store_key: 'post_excerpt' );
		$fields->column( 'date', store_key: 'post_date' );

		return $fields;
	}

	public static function get_post_type(): string {
		return 'page';
	}
}
