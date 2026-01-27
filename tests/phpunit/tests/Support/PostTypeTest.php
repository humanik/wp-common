<?php

declare(strict_types=1);

namespace Humanik\WP\Tests\Support;

use Humanik\WP\Support\PostType;
use WP_UnitTestCase;

/**
 * Tests for the PostType class.
 */
class PostTypeTest extends WP_UnitTestCase {

	/**
	 * Clean up after each test.
	 */
	public function tear_down(): void {
		PostType::remove_allow_duplicate_names( 'post' );
		PostType::remove_allow_duplicate_names( 'page' );
		PostType::remove_disallow_duplicate_names( 'post' );
		PostType::remove_disallow_duplicate_names( 'page' );
		parent::tear_down();
	}

	/**
	 * Test that duplicate post names are allowed when enabled for a post type.
	 */
	public function test_allow_duplicate_names_permits_same_slug(): void {
		$post_id_1 = self::factory()->post->create(
			[
				'post_name'   => 'test-slug',
				'post_status' => 'publish',
			]
		);

		PostType::allow_duplicate_names( 'post' );

		$post_id_2 = self::factory()->post->create(
			[
				'post_name'   => 'test-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );

		$this->assertSame( 'test-slug', $post_1->post_name );
		$this->assertSame( 'test-slug', $post_2->post_name );
	}

	/**
	 * Test that duplicate post names are disallowed by default (WordPress appends suffix).
	 */
	public function test_default_behavior_appends_suffix_to_duplicates(): void {
		$post_id_1 = self::factory()->post->create(
			[
				'post_name'   => 'unique-slug',
				'post_status' => 'publish',
			]
		);

		$post_id_2 = self::factory()->post->create(
			[
				'post_name'   => 'unique-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );

		$this->assertSame( 'unique-slug', $post_1->post_name );
		$this->assertSame( 'unique-slug-2', $post_2->post_name );
	}

	/**
	 * Test that disallow_duplicate_names throws exception on duplicate slug.
	 */
	public function test_disallow_duplicate_names_throws_exception_on_duplicate(): void {
		self::factory()->post->create(
			[
				'post_name'   => 'protected-slug',
				'post_status' => 'publish',
			]
		);

		PostType::disallow_duplicate_names( 'post' );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'A post with the slug "protected-slug" already exists.' );

		self::factory()->post->create(
			[
				'post_name'   => 'protected-slug',
				'post_status' => 'publish',
			]
		);
	}

	/**
	 * Test that disallow_duplicate_names allows unique slugs.
	 */
	public function test_disallow_duplicate_names_allows_unique_slugs(): void {
		PostType::disallow_duplicate_names( 'post' );

		$post_id_1 = self::factory()->post->create(
			[
				'post_name'   => 'first-slug',
				'post_status' => 'publish',
			]
		);

		$post_id_2 = self::factory()->post->create(
			[
				'post_name'   => 'second-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );

		$this->assertSame( 'first-slug', $post_1->post_name );
		$this->assertSame( 'second-slug', $post_2->post_name );
	}

	/**
	 * Test that disallow_duplicate_names allows updating same post.
	 */
	public function test_disallow_duplicate_names_allows_updating_same_post(): void {
		PostType::disallow_duplicate_names( 'post' );

		$post_id = self::factory()->post->create(
			[
				'post_name'   => 'update-slug',
				'post_status' => 'publish',
			]
		);

		wp_update_post(
			[
				'ID'         => $post_id,
				'post_title' => 'Updated Title',
			]
		);

		$post = get_post( $post_id );

		$this->assertSame( 'update-slug', $post->post_name );
		$this->assertSame( 'Updated Title', $post->post_title );
	}

	/**
	 * Test that remove_allow_duplicate_names restores default behavior.
	 */
	public function test_remove_allow_duplicate_names_restores_default(): void {
		PostType::allow_duplicate_names( 'post' );
		PostType::remove_allow_duplicate_names( 'post' );

		$post_id_1 = self::factory()->post->create(
			[
				'post_name'   => 'another-slug',
				'post_status' => 'publish',
			]
		);

		$post_id_2 = self::factory()->post->create(
			[
				'post_name'   => 'another-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );

		$this->assertSame( 'another-slug', $post_1->post_name );
		$this->assertSame( 'another-slug-2', $post_2->post_name );
	}

	/**
	 * Test that calling allow_duplicate_names multiple times is safe.
	 */
	public function test_allow_duplicate_names_is_idempotent(): void {
		PostType::allow_duplicate_names( 'post' );
		PostType::allow_duplicate_names( 'post' );
		PostType::allow_duplicate_names( 'post' );

		$post_id_1 = self::factory()->post->create(
			[
				'post_name'   => 'idempotent-slug',
				'post_status' => 'publish',
			]
		);

		$post_id_2 = self::factory()->post->create(
			[
				'post_name'   => 'idempotent-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );

		$this->assertSame( 'idempotent-slug', $post_1->post_name );
		$this->assertSame( 'idempotent-slug', $post_2->post_name );
	}

	/**
	 * Test that calling disallow_duplicate_names multiple times is safe.
	 */
	public function test_disallow_duplicate_names_is_idempotent(): void {
		PostType::disallow_duplicate_names( 'post' );
		PostType::disallow_duplicate_names( 'post' );
		PostType::disallow_duplicate_names( 'post' );

		$this->assertTrue( true );
	}

	/**
	 * Test that calling remove methods without setup is safe.
	 */
	public function test_remove_methods_without_setup_is_safe(): void {
		PostType::remove_allow_duplicate_names( 'post' );
		PostType::remove_disallow_duplicate_names( 'post' );

		$this->assertTrue( true );
	}

	/**
	 * Test that allowing duplicates for one post type does not affect other post types.
	 */
	public function test_allow_duplicates_only_affects_specified_post_type(): void {
		PostType::allow_duplicate_names( 'post' );

		$post_id_1 = self::factory()->post->create(
			[
				'post_type'   => 'post',
				'post_name'   => 'shared-slug',
				'post_status' => 'publish',
			]
		);

		$post_id_2 = self::factory()->post->create(
			[
				'post_type'   => 'post',
				'post_name'   => 'shared-slug',
				'post_status' => 'publish',
			]
		);

		$page_id_1 = self::factory()->post->create(
			[
				'post_type'   => 'page',
				'post_name'   => 'page-slug',
				'post_status' => 'publish',
			]
		);

		$page_id_2 = self::factory()->post->create(
			[
				'post_type'   => 'page',
				'post_name'   => 'page-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );
		$page_1 = get_post( $page_id_1 );
		$page_2 = get_post( $page_id_2 );

		$this->assertSame( 'shared-slug', $post_1->post_name );
		$this->assertSame( 'shared-slug', $post_2->post_name );
		$this->assertSame( 'page-slug', $page_1->post_name );
		$this->assertSame( 'page-slug-2', $page_2->post_name );
	}

	/**
	 * Test that disallow duplicates only affects specified post type.
	 */
	public function test_disallow_duplicates_only_affects_specified_post_type(): void {
		PostType::disallow_duplicate_names( 'post' );

		$post_id_1 = self::factory()->post->create(
			[
				'post_type'   => 'post',
				'post_name'   => 'disallow-slug',
				'post_status' => 'publish',
			]
		);

		$page_id_1 = self::factory()->post->create(
			[
				'post_type'   => 'page',
				'post_name'   => 'page-disallow-slug',
				'post_status' => 'publish',
			]
		);

		$page_id_2 = self::factory()->post->create(
			[
				'post_type'   => 'page',
				'post_name'   => 'page-disallow-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$page_1 = get_post( $page_id_1 );
		$page_2 = get_post( $page_id_2 );

		$this->assertSame( 'disallow-slug', $post_1->post_name );
		$this->assertSame( 'page-disallow-slug', $page_1->post_name );
		$this->assertSame( 'page-disallow-slug-2', $page_2->post_name );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'A post with the slug "disallow-slug" already exists.' );
		self::factory()->post->create(
			[
				'post_type'   => 'post',
				'post_name'   => 'disallow-slug',
				'post_status' => 'publish',
			]
		);
	}

	/**
	 * Test that multiple post types can have duplicates allowed independently.
	 */
	public function test_allow_duplicates_for_multiple_post_types(): void {
		PostType::allow_duplicate_names( 'post' );
		PostType::allow_duplicate_names( 'page' );

		$post_id_1 = self::factory()->post->create(
			[
				'post_type'   => 'post',
				'post_name'   => 'multi-slug',
				'post_status' => 'publish',
			]
		);

		$post_id_2 = self::factory()->post->create(
			[
				'post_type'   => 'post',
				'post_name'   => 'multi-slug',
				'post_status' => 'publish',
			]
		);

		$page_id_1 = self::factory()->post->create(
			[
				'post_type'   => 'page',
				'post_name'   => 'multi-page-slug',
				'post_status' => 'publish',
			]
		);

		$page_id_2 = self::factory()->post->create(
			[
				'post_type'   => 'page',
				'post_name'   => 'multi-page-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );
		$page_1 = get_post( $page_id_1 );
		$page_2 = get_post( $page_id_2 );

		$this->assertSame( 'multi-slug', $post_1->post_name );
		$this->assertSame( 'multi-slug', $post_2->post_name );
		$this->assertSame( 'multi-page-slug', $page_1->post_name );
		$this->assertSame( 'multi-page-slug', $page_2->post_name );
	}

	/**
	 * Test that allow removes disallow filter and vice versa.
	 */
	public function test_allow_and_disallow_are_mutually_exclusive(): void {
		PostType::disallow_duplicate_names( 'post' );
		PostType::allow_duplicate_names( 'post' );

		$post_id_1 = self::factory()->post->create(
			[
				'post_name'   => 'exclusive-slug',
				'post_status' => 'publish',
			]
		);

		$post_id_2 = self::factory()->post->create(
			[
				'post_name'   => 'exclusive-slug',
				'post_status' => 'publish',
			]
		);

		$post_1 = get_post( $post_id_1 );
		$post_2 = get_post( $post_id_2 );

		$this->assertSame( 'exclusive-slug', $post_1->post_name );
		$this->assertSame( 'exclusive-slug', $post_2->post_name );
	}
}
