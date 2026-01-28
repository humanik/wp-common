<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Database;

use Humanik\WP\Database\Post;
use Humanik\WP\Database\PostQueryBuilder;
use Illuminate\Support\ItemNotFoundException;
use WP_UnitTestCase;

/**
 * Tests for the PostModel abstract class via the Post implementation.
 */
class PostModelTest extends WP_UnitTestCase {

	/**
	 * Test that from_id creates model with specified post ID.
	 */
	public function test_from_id_creates_model_with_post_id(): void {
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$post = Post::from_id( $post_id );

		$this->assertSame( $post_id, $post->post_id );
	}

	/**
	 * Test that from_id loads existing post data.
	 */
	public function test_from_id_loads_existing_post(): void {
		$post_id = self::factory()->post->create(
			[
				'post_title'  => 'From ID Test',
				'post_status' => 'publish',
			]
		);

		$post = Post::from_id( $post_id );

		$this->assertSame( 'From ID Test', $post->title );
	}

	/**
	 * Test that from_name finds post by slug.
	 */
	public function test_from_name_finds_post_by_slug(): void {
		$post_id = self::factory()->post->create(
			[
				'post_name'   => 'unique-test-slug',
				'post_status' => 'publish',
			]
		);

		$post = Post::from_name( 'unique-test-slug' );

		$this->assertSame( $post_id, $post->post_id );
	}

	/**
	 * Test that from_name throws when post not found.
	 */
	public function test_from_name_throws_when_not_found(): void {
		$this->expectException( ItemNotFoundException::class );

		Post::from_name( 'non-existent-slug-12345' );
	}

	/**
	 * Test that query returns PostQueryBuilder.
	 */
	public function test_query_returns_post_query_builder(): void {
		$builder = Post::query();

		$this->assertInstanceOf( PostQueryBuilder::class, $builder );
	}

	/**
	 * Test that get_post_id returns null for new post.
	 */
	public function test_get_post_id_returns_null_for_new_post(): void {
		$post = new Post();

		$this->assertNull( $post->post_id );
	}

	/**
	 * Test that get_post_id returns ID for existing post.
	 */
	public function test_get_post_id_returns_id_for_existing_post(): void {
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$post = new Post( $post_id );

		$this->assertSame( $post_id, $post->post_id );
	}

	/**
	 * Test that __get returns field value.
	 */
	public function test_get_returns_field_value(): void {
		$post_id = self::factory()->post->create(
			[
				'post_title'  => 'Magic Getter Test',
				'post_status' => 'publish',
			]
		);

		$post = new Post( $post_id );

		$this->assertSame( 'Magic Getter Test', $post->title );
	}

	/**
	 * Test that __set tracks field change.
	 */
	public function test_set_tracks_field_change(): void {
		$post        = new Post();
		$post->title = 'New Title';

		$this->assertSame( 'New Title', $post->title );
	}

	/**
	 * Test that __isset returns true for defined field.
	 */
	public function test_isset_returns_true_for_defined_field(): void {
		$post = new Post();

		$this->assertTrue( isset( $post->title ) );
	}

	/**
	 * Test that __isset returns false for undefined field.
	 */
	public function test_isset_returns_false_for_undefined_field(): void {
		$post = new Post();

		$this->assertFalse( isset( $post->unknown_field ) );
	}

	/**
	 * Test that __get throws for undefined field.
	 */
	public function test_get_throws_for_undefined_field(): void {
		$this->expectException( \InvalidArgumentException::class );

		$post = new Post();
		$_    = $post->unknown_field;
	}

	/**
	 * Test that save creates new post.
	 */
	public function test_save_creates_new_post(): void {
		$post        = new Post();
		$post->title = 'New Post Title';

		$post->save();

		$this->assertNotNull( $post->post_id );
		$this->assertSame( 'New Post Title', \get_the_title( $post->post_id ) );
	}

	/**
	 * Test that save updates existing post.
	 */
	public function test_save_updates_existing_post(): void {
		$post_id = self::factory()->post->create(
			[
				'post_title'  => 'Original Title',
				'post_status' => 'publish',
			]
		);

		$post        = new Post( $post_id );
		$post->title = 'Updated Title';
		$post->save();

		$this->assertSame( 'Updated Title', $post->title );
		$this->assertSame( 'Updated Title', \get_the_title( $post_id ) );
	}

	/**
	 * Test that save updates post_id after insert.
	 */
	public function test_save_updates_post_id_after_insert(): void {
		$post        = new Post();
		$post->title = 'Insert Test';

		$this->assertNull( $post->post_id );

		$post->save();

		$this->assertIsInt( $post->post_id );
		$this->assertGreaterThan( 0, $post->post_id );
	}

	/**
	 * Test that is_dirty returns false for new model without changes.
	 */
	public function test_is_dirty_false_for_new_model(): void {
		$post = new Post();

		$this->assertFalse( $post->is_dirty() );
	}

	/**
	 * Test that is_dirty returns true after set.
	 */
	public function test_is_dirty_true_after_set(): void {
		$post        = new Post();
		$post->title = 'Changed';

		$this->assertTrue( $post->is_dirty() );
	}

	/**
	 * Test that is_dirty returns false after save.
	 */
	public function test_is_dirty_false_after_save(): void {
		$post        = new Post();
		$post->title = 'New Title';

		$this->assertTrue( $post->is_dirty() );

		$post->save();

		$this->assertFalse( $post->is_dirty() );
	}
}
