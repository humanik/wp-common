<?php

declare(strict_types=1);

namespace Humanik\WP\Support;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

abstract class ServiceProvider extends IlluminateServiceProvider {
	/**
	 * The application instance.
	 *
	 * @var \Humanik\WP\Application
	 */
	protected $app;
}
