<?php

declare(strict_types=1);

namespace Humanik\WP\PHPUnit\Tests\Database\Fields;

use Humanik\WP\Database\Fields\PostFields;
use WP_UnitTestCase;

/**
 * Tests for the PostFields class.
 */
class PostFieldsTest extends WP_UnitTestCase {

	/**
	 * Test that add registers a field definition.
	 */
	public function test_add_registers_field_definition(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$this->assertTrue( $fields->has( 'title' ) );
	}

	/**
	 * Test that has returns true for defined field.
	 */
	public function test_has_returns_true_for_defined_field(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$this->assertTrue( $fields->has( 'title' ) );
	}

	/**
	 * Test that has returns false for undefined field.
	 */
	public function test_has_returns_false_for_undefined_field(): void {
		$fields = new PostFields( null, 'post' );

		$this->assertFalse( $fields->has( 'unknown' ) );
	}

	/**
	 * Test that get returns default for new post.
	 */
	public function test_get_returns_default_for_new_post(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: 'Default Title',
		);

		$this->assertSame( 'Default Title', $fields->get( 'title' ) );
	}

	/**
	 * Test that get returns pending change.
	 */
	public function test_get_returns_pending_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$fields->set( 'title', 'Changed Title' );

		$this->assertSame( 'Changed Title', $fields->get( 'title' ) );
	}

	/**
	 * Test that get loads from database for existing post.
	 */
	public function test_get_loads_from_database(): void {
		$post_id = self::factory()->post->create(
			[
				'post_title'  => 'DB Title',
				'post_status' => 'publish',
			]
		);

		$fields = new PostFields( $post_id, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$this->assertSame( 'DB Title', $fields->get( 'title' ) );
	}

	/**
	 * Test that get throws for undefined field.
	 */
	public function test_get_throws_for_undefined_field(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( "Field 'unknown' is not defined." );

		$fields = new PostFields( null, 'post' );
		$fields->get( 'unknown' );
	}

	/**
	 * Test that set tracks column change.
	 */
	public function test_set_tracks_column_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$fields->set( 'title', 'New Title' );

		$this->assertTrue( $fields->is_dirty() );
		$this->assertSame( 'New Title', $fields->get( 'title' ) );
	}

	/**
	 * Test that set tracks meta change.
	 */
	public function test_set_tracks_meta_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'custom_field',
			store_key: 'custom_meta_key',
			store_type: 'meta',
			default: '',
		);

		$fields->set( 'custom_field', 'Meta Value' );

		$this->assertTrue( $fields->is_dirty() );
	}

	/**
	 * Test that set throws for undefined field.
	 */
	public function test_set_throws_for_undefined_field(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( "Field 'unknown' is not defined." );

		$fields = new PostFields( null, 'post' );
		$fields->set( 'unknown', 'value' );
	}

	/**
	 * Test that is_dirty returns false initially.
	 */
	public function test_is_dirty_false_initially(): void {
		$fields = new PostFields( null, 'post' );

		$this->assertFalse( $fields->is_dirty() );
	}

	/**
	 * Test that is_dirty returns true after column change.
	 */
	public function test_is_dirty_true_after_column_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$fields->set( 'title', 'Changed' );

		$this->assertTrue( $fields->is_dirty() );
	}

	/**
	 * Test that is_dirty returns true after meta change.
	 */
	public function test_is_dirty_true_after_meta_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'meta_field',
			store_key: 'meta_key',
			store_type: 'meta',
			default: '',
		);

		$fields->set( 'meta_field', 'Changed' );

		$this->assertTrue( $fields->is_dirty() );
	}

	/**
	 * Test that is_dirty returns false after save.
	 */
	public function test_is_dirty_false_after_save(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$fields->set( 'title', 'New Title' );
		$this->assertTrue( $fields->is_dirty() );

		$fields->save();
		$this->assertFalse( $fields->is_dirty() );
	}

	/**
	 * Test that save inserts new post.
	 */
	public function test_save_inserts_new_post(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$fields->set( 'title', 'Inserted Post' );
		$fields->save();

		$new_id = $fields->get_last_insert_id();

		$this->assertNotNull( $new_id );
		$this->assertSame( 'Inserted Post', \get_the_title( $new_id ) );
	}

	/**
	 * Test that save updates existing post.
	 */
	public function test_save_updates_existing_post(): void {
		$post_id = self::factory()->post->create(
			[
				'post_title'  => 'Original',
				'post_status' => 'publish',
			]
		);

		$fields = new PostFields( $post_id, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$fields->set( 'title', 'Updated Title' );
		$fields->save();

		$this->assertSame( 'Updated Title', \get_the_title( $post_id ) );
	}

	/**
	 * Test that save sets last_insert_id for new posts.
	 */
	public function test_save_sets_last_insert_id(): void {
		$fields = new PostFields( null, 'post' );
		$fields->add(
			name: 'title',
			store_key: 'post_title',
			store_type: 'column',
			default: '',
		);

		$this->assertNull( $fields->get_last_insert_id() );

		$fields->set( 'title', 'New Post' );
		$fields->save();

		$this->assertIsInt( $fields->get_last_insert_id() );
	}

	/**
	 * Test that save updates single meta.
	 */
	public function test_save_updates_single_meta(): void {
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$fields = new PostFields( $post_id, 'post' );
		$fields->add(
			name: 'rating',
			store_key: 'post_rating',
			store_type: 'meta',
			single: true,
			default: 0,
		);

		$fields->set( 'rating', 5 );
		$fields->save();

		$this->assertEquals( 5, \get_post_meta( $post_id, 'post_rating', true ) );
	}

	/**
	 * Test that save updates multi-value meta.
	 */
	public function test_save_updates_multi_meta(): void {
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$fields = new PostFields( $post_id, 'post' );
		$fields->add(
			name: 'tags',
			store_key: 'custom_tags',
			store_type: 'meta',
			single: false,
			default: [],
		);

		$fields->set( 'tags', [ 'tag1', 'tag2', 'tag3' ] );
		$fields->save();

		$meta_values = \get_post_meta( $post_id, 'custom_tags', false );

		$this->assertCount( 3, $meta_values );
		$this->assertContains( 'tag1', $meta_values );
		$this->assertContains( 'tag2', $meta_values );
		$this->assertContains( 'tag3', $meta_values );
	}

	/**
	 * Test that get returns meta from database.
	 */
	public function test_get_loads_meta_from_database(): void {
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		\update_post_meta( $post_id, 'custom_key', 'Stored Value' );

		$fields = new PostFields( $post_id, 'post' );
		$fields->add(
			name: 'custom',
			store_key: 'custom_key',
			store_type: 'meta',
			single: true,
			default: '',
		);

		$this->assertSame( 'Stored Value', $fields->get( 'custom' ) );
	}

	/**
	 * Test that multi-value meta replaces existing values on save.
	 */
	public function test_multi_meta_replaces_existing_values(): void {
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		// Add initial values.
		\add_post_meta( $post_id, 'items', 'old1' );
		\add_post_meta( $post_id, 'items', 'old2' );

		$fields = new PostFields( $post_id, 'post' );
		$fields->add(
			name: 'items',
			store_key: 'items',
			store_type: 'meta',
			single: false,
			default: [],
		);

		// Set new values.
		$fields->set( 'items', [ 'new1', 'new2', 'new3' ] );
		$fields->save();

		$meta_values = \get_post_meta( $post_id, 'items', false );

		$this->assertCount( 3, $meta_values );
		$this->assertNotContains( 'old1', $meta_values );
		$this->assertContains( 'new1', $meta_values );
	}

	/**
	 * Test that acf registers field with acf_meta store type.
	 */
	public function test_acf_registers_field_with_acf_meta_store_type(): void {
		$fields = new PostFields( null, 'post' );
		$fields->acf( 'hero_image' );

		$this->assertTrue( $fields->has( 'hero_image' ) );
	}

	/**
	 * Test that acf uses name as store_key by default.
	 */
	public function test_acf_uses_name_as_store_key_by_default(): void {
		$fields = new PostFields( null, 'post' );
		$fields->acf( 'hero_image', 'default_value' );

		$fields->set( 'hero_image', 'test_value' );

		$this->assertSame( 'test_value', $fields->get( 'hero_image' ) );
	}

	/**
	 * Test that acf uses custom store_key when provided.
	 */
	public function test_acf_uses_custom_store_key_when_provided(): void {
		$fields = new PostFields( null, 'post' );
		$fields->acf( 'hero_image', '', 'field_hero_image' );

		$this->assertTrue( $fields->has( 'hero_image' ) );
	}

	/**
	 * Test that acf field returns default for new post.
	 */
	public function test_acf_returns_default_for_new_post(): void {
		$fields = new PostFields( null, 'post' );
		$fields->acf( 'hero_image', 'default_image.jpg' );

		$this->assertSame( 'default_image.jpg', $fields->get( 'hero_image' ) );
	}

	/**
	 * Test that get returns pending ACF change.
	 */
	public function test_get_returns_pending_acf_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->acf( 'hero_image', '' );

		$fields->set( 'hero_image', 'new_image.jpg' );

		$this->assertSame( 'new_image.jpg', $fields->get( 'hero_image' ) );
	}

	/**
	 * Test that set tracks ACF change.
	 */
	public function test_set_tracks_acf_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->acf( 'hero_image', '' );

		$fields->set( 'hero_image', 'new_image.jpg' );

		$this->assertTrue( $fields->is_dirty() );
		$this->assertSame( 'new_image.jpg', $fields->get( 'hero_image' ) );
	}

	/**
	 * Test that is_dirty returns true after ACF change.
	 */
	public function test_is_dirty_true_after_acf_change(): void {
		$fields = new PostFields( null, 'post' );
		$fields->acf( 'hero_image', '' );

		$fields->set( 'hero_image', 'changed_image.jpg' );

		$this->assertTrue( $fields->is_dirty() );
	}

	/**
	 * Test that is_dirty returns false after save with ACF changes.
	 */
	public function test_is_dirty_false_after_save_with_acf_changes(): void {
		if ( ! \function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not available.' );
		}

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$fields = new PostFields( $post_id, 'post' );
		$fields->acf( 'hero_image', '' );

		$fields->set( 'hero_image', 'new_image.jpg' );
		$this->assertTrue( $fields->is_dirty() );

		$fields->save();
		$this->assertFalse( $fields->is_dirty() );
	}

	/**
	 * Test that save updates ACF field.
	 */
	public function test_save_updates_acf_field(): void {
		if ( ! \function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not available.' );
		}

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$fields = new PostFields( $post_id, 'post' );
		$fields->acf( 'hero_image', '' );

		$fields->set( 'hero_image', 'saved_image.jpg' );
		$fields->save();

		$this->assertSame( 'saved_image.jpg', \get_field( 'hero_image', $post_id ) );
	}

	/**
	 * Test that get loads ACF field from database.
	 */
	public function test_get_loads_acf_field_from_database(): void {
		if ( ! \function_exists( 'get_field' ) || ! \function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not available.' );
		}

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		\update_field( 'hero_image', 'stored_image.jpg', $post_id );

		$fields = new PostFields( $post_id, 'post' );
		$fields->acf( 'hero_image', '' );

		$this->assertSame( 'stored_image.jpg', $fields->get( 'hero_image' ) );
	}

	/**
	 * Test that acf field with custom store_key saves correctly.
	 */
	public function test_acf_with_custom_store_key_saves_correctly(): void {
		if ( ! \function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not available.' );
		}

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$fields = new PostFields( $post_id, 'post' );
		$fields->acf( 'hero', '', 'hero_image_field' );

		$fields->set( 'hero', 'custom_key_image.jpg' );
		$fields->save();

		$this->assertSame( 'custom_key_image.jpg', \get_field( 'hero_image_field', $post_id ) );
	}

	/**
	 * Test that acf field with custom store_key loads correctly.
	 */
	public function test_acf_with_custom_store_key_loads_correctly(): void {
		if ( ! \function_exists( 'get_field' ) || ! \function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not available.' );
		}

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		\update_field( 'hero_image_field', 'loaded_image.jpg', $post_id );

		$fields = new PostFields( $post_id, 'post' );
		$fields->acf( 'hero', '', 'hero_image_field' );

		$this->assertSame( 'loaded_image.jpg', $fields->get( 'hero' ) );
	}

	/**
	 * Test that acf returns default when database value is empty.
	 */
	public function test_acf_returns_default_when_database_value_is_empty(): void {
		if ( ! \function_exists( 'get_field' ) || ! \function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not available.' );
		}

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		\update_field( 'hero_image', '', $post_id );

		$fields = new PostFields( $post_id, 'post' );
		$fields->acf( 'hero_image', 'fallback_image.jpg' );

		$this->assertSame( 'fallback_image.jpg', $fields->get( 'hero_image' ) );
	}

	/**
	 * Test that acf returns default when database value is empty array.
	 */
	public function test_acf_returns_default_when_database_value_is_empty_array(): void {
		if ( ! \function_exists( 'get_field' ) || ! \function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not available.' );
		}

		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		\update_field( 'gallery', [], $post_id );

		$fields = new PostFields( $post_id, 'post' );
		$fields->acf( 'gallery', [ 'default.jpg' ] );

		$this->assertSame( [ 'default.jpg' ], $fields->get( 'gallery' ) );
	}
}
