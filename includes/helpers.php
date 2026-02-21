<?php

namespace Humanik\WP;

use Illuminate\Container\Container;

/**
 * Helper function to resolve a class from the container.
 *
 * @template TClass of object
 *
 * @param  class-string<TClass> $class_name
 * @param  array<string,mixed>  $parameters
 * @return TClass
 */
function make( string $class_name, array $parameters = [] ) {
	return Container::getInstance()->make( $class_name, $parameters );
}
