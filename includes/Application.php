<?php

declare(strict_types=1);

namespace Humanik\WP\Support;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

class Application extends Container implements ApplicationContract {

	protected readonly string $basepath;

	/**
	 * Indicates if the application has "booted".
	 */
	protected bool $booted = false;

	/**
	 * The array of booting callbacks.
	 *
	 * @var array<callable>
	 */
	protected array $booting_callbacks = [];

	/**
	 * The array of booted callbacks.
	 *
	 * @var array<callable>
	 */
	protected array $booted_callbacks = [];

	/**
	 * The array of terminating callbacks.
	 *
	 * @var list<callable>
	 */
	protected array $terminating_callbacks = [];

	/**
	 * The names of the loaded service providers.
	 *
	 * @var array<class-string<\Illuminate\Support\ServiceProvider>,true>
	 */
	protected array $loaded_providers = [];

	/**
	 * All of the registered service providers.
	 *
	 * @var array<\Illuminate\Support\ServiceProvider>
	 */
	protected array $service_providers = [];

	/**
	 * The deferred services and their providers.
	 *
	 * @var array<string,string>
	 */
	protected array $deferred_services = [];

	public function __construct( public readonly string $entry ) {
		$this->basepath = \dirname( $entry );

		$this->instance( 'app', $this );
		$this->instance( Container::class, $this );
		$this->instance( ApplicationContract::class, $this );
	}

	public function version(): string {
		return '1.0.0';
	}

	/** {@inheritDoc} */
	public function basePath( $path = '' ): string {
		return \path_join( $this->basepath, $path );
	}

	/** {@inheritDoc} */
	public function bootstrapPath( $path = '' ): string {
		return \path_join( $this->basepath . '/bootstrap', $path );
	}

	/** {@inheritDoc} */
	public function configPath( $path = '' ): string {
		return \path_join( $this->basepath . '/config', $path );
	}

	/** {@inheritDoc} */
	public function databasePath( $path = '' ): string {
		return \path_join( $this->basepath . '/database', $path );
	}

	/** {@inheritDoc} */
	public function langPath( $path = '' ): string {
		return \path_join( $this->basepath . '/lang', $path );
	}

	/** {@inheritDoc} */
	public function publicPath( $path = '' ): string {
		return \path_join( $this->basepath . '/public', $path );
	}

	/** {@inheritDoc} */
	public function resourcePath( $path = '' ): string {
		return \path_join( $this->basepath . '/resources', $path );
	}

	/** {@inheritDoc} */
	public function storagePath( $path = '' ): string {
		return \path_join( $this->basepath . '/storage', $path );
	}

	/** {@inheritDoc}
	 *
	 * @param  string  ...$environments
	 */
	public function environment( ...$environments ) {
		$env = \wp_get_environment_type();

		return \in_array( $env, $environments, true );
	}

	public function runningInConsole(): bool {
		return \defined( 'WP_CLI' ) && \constant( 'WP_CLI' );
	}

	public function runningUnitTests(): bool {
		return false;
	}

	public function hasDebugModeEnabled(): bool {
		return false;
	}

	public function makeGlobal(): self {
		static::setInstance( $this );
		Facade::setFacadeApplication( $this );

		return $this;
	}

	/** {@inheritDoc} */
	public function maintenanceMode() {
		throw new \Exception( 'Not implemented' );
	}

	/** {@inheritDoc} */
	public function isDownForMaintenance(): bool {
		return false;
	}

	/** {@inheritDoc} */
	public function registerConfiguredProviders(): void {}

	/**
	 * Get the registered service provider instance if it exists.
	 */
	public function getProvider( ServiceProvider|string $provider ): ?ServiceProvider {
		return \array_values( $this->getProviders( $provider ) )[0] ?? null;
	}

	/** {@inheritDoc} */
	public function register( $provider, $force = false ): ServiceProvider {
		$registered = $this->getProvider( $provider );
		if ( $registered && ! $force ) {
			return $registered;
		}

		if ( \is_string( $provider ) ) {
			$provider = $this->resolveProvider( $provider );
		}

		$provider->register();

		$this->markAsRegistered( $provider );

		if ( $this->isBooted() ) {
			$this->bootProvider( $provider );
		}

		return $provider;
	}

