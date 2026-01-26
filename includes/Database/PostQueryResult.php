<?php

namespace Humanik\WP\Database;

use Illuminate\Support\Collection;

/**
 * @template T of PostModel
 */
class PostQueryResult {

	/**
	 * @phpstan-param class-string<T> $model
	 */
	public function __construct( protected string $model, public readonly \WP_Query $wp_query ) {}

	/**
	 * @return T
	 */
	public function first(): PostModel {
		return $this->records()->firstOrFail();
	}

	/**
	 * @return Collection<int,T>
	 */
	public function records(): Collection {
		return \collect( $this->wp_query->posts )
			->map(
				fn( $post ) => new $this->model(
					$post instanceof \WP_Post ? $post->ID : (int) $post
				)
			);
	}

	/**
	 * @phpstan-return \Generator<int,T,void,void>
	 */
	public function loop(): \Generator {
		while ( $this->wp_query->have_posts() ) {
			$this->wp_query->the_post();

			yield new $this->model( get_the_ID() );
		}

		\wp_reset_postdata();
	}
}
