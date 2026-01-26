<?php

declare(strict_types=1);

namespace Humanik\WP\Tests\Database;

use Humanik\WP\Database\Post;
use WP_UnitTestCase;

/**
 * Tests for the Post class.
 */
class PostTest extends WP_UnitTestCase {

	/**
	 * Test that get_post_type returns 'post'.
	 */
	public function test_get_post_type_returns_post(): void {
		$this->assertSame( 'post', Post::get_post_type() );
	}

	/**
	 * Test that title field is configured.
	 */
	public function test_field_title_is_defined(): void {
		$post = new Post();

		$this->assertTrue( isset( $post->title ) );
	}

	/**
	 * Test that name field is configured.
	 */
	public function test_field_name_is_defined(): void {
		$post = new Post();

		$this->assertTrue( isset( $post->name ) );
	}

	/**
	 * Test that content field is configured.
	 */
	public function test_field_content_is_defined(): void {
		$post = new Post();

		$this->assertTrue( isset( $post->content ) );
	}

	/**
	 * Test that title field returns default for new post.
	 */
	public function test_title_returns_default_for_new_post(): void {
		$post = new Post();

		$this->assertSame( '', $post->title );
	}

	/**
	 * Test that title field loads from existing post.
	 */
	public function test_title_loads_from_existing_post(): void {
		$post_id = self::factory()->post->create(
			[
				'post_title'  => 'Test Title',
				'post_status' => 'publish',
			]
		);

		$post = new Post( $post_id );

		$this->assertSame( 'Test Title', $post->title );
	}

	/**
	 * Test that name field loads from existing post.
	 */
	public function test_name_loads_from_existing_post(): void {
		$post_id = self::factory()->post->create(
			[
				'post_name'   => 'test-slug',
				'post_status' => 'publish',
			]
		);

		$post = new Post( $post_id );

		$this->assertSame( 'test-slug', $post->name );
	}

	/**
	 * Test that content field loads from existing post.
	 */
	public function test_content_loads_from_existing_post(): void {
		$post_id = self::factory()->post->create(
			[
				'post_content' => 'Test content here.',
				'post_status'  => 'publish',
			]
		);

		$post = new Post( $post_id );

		$this->assertSame( 'Test content here.', $post->content );
	}
}
