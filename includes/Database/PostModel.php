<?php

declare(strict_types=1);

namespace Humanik\WP\Database;

use Humanik\WP\Database\Fields\PostFields;

/**
 * Abstract base class for post models.
 *
 * Provides a fluent interface for working with WordPress posts,
 * including field management, change tracking, and persistence.
 *
 * @property int|null $post_id The ID of the post, or null for new posts.
 */
abstract class PostModel {

	/**
	 * The post fields manager.
	 *
	 * @var  PostFields
	 */
	protected PostFields $fields;

	/**
	 * Constructor.
	 *
	 * @param  int|null  $post_id  The post ID, or null for new posts.
	 */
	final public function __construct( protected ?int $post_id = null ) {
		if ( ! \is_null( $this->post_id ) ) {
			\assert( \get_post_type( $this->post_id ) === static::get_post_type() );
		}

		$this->fields = $this->configure_fields( new PostFields( $this->post_id, static::get_post_type() ) );
	}

	/**
	 * Get the post type slug.
	 *
	 * @return string The post type.
	 */
	abstract public static function get_post_type(): string;

	/**
	 * Configure the model fields.
	 *
	 * Override this method in subclasses to define fields.
	 *
	 * @param  PostFields  $fields  The fields manager.
	 * @return PostFields The configured fields manager.
	 */
	protected function configure_fields( PostFields $fields ): PostFields {
		return $fields;
	}

	/**
	 * Create a query builder for this model.
	 *
	 * @phpstan-return PostQueryBuilder<static>
	 *
	 * @return PostQueryBuilder The query builder.
	 */
	public static function query(): PostQueryBuilder {
		return new PostQueryBuilder( static::class );
	}

	/**
	 * Create a model instance from a post ID.
	 *
	 * @param  int  $id  The post ID.
	 * @return static The model instance.
	 */
	public static function from_id( int $id ): static {
		return new static( $id );
	}

	/**
	 * Create a model instance from a post name (slug).
	 *
	 * @param  string  $name  The post name/slug.
	 * @return static The model instance.
	 *
	 * @throws \Illuminate\Support\ItemNotFoundException If post not found.
	 */
	public static function from_name( string $name ): static {
		return static::query()
			->post_name__in( [ $name ] )
			->fetch()
			->first();
	}

	/**
	 * Magic getter for field access.
	 *
	 * @param  string  $name  The field name.
	 * @return mixed The field value.
	 */
	public function __get( string $name ): mixed {
		if ( 'post_id' === $name ) {
			return $this->post_id;
		}

		if ( 'post_type' === $name ) {
			return static::get_post_type();
		}

		return $this->fields->get( $name );
	}

	/**
	 * Magic setter for field access.
	 *
	 * @param  string  $name   The field name.
	 * @param  mixed   $value  The field value.
	 */
	public function __set( string $name, mixed $value ): void {
		$this->fields->set( $name, $value );
	}

	/**
	 * Magic isset for field access.
	 *
	 * @param  string  $name  The field name.
	 * @return bool True if field is defined.
	 */
	public function __isset( string $name ): bool {
		if ( 'post_id' === $name || 'id' === $name ) {
			return true;
		}

		return $this->fields->has( $name );
	}

	/**
	 * Persist all pending changes to the database.
	 */
	public function save(): void {
		$this->fields->save();

		// Update post_id if this was a new post.
		if ( \is_null( $this->post_id ) ) {
			$this->post_id = $this->fields->get_last_insert_id();
		}
	}

	/**
	 * Check if there are unsaved changes.
	 *
	 * @return bool True if there are pending changes.
	 */
	public function is_dirty(): bool {
		return $this->fields->is_dirty();
	}
}
