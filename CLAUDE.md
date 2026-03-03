## Project Overview

WP Common is a PHP library providing common utilities for WordPress development. It includes a Laravel-compatible dependency injection container and an ORM-like database layer for WordPress posts.

- **Namespace**: `Humanik\WP`
- **PHP Version**: 8.3+
- **WordPress Version**: 6.0+

## Commands

### Testing (requires wp-env running)

```bash
npm start                    # Start wp-env Docker environment
npm test                     # Run PHPUnit tests
npm run test-php-multisite   # Run multisite tests
npm stop                     # Stop wp-env
```

### Linting & Static Analysis

```bash
composer phpcs      # Check coding standards
composer phpcbf     # Auto-fix coding standard issues
composer phpstan    # Run static analysis (max level)
composer test       # Run tests, same as 'npm test'
```

## Architecture

### Database Layer (`includes/Database/`)

ORM-like abstraction over WordPress posts with change tracking (unit-of-work pattern):

- **PostModel**: Abstract base class for custom post type models. Extend and implement `get_post_type()` and `configure_fields()`.
- **PostFields**: Manages field definitions and change tracking. Fields are registered via `column()`, `meta()`, `acf()`, or `taxonomy()` methods. The `add()` method is protected — always use the specific registration methods.
  - `column` - WP post table columns (post_title, post_content, etc.)
  - `meta` - Post meta via get/update_post_meta (supports single and multi-value)
  - `acf_meta` - ACF fields via get/update_field (supports custom store keys)
  - `taxonomy` - Taxonomy terms via wp_get/set_post_terms (supports single and multi-value, returns names)
- **PostQueryBuilder**: Fluent wrapper around WP_Query with type-safe methods. Uses `johnbillion/args` for typed query arguments. Generic `@template T of PostModel` enables typed results.
- **PostQueryResult**: Wraps WP_Query results. `records()` returns a Laravel Collection, `loop()` returns a memory-efficient Generator.

Example model definition:

```php
class Movie extends PostModel {
    protected function configure_fields( PostFields $fields ): PostFields {
        $fields->column( 'title', 'post_title' );
        $fields->meta( 'rating' );
        $fields->acf( 'poster_image' );
        $fields->taxonomy( 'genres', store_key: 'genre' );
        return $fields;
    }

    public static function get_post_type(): string {
        return 'movie';
    }
}
```

Example query usage:

```php
$posts = Post::query()
    ->published()
    ->latest()
    ->posts_per_page(10)
    ->fetch()
    ->records();
```

### Application Container (`includes/Application.php`)

Laravel-compatible DI container implementing `Illuminate\Contracts\Foundation\Application`. Supports service providers, booting lifecycle, and facades. Integrates with WordPress via `wp_get_environment_type()` and WP-CLI detection.

### Support Utilities (`includes/Support/`)

- **ServiceProvider**: Abstract base extending Laravel's ServiceProvider with typed `$app` property (`Humanik\WP\Application`).
- **PostType**: Static helper for controlling duplicate slug behavior per post type (`allow_duplicate_names()` / `disallow_duplicate_names()`).
- **Stdin**: CLI input helpers — lazy line reading via `LazyCollection`, non-blocking content detection, and full content reading.

## Workflow

After making code changes, always run the following before considering the task complete:

1. `composer phpcbf` — auto-fix coding standard issues
2. `composer phpcs` — check coding standards
3. `composer phpstan` — run static analysis
4. `composer test` — run tests (requires wp-env running via `npm start`)
5. Update `AGENTS.md` if the changes affect architecture, commands, coding standards, or other documented details

## Coding Standards

- WordPress Coding Standards (WPCS) with modifications in `phpcs.xml`
- All source files must use `declare(strict_types=1)`
- Global prefix: `wp_common` or `Humanik\WP` namespace
- Text domain: `wp-common`
- PHPStan level: max

## Testing

- Tests run inside Docker via `wp-env` — not locally
- All tests extend `WP_UnitTestCase` with automatic database rollback between tests
- Use `self::factory()->post->create()` for test data
- ACF-dependent tests guard with `function_exists()` checks and `markTestSkipped()`