	/** {@inheritdoc} */
	public function registerDeferredProvider( $provider, $service = null ) {
		if ( $service ) {
			unset( $this->deferred_services[ $service ] );
		}

		$instance = $this->resolveProvider( $provider );
		$this->register( $instance );

		if ( ! $this->isBooted() ) {
			$this->booting(
				function () use ( $instance ): void {
					$this->bootProvider( $instance );
				}
			);
		}
	}

	/** {@inheritdoc} */
	public function resolveProvider( $provider ) {
		return new $provider( $this ); // @phpstan-ignore-line
	}

	public function isBooted(): bool {
		return $this->booted;
	}

	/**
	 * @param  iterable<class-string<\Illuminate\Support\ServiceProvider>>  $providers
	 */
	public function withProviders( iterable $providers ): self {
		foreach ( $providers as $provider ) {
			$this->register( $provider );
		}

		return $this;
	}

	/** {@inheritdoc} */
	public function boot(): void {
		if ( $this->isBooted() ) {
			return;
		}

		// Once the application has booted we will also fire some "booted" callbacks
		// for any listeners that need to do work after this initial booting gets
		// finished. This is useful when ordering the boot-up processes we run.
		$this->fireAppCallbacks( $this->booting_callbacks );

		\array_walk(
			$this->service_providers,
			function ( $provider ): void {
				$this->bootProvider( $provider );
			}
		);

		$this->booted = true;

		$this->fireAppCallbacks( $this->booted_callbacks );
	}

	/** {@inheritdoc} */
	public function booting( $callback ) {
		$this->booting_callbacks[] = $callback;
	}

	/** {@inheritdoc} */
	public function booted( $callback ) {
		$this->booted_callbacks[] = $callback;

		if ( $this->isBooted() ) {
			$callback( $this );
		}
	}

	/**
	 * @param  array<class-string>  $bootstrappers
	 */
	public function bootstrapWith( array $bootstrappers ): void {}

	/** {@inheritdoc} */
	public function getLocale(): string {
		return \get_locale();
	}

	/** {@inheritdoc} */
	public function getNamespace(): string {
		return 'App\\';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array<\Illuminate\Support\ServiceProvider>
	 */
	public function getProviders( $provider ): array {
		$name = \is_string( $provider ) ? $provider : \get_class( $provider );

		/** @var array<\Illuminate\Support\ServiceProvider> */
		return Arr::where( $this->service_providers, static fn ( $value ): bool => $value instanceof $name );
	}

	/** {@inheritdoc} */
	public function hasBeenBootstrapped(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function loadDeferredProviders(): void {}

	/** {@inheritdoc} */
	public function setLocale( $locale ): void {
		\switch_to_locale( $locale );
	}

	/** {@inheritdoc} */
	public function shouldSkipMiddleware(): bool {
		return false;
	}

	/** {@inheritDoc} */
	public function terminating( $callback ) {
		$this->terminating_callbacks[] = $callback; // @phpstan-ignore assign.propertyType

		return $this;
	}

	/** {@inheritdoc} */
	public function terminate(): void {
		$index = 0;

		while ( $index < \count( $this->terminating_callbacks ) ) {
			$this->call( $this->terminating_callbacks[ $index ] );

			++$index;
		}
	}

	/**
	 * Mark the given provider as registered.
	 */
	protected function markAsRegistered( ServiceProvider $provider ): void {
		$this->service_providers[] = $provider;

		$this->loaded_providers[ \get_class( $provider ) ] = true;
	}

	/**
	 * Boot the given service provider.
	 */
	protected function bootProvider( ServiceProvider $provider ): void {
		$provider->callBootingCallbacks();

		if ( \method_exists( $provider, 'boot' ) ) {
			$this->call( [ $provider, 'boot' ] );
		}

		$provider->callBootedCallbacks();
	}

	/**
	 * Call the booting callbacks for the application.
	 *
	 * @param  array<callable>  $callbacks
	 */
	protected function fireAppCallbacks( array &$callbacks ): void {
		$index = 0;

		while ( $index < \count( $callbacks ) ) {
			$callbacks[ $index ]( $this );

			++$index;
		}
	}
}
