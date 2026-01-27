<?php

declare(strict_types=1);

namespace Humanik\WP\Support;

/**
 * Helper class for post type operations.
 */
class PostType {
	/**
	 * Callbacks for allowing duplicate post slugs, keyed by post type.
	 *
	 * @var array<string, callable>
	 */
	private static array $allow_duplicates_callbacks = [];

	/**
	 * Callbacks for disallowing duplicate post slugs, keyed by post type.
	 *
	 * @var array<string, callable>
	 */
	private static array $disallow_duplicates_callbacks = [];

	/**
	 * Allow duplicate post names (slugs) in WordPress for a specific post type.
	 *
	 * This adds a filter that prevents WordPress from appending numeric suffixes
	 * to duplicate post slugs for the specified post type.
	 *
	 * @param string $post_type The post type to allow duplicate names for.
	 */
	public static function allow_duplicate_names( string $post_type ): void {
		self::remove_disallow_duplicate_names( $post_type );

		if ( isset( self::$allow_duplicates_callbacks[ $post_type ] ) ) {
			return;
		}

		$callback = static function (
			string $slug,
			int $post_id,
			string $post_status,
			string $current_post_type,
			int $post_parent,
			string $original_slug
		) use ( $post_type ): string {
			if ( $current_post_type === $post_type ) {
				return $original_slug;
			}

			return $slug;
		};

		self::$allow_duplicates_callbacks[ $post_type ] = $callback;
		add_filter( 'wp_unique_post_slug', $callback, 10, 6 );
	}

	/**
	 * Remove the allow duplicate post names filter for a specific post type.
	 *
	 * This restores the default WordPress behavior of appending numeric suffixes
	 * to duplicate post slugs.
	 *
	 * @param string $post_type The post type to remove the allow filter for.
	 */
	public static function remove_allow_duplicate_names( string $post_type ): void {
		if ( ! isset( self::$allow_duplicates_callbacks[ $post_type ] ) ) {
			return;
		}

		remove_filter( 'wp_unique_post_slug', self::$allow_duplicates_callbacks[ $post_type ], 10 );
		unset( self::$allow_duplicates_callbacks[ $post_type ] );
	}

	/**
	 * Disallow duplicate post names (slugs) in WordPress for a specific post type.
	 *
	 * This adds a filter that checks for existing posts with the same slug and
	 * throws an exception if a duplicate is found during insert.
	 *
	 * @param string $post_type The post type to disallow duplicate names for.
	 */
	public static function disallow_duplicate_names( string $post_type ): void {
		self::remove_allow_duplicate_names( $post_type );

		if ( isset( self::$disallow_duplicates_callbacks[ $post_type ] ) ) {
			return;
		}

		$callback = static function (
			?string $override_slug,
			string $post_name,
			int $post_id,
			string $post_status,
			string $current_post_type,
		) use ( $post_type ): ?string {
			if ( $current_post_type !== $post_type ) {
				return $override_slug;
			}

			if ( empty( $post_name ) ) {
				return $override_slug;
			}

			$existing = get_posts(
				[
					'post_type'      => $post_type,
					'name'           => $post_name,
					'post_status'    => $post_status,
					'posts_per_page' => 1,
					'exclude'        => $post_id ? [ $post_id ] : [],
					'fields'         => 'ids',
				]
			);

			if ( ! empty( $existing ) ) {
				throw new \RuntimeException(
					sprintf(
						'A %s with the slug "%s" already exists.',
						esc_html( $post_type ),
						esc_html( $post_name )
					)
				);
			}

			return $override_slug;
		};

		self::$disallow_duplicates_callbacks[ $post_type ] = $callback;
		add_filter( 'pre_wp_unique_post_slug', $callback, 10, 5 );
	}

	/**
	 * Remove the disallow duplicate post names filter for a specific post type.
	 *
	 * This restores the default WordPress behavior of appending numeric suffixes
	 * to duplicate post slugs.
	 *
	 * @param string $post_type The post type to remove the disallow filter for.
	 */
	public static function remove_disallow_duplicate_names( string $post_type ): void {
		if ( ! isset( self::$disallow_duplicates_callbacks[ $post_type ] ) ) {
			return;
		}

		remove_filter( 'pre_wp_unique_post_slug', self::$disallow_duplicates_callbacks[ $post_type ], 10 );
		unset( self::$disallow_duplicates_callbacks[ $post_type ] );
	}
}
