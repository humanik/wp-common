<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests;

use Humanik\WP\Application;
use WP_UnitTestCase;

/**
 * Tests for the Application class.
 */
class ApplicationTest extends WP_UnitTestCase {

	public function test_url_returns_plugin_base_url(): void {
		$app = new Application( WP_PLUGIN_DIR . '/wp-common/wp-common.php' );

		$this->assertSame(
			WP_PLUGIN_URL . '/wp-common',
			$app->url()
		);
	}

	public function test_url_returns_plugin_url_with_path(): void {
		$app = new Application( WP_PLUGIN_DIR . '/wp-common/wp-common.php' );

		$this->assertSame(
			WP_PLUGIN_URL . '/wp-common/assets/style.css',
			$app->url( 'assets/style.css' )
		);
	}

	public function test_url_returns_plugin_base_url_with_empty_path(): void {
		$app = new Application( WP_PLUGIN_DIR . '/wp-common/wp-common.php' );

		$this->assertSame(
			WP_PLUGIN_URL . '/wp-common',
			$app->url( '' )
		);
	}

	public function test_url_returns_theme_base_url(): void {
		$app = new Application( get_theme_root() . '/my-theme/functions.php' );

		$this->assertSame(
			get_theme_root_uri() . '/my-theme',
			$app->url()
		);
	}

	public function test_url_returns_theme_url_with_path(): void {
		$app = new Application( get_theme_root() . '/my-theme/functions.php' );

		$this->assertSame(
			get_theme_root_uri() . '/my-theme/assets/style.css',
			$app->url( 'assets/style.css' )
		);
	}

	public function test_baseurl_returns_plugin_base_url(): void {
		$app = new Application( WP_PLUGIN_DIR . '/wp-common/wp-common.php' );

		$this->assertSame(
			WP_PLUGIN_URL . '/wp-common',
			$app->url()
		);
	}

	public function test_baseurl_returns_theme_base_url(): void {
		$app = new Application( get_theme_root() . '/my-theme/functions.php' );

		$this->assertSame(
			get_theme_root_uri() . '/my-theme',
			$app->url()
		);
	}
}
