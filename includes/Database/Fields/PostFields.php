<?php

declare(strict_types=1);

namespace Humanik\WP\Database\Fields;

/**
 * Manages post fields with lazy loading and change tracking.
 *
 * @phpstan-type StoreType 'column'|'meta'|'acf_meta'
 * @phpstan-type FieldDefinition array{
 *     name: string,
 *     store_key: string,
 *     store_type: StoreType,
 *     single: bool,
 *     default: mixed
 * }
 */
class PostFields {

	/**
	 * Field definitions indexed by name.
	 *
	 * @var  array<string, FieldDefinition>
	 */
	private array $definitions = [];

	/**
	 * Pending column changes.
	 *
	 * @var  array<string, mixed>
	 */
	private array $column_changes = [];

	/**
	 * Pending meta changes.
	 *
	 * @var  array<string, mixed>
	 */
	private array $meta_changes = [];

	/**
	 * Pending ACF meta changes.
	 *
	 * @var  array<string, mixed>
	 */
	private array $acf_changes = [];

	/**
	 * ID of newly created post.
	 *
	 * @var  int|null
	 */
	private ?int $last_insert_id = null;

	/**
	 * Constructor.
	 */
	public function __construct( private ?int $post_id, private string $post_type ) {}

	/**
	 * Add a field definition.
	 *
	 * @phpstan-param StoreType $store_type
	 *
	 * @param  string  $name        The field name used for access.
	 * @param  string  $store_key   The actual storage key (column name or meta key).
	 * @param  string  $store_type  Storage type: 'column', 'meta', or 'acf_meta'.
	 * @param  bool    $single      For meta fields, whether to return single value.
	 * @param  mixed   $default     Default value when field is empty/null.
	 */
	public function add(
		string $name,
		string $store_key,
		string $store_type,
		bool $single = true,
		mixed $default = null
	): void {
		$this->definitions[ $name ] = [
			'name'       => $name,
			'store_key'  => $store_key,
			'store_type' => $store_type,
			'single'     => $single,
			'default'    => $default,
		];
	}

	public function column( string $name, string $store_key, mixed $default = '' ): void {
		$this->add(
			name: $name,
			store_key: $store_key,
			store_type: 'column',
			default: $default,
		);
	}

	public function meta( string $name, mixed $default = '', bool $single = true ): void {
		$this->add(
			name: $name,
			store_key: $name,
			store_type: 'meta',
			single: $single,
			default: $default,
		);
	}

	public function acf( string $name, mixed $default = '', ?string $store_key = null ): void {
		$this->add(
			name: $name,
			store_key: $store_key ?? $name,
			store_type: 'acf_meta',
			default: $default,
		);
	}

