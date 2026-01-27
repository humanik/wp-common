# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WP Common is a PHP library providing common utilities for WordPress development. It includes a Laravel-compatible dependency injection container and an ORM-like database layer for WordPress posts.

- **Namespace**: `Humanik\WP`
- **PHP Version**: 8.3+
- **WordPress Version**: 6.0+

## Commands

### Testing (requires wp-env running)

```bash
npm start                    # Start wp-env Docker environment
npm run test-php             # Run PHPUnit tests
npm run test-php-multisite   # Run multisite tests
npm stop                     # Stop wp-env
```

### Linting & Static Analysis

```bash
composer phpcs      # Check coding standards
composer phpcbf     # Auto-fix coding standard issues
composer phpstan    # Run static analysis (max level)
composer lint       # Run both phpcs and phpstan
```

## Architecture

### Database Layer (`includes/Database/`)

ORM-like abstraction over WordPress posts with change tracking:

- **PostModel**: Abstract base class for custom post type models. Extend and implement `get_post_type()` and `configure_fields()`.
- **PostFields**: Manages field definitions and change tracking. Supports three storage types:
  - `column` - WP post table columns (post_title, post_content, etc.)
  - `meta` - Post meta via get/update_post_meta
  - `acf_meta` - ACF fields via get/update_field
- **PostQueryBuilder**: Fluent wrapper around WP_Query with type-safe methods. Uses `johnbillion/args` for typed query arguments.
- **PostQueryResult**: Wraps WP_Query results, returns Laravel Collections of model instances.

Example usage:

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

## Coding Standards

- WordPress Coding Standards (WPCS) with modifications in `phpcs.xml`
- Global prefix: `wp_common` or `Humanik\WP` namespace
- Text domain: `wp-common`
- PHPStan level: max