	/**
	 * Get a field value.
	 *
	 * @param  string  $name  The field name.
	 * @return mixed The field value.
	 *
	 * @throws \InvalidArgumentException If field is not defined.
	 */
	public function get( string $name ): mixed {
		if ( ! isset( $this->definitions[ $name ] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message, not HTML output.
			throw new \InvalidArgumentException( \sprintf( "Field '%s' is not defined.", $name ) );
		}

		$definition = $this->definitions[ $name ];
		$store_key  = $definition['store_key'];

		// Check pending changes first.
		if ( 'column' === $definition['store_type'] && \array_key_exists( $store_key, $this->column_changes ) ) {
			return $this->column_changes[ $store_key ];
		}

		if ( 'meta' === $definition['store_type'] && \array_key_exists( $store_key, $this->meta_changes ) ) {
			return $this->meta_changes[ $store_key ];
		}

		if ( 'acf_meta' === $definition['store_type'] && \array_key_exists( $store_key, $this->acf_changes ) ) {
			return $this->acf_changes[ $store_key ];
		}

		return $this->load_value( $definition );
	}

	/**
	 * Set a field value (tracks changes, does not persist).
	 *
	 * @param  string  $name   The field name.
	 * @param  mixed   $value  The new value.
	 *
	 * @throws \InvalidArgumentException If field is not defined.
	 */
	public function set( string $name, mixed $value ): void {
		if ( ! isset( $this->definitions[ $name ] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message, not HTML output.
			throw new \InvalidArgumentException( \sprintf( "Field '%s' is not defined.", $name ) );
		}

		$definition = $this->definitions[ $name ];
		$store_key  = $definition['store_key'];

		match ( $definition['store_type'] ) {
			'column'   => $this->column_changes[ $store_key ] = $value,
			'meta'     => $this->meta_changes[ $store_key ]   = $value,
			'acf_meta' => $this->acf_changes[ $store_key ]    = $value,
		};
	}

	/**
	 * Check if a field is defined.
	 *
	 * @param  string  $name  The field name.
	 * @return bool True if field is defined.
	 */
	public function has( string $name ): bool {
		return isset( $this->definitions[ $name ] );
	}

	/**
	 * Check if there are unsaved changes.
	 *
	 * @return bool True if there are pending changes.
	 */
	public function is_dirty(): bool {
		return ! empty( $this->column_changes )
			|| ! empty( $this->meta_changes )
			|| ! empty( $this->acf_changes );
	}

	/**
	 * Persist all pending changes to the database.
	 *
	 * @throws \RuntimeException If WordPress operation fails.
	 */
	public function save(): void {
		// Handle column changes (creates or updates the post).
		if ( ! empty( $this->column_changes ) ) {
			$this->post_id = $this->save_columns( $this->post_id );
		}

		// Handle meta changes.
		if ( ! empty( $this->meta_changes ) && ! \is_null( $this->post_id ) ) {
			$this->save_meta( $this->post_id );
		}

		// Handle ACF meta changes.
		if ( ! empty( $this->acf_changes ) && ! \is_null( $this->post_id ) ) {
			$this->save_acf_meta( $this->post_id );
		}

		// Clear all pending changes.
		$this->column_changes = [];
		$this->meta_changes   = [];
		$this->acf_changes    = [];
	}

	/**
	 * Get the last inserted post ID (after save of new post).
	 *
	 * @return int|null The new post ID or null if no insert occurred.
	 */
	public function get_last_insert_id(): ?int {
		return $this->last_insert_id;
	}

	/**
	 * Load a field value from the database.
	 *
	 * @phpstan-param FieldDefinition $definition
	 *
	 * @param  array  $definition  The field definition.
	 * @return mixed The loaded value or default.
	 */
	private function load_value( array $definition ): mixed {
		if ( \is_null( $this->post_id ) ) {
			return $definition['default'];
		}

		$value = match ( $definition['store_type'] ) {
			'column'   => \get_post_field( $definition['store_key'], $this->post_id, 'raw' ),
			'meta'     => \get_post_meta( $this->post_id, $definition['store_key'], $definition['single'] ),
			'acf_meta' => \get_field( $definition['store_key'], $this->post_id )
		};

		// Handle empty values - return default.
		if ( '' === $value || ( \is_array( $value ) && empty( $value ) ) ) {
			return $definition['default'];
		}

		return $value;
	}

	/**
	 * Save column changes via wp_insert_post or wp_update_post.
	 *
	 * @param  int|null  $post_id  The current post ID (null for new posts).
	 * @return int The post ID after save.
	 *
	 * @throws \RuntimeException If save fails.
	 */
	private function save_columns( ?int $post_id ): int {
		if ( \is_null( $post_id ) ) {
			// Insert new post.
			$postarr = \array_merge(
				[
					'post_type'   => $this->post_type,
					'post_status' => 'publish',
				],
				$this->column_changes
			);

			// @phpstan-ignore argument.type
			$result = \wp_insert_post( $postarr, true );

			if ( ! \is_wp_error( $result ) ) {
				$this->last_insert_id = $result;
			}
		} else {
			// Update existing post.
			$postarr = \array_merge(
				[ 'ID' => $post_id ],
				$this->column_changes
			);

			// @phpstan-ignore argument.type
			$result = \wp_update_post( $postarr, true );
		}

		if ( \is_wp_error( $result ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message, not HTML output.
			throw new \RuntimeException( $result->get_error_message() );
		}

		return $result;
	}

	/**
	 * Save meta field changes.
	 *
	 * @param  int  $post_id  The post ID.
	 */
	private function save_meta( int $post_id ): void {
		foreach ( $this->meta_changes as $key => $value ) {
			$definition = $this->find_definition_by_store_key( $key, 'meta' );

			if ( $definition && ! $definition['single'] ) {
				// For multi-value fields, delete existing and add new.
				\delete_post_meta( $post_id, $key );
				foreach ( (array) $value as $single_value ) {
					\add_post_meta( $post_id, $key, $single_value );
				}
			} else {
				\update_post_meta( $post_id, $key, $value );
			}
		}
	}

	/**
	 * Save ACF field changes.
	 *
	 * @param  int  $post_id  The post ID.
	 */
	private function save_acf_meta( int $post_id ): void {
		foreach ( $this->acf_changes as $key => $value ) {
			\update_field( $key, $value, $post_id );
		}
	}

	/**
	 * Find a field definition by its store key.
	 *
	 * @phpstan-param StoreType $store_type
	 *
	 * @param  string  $store_key   The storage key.
	 * @param  string  $store_type  The storage type.
	 * @return FieldDefinition|null The field definition or null.
	 */
	private function find_definition_by_store_key( string $store_key, string $store_type ): ?array {
		foreach ( $this->definitions as $definition ) {
			if ( $definition['store_key'] === $store_key && $definition['store_type'] === $store_type ) {
				return $definition;
			}
		}

		return null;
	}
}
