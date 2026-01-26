<?php

/**
 * PostQueryBuilder test case.
 */

namespace Humanik\WP\PHPUnit\Tests\Database;

use Humanik\WP\Database\Post;
use Humanik\WP\Database\PostQueryBuilder;
use WP_UnitTestCase;

/**
 * Tests for PostQueryBuilder class.
 */
class PostQueryBuilderTest extends WP_UnitTestCase {

	/**
	 * Test that constructor sets post_type from model.
	 */
	public function test_constructor_sets_post_type(): void {
		$builder = new PostQueryBuilder( Post::class );
		$result  = $builder->fetch();

		$this->assertSame( 'post', $result->wp_query->query_vars['post_type'] );
	}

	/**
	 * Test fluent interface returns same instance.
	 */
	public function test_fluent_interface(): void {
		$builder = new PostQueryBuilder( Post::class );

		$this->assertSame( $builder, $builder->author( 1 ) );
		$this->assertSame( $builder, $builder->cat( 1 ) );
		$this->assertSame( $builder, $builder->tag( 'test' ) );
		$this->assertSame( $builder, $builder->search( 'search' ) );
		$this->assertSame( $builder, $builder->posts_per_page( 10 ) );
		$this->assertSame( $builder, $builder->order( 'DESC' ) );
		$this->assertSame( $builder, $builder->published() );
	}

	/**
	 * Test author parameter methods.
	 */
	public function test_author_parameters(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->author( 5 );
		$result = $builder->fetch();

		$this->assertSame( '5', $result->wp_query->query_vars['author'] );
	}

	/**
	 * Test author_name parameter.
	 */
	public function test_author_name_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->author_name( 'admin' );
		$result = $builder->fetch();

		$this->assertSame( 'admin', $result->wp_query->query_vars['author_name'] );
	}

	/**
	 * Test author__in parameter.
	 */
	public function test_author__in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->author__in( [ 1, 2, 3 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 1, 2, 3 ], $result->wp_query->query_vars['author__in'] );
	}

	/**
	 * Test author__not_in parameter.
	 */
	public function test_author__not_in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->author__not_in( [ 4, 5 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 4, 5 ], $result->wp_query->query_vars['author__not_in'] );
	}

	/**
	 * Test category parameter methods.
	 */
	public function test_cat_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->cat( 10 );
		$result = $builder->fetch();

		$this->assertSame( '10', $result->wp_query->query_vars['cat'] );
	}

	/**
	 * Test category_name parameter.
	 */
	public function test_category_name_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->category_name( 'news' );
		$result = $builder->fetch();

		$this->assertSame( 'news', $result->wp_query->query_vars['category_name'] );
	}

	/**
	 * Test category__and parameter.
	 */
	public function test_category__and_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->category__and( [ 1, 2 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 1, 2 ], $result->wp_query->query_vars['category__and'] );
	}

	/**
	 * Test category__in parameter.
	 */
	public function test_category__in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->category__in( [ 3, 4, 5 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 3, 4, 5 ], $result->wp_query->query_vars['category__in'] );
	}

	/**
	 * Test category__not_in parameter.
	 */
	public function test_category__not_in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->category__not_in( [ 6, 7 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 6, 7 ], $result->wp_query->query_vars['category__not_in'] );
	}

	/**
	 * Test tag parameter.
	 */
	public function test_tag_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->tag( 'featured' );
		$result = $builder->fetch();

		$this->assertSame( 'featured', $result->wp_query->query_vars['tag'] );
	}

	/**
	 * Test tag_id parameter.
	 */
	public function test_tag_id_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->tag_id( 15 );
		$result = $builder->fetch();

		$this->assertSame( 15, $result->wp_query->query_vars['tag_id'] );
	}

	/**
	 * Test tag__and parameter.
	 */
	public function test_tag__and_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->tag__and( [ 1, 2 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 1, 2 ], $result->wp_query->query_vars['tag__and'] );
	}

	/**
	 * Test tag__in parameter.
	 */
	public function test_tag__in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->tag__in( [ 3, 4 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 3, 4 ], $result->wp_query->query_vars['tag__in'] );
	}

	/**
	 * Test tag__not_in parameter.
	 */
	public function test_tag__not_in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->tag__not_in( [ 5, 6 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 5, 6 ], $result->wp_query->query_vars['tag__not_in'] );
	}

	/**
	 * Test tag_slug__and parameter.
	 */
	public function test_tag_slug__and_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->tag_slug__and( [ 'tag1', 'tag2' ] );
		$result = $builder->fetch();

		$this->assertSame( [ 'tag1', 'tag2' ], $result->wp_query->query_vars['tag_slug__and'] );
	}

	/**
	 * Test tag_slug__in parameter.
	 */
	public function test_tag_slug__in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->tag_slug__in( [ 'tag3', 'tag4' ] );
		$result = $builder->fetch();

		$this->assertSame( [ 'tag3', 'tag4' ], $result->wp_query->query_vars['tag_slug__in'] );
	}

	/**
	 * Test search parameter.
	 */
	public function test_search_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->search( 'test query' );
		$result = $builder->fetch();

		$this->assertSame( 'test query', $result->wp_query->query_vars['s'] );
	}

	/**
	 * Test post_title parameter.
	 */
	public function test_post_title_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_title( 'My Post Title' );
		$result = $builder->fetch();

		$this->assertSame( 'My Post Title', $result->wp_query->query_vars['title'] );
	}

	/**
	 * Test post_parent parameter.
	 */
	public function test_post_parent_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_parent( 50 );
		$result = $builder->fetch();

		$this->assertSame( 50, $result->wp_query->query_vars['post_parent'] );
	}

	/**
	 * Test post_parent__in parameter.
	 */
	public function test_post_parent__in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_parent__in( [ 10, 20 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 10, 20 ], $result->wp_query->query_vars['post_parent__in'] );
	}

	/**
	 * Test post_parent__not_in parameter.
	 */
	public function test_post_parent__not_in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_parent__not_in( [ 30, 40 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 30, 40 ], $result->wp_query->query_vars['post_parent__not_in'] );
	}

	/**
	 * Test post__in parameter.
	 */
	public function test_post__in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post__in( [ 1, 2, 3 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 1, 2, 3 ], $result->wp_query->query_vars['post__in'] );
	}

	/**
	 * Test post__not_in parameter.
	 */
	public function test_post__not_in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post__not_in( [ 4, 5, 6 ] );
		$result = $builder->fetch();

		$this->assertSame( [ 4, 5, 6 ], $result->wp_query->query_vars['post__not_in'] );
	}

	/**
	 * Test post_name__in parameter.
	 */
	public function test_post_name__in_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_name__in( [ 'slug1', 'slug2' ] );
		$result = $builder->fetch();

		$this->assertSame( [ 'slug1', 'slug2' ], $result->wp_query->query_vars['post_name__in'] );
	}

	/**
	 * Test page_id parameter.
	 */
	public function test_page_id_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->page_id( 100 );
		$result = $builder->fetch();

		$this->assertSame( 100, $result->wp_query->query_vars['page_id'] );
	}

	/**
	 * Test pagename parameter.
	 */
	public function test_pagename_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->pagename( 'about-us' );
		$result = $builder->fetch();

		$this->assertSame( 'about-us', $result->wp_query->query_vars['pagename'] );
	}

	/**
	 * Test post_status parameter with string.
	 */
	public function test_post_status_string_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_status( 'draft' );
		$result = $builder->fetch();

		$this->assertSame( 'draft', $result->wp_query->query_vars['post_status'] );
	}

	/**
	 * Test post_status parameter with array.
	 */
	public function test_post_status_array_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_status( [ 'draft', 'publish' ] );
		$result = $builder->fetch();

		$this->assertSame( [ 'draft', 'publish' ], $result->wp_query->query_vars['post_status'] );
	}

	/**
	 * Test comment_status parameter.
	 */
	public function test_comment_status_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->comment_status( 'open' );
		$result = $builder->fetch();

		$this->assertSame( 'open', $result->wp_query->query_vars['comment_status'] );
	}

	/**
	 * Test ping_status parameter.
	 */
	public function test_ping_status_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->ping_status( 'closed' );
		$result = $builder->fetch();

		$this->assertSame( 'closed', $result->wp_query->query_vars['ping_status'] );
	}

	/**
	 * Test posts_per_page parameter.
	 */
	public function test_posts_per_page_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->posts_per_page( 25 );
		$result = $builder->fetch();

		$this->assertSame( 25, $result->wp_query->query_vars['posts_per_page'] );
	}

	/**
	 * Test posts_per_page with -1 for all posts.
	 */
	public function test_posts_per_page_all(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->posts_per_page( -1 );
		$result = $builder->fetch();

		$this->assertSame( -1, $result->wp_query->query_vars['posts_per_page'] );
	}

	/**
	 * Test paged parameter.
	 */
	public function test_paged_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->paged( 3 );
		$result = $builder->fetch();

		$this->assertSame( 3, $result->wp_query->query_vars['paged'] );
	}

	/**
	 * Test offset parameter.
	 */
	public function test_offset_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->offset( 10 );
		$result = $builder->fetch();

		$this->assertSame( 10, $result->wp_query->query_vars['offset'] );
	}

	/**
	 * Test nopaging parameter.
	 */
	public function test_nopaging_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->nopaging();
		$result = $builder->fetch();

		$this->assertTrue( $result->wp_query->query_vars['nopaging'] );
	}

	/**
	 * Test nopaging parameter with false.
	 */
	public function test_nopaging_false_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->nopaging( false );
		$result = $builder->fetch();

		$this->assertFalse( $result->wp_query->query_vars['nopaging'] );
	}

	/**
	 * Test order parameter.
	 */
	public function test_order_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->order( 'ASC' );
		$result = $builder->fetch();

		$this->assertSame( 'ASC', $result->wp_query->query_vars['order'] );
	}

	/**
	 * Test orderby parameter with string.
	 */
	public function test_orderby_string_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->orderby( 'title' );
		$result = $builder->fetch();

		$this->assertSame( 'title', $result->wp_query->query_vars['orderby'] );
	}

	/**
	 * Test orderby parameter with array.
	 */
	public function test_orderby_array_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->orderby( [ 'title', 'date' ] );
		$result = $builder->fetch();

		$this->assertSame( [ 'title', 'date' ], $result->wp_query->query_vars['orderby'] );
	}

	/**
	 * Test year parameter.
	 */
	public function test_year_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->year( 2024 );
		$result = $builder->fetch();

		$this->assertSame( 2024, $result->wp_query->query_vars['year'] );
	}

	/**
	 * Test monthnum parameter.
	 */
	public function test_monthnum_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->monthnum( 6 );
		$result = $builder->fetch();

		$this->assertSame( 6, $result->wp_query->query_vars['monthnum'] );
	}

	/**
	 * Test week parameter.
	 */
	public function test_week_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->week( 25 );
		$result = $builder->fetch();

		$this->assertSame( 25, $result->wp_query->query_vars['w'] );
	}

	/**
	 * Test day parameter.
	 */
	public function test_day_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->day( 15 );
		$result = $builder->fetch();

		$this->assertSame( 15, $result->wp_query->query_vars['day'] );
	}

	/**
	 * Test hour parameter.
	 */
	public function test_hour_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->hour( 14 );
		$result = $builder->fetch();

		$this->assertSame( 14, $result->wp_query->query_vars['hour'] );
	}

	/**
	 * Test minute parameter.
	 */
	public function test_minute_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->minute( 30 );
		$result = $builder->fetch();

		$this->assertSame( 30, $result->wp_query->query_vars['minute'] );
	}

	/**
	 * Test second parameter.
	 */
	public function test_second_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->second( 45 );
		$result = $builder->fetch();

		$this->assertSame( 45, $result->wp_query->query_vars['second'] );
	}

	/**
	 * Test meta_compare parameter.
	 */
	public function test_meta_compare_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->meta_compare( '!=' );
		$result = $builder->fetch();

		$this->assertSame( '!=', $result->wp_query->query_vars['meta_compare'] );
	}

	/**
	 * Test permission parameter.
	 */
	public function test_permission_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->permission( 'readable' );
		$result = $builder->fetch();

		$this->assertSame( 'readable', $result->wp_query->query_vars['perm'] );
	}

	/**
	 * Test post_mime_type parameter.
	 */
	public function test_post_mime_type_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post_mime_type( 'image/jpeg' );
		$result = $builder->fetch();

		$this->assertSame( 'image/jpeg', $result->wp_query->query_vars['post_mime_type'] );
	}

	/**
	 * Test cache_results parameter.
	 */
	public function test_cache_results_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->cache_results( false );
		$result = $builder->fetch();

		$this->assertFalse( $result->wp_query->query_vars['cache_results'] );
	}

	/**
	 * Test update_post_meta_cache parameter.
	 */
	public function test_update_post_meta_cache_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->update_post_meta_cache( false );
		$result = $builder->fetch();

		$this->assertFalse( $result->wp_query->query_vars['update_post_meta_cache'] );
	}

	/**
	 * Test update_post_term_cache parameter.
	 */
	public function test_update_post_term_cache_parameter(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->update_post_term_cache( false );
		$result = $builder->fetch();

		$this->assertFalse( $result->wp_query->query_vars['update_post_term_cache'] );
	}

	/**
	 * Test published convenience method.
	 */
	public function test_published_convenience_method(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->published();
		$result = $builder->fetch();

		$this->assertSame( 'publish', $result->wp_query->query_vars['post_status'] );
	}

	/**
	 * Test draft convenience method.
	 */
	public function test_draft_convenience_method(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->draft();
		$result = $builder->fetch();

		$this->assertSame( 'draft', $result->wp_query->query_vars['post_status'] );
	}

	/**
	 * Test pending convenience method.
	 */
	public function test_pending_convenience_method(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->pending();
		$result = $builder->fetch();

		$this->assertSame( 'pending', $result->wp_query->query_vars['post_status'] );
	}

	/**
	 * Test trashed convenience method.
	 */
	public function test_trashed_convenience_method(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->trashed();
		$result = $builder->fetch();

		$this->assertSame( 'trash', $result->wp_query->query_vars['post_status'] );
	}

	/**
	 * Test latest convenience method.
	 */
	public function test_latest_convenience_method(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->latest();
		$result = $builder->fetch();

		$this->assertSame( 'date', $result->wp_query->query_vars['orderby'] );
		$this->assertSame( 'DESC', $result->wp_query->query_vars['order'] );
	}

	/**
	 * Test oldest convenience method.
	 */
	public function test_oldest_convenience_method(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->oldest();
		$result = $builder->fetch();

		$this->assertSame( 'date', $result->wp_query->query_vars['orderby'] );
		$this->assertSame( 'ASC', $result->wp_query->query_vars['order'] );
	}

	/**
	 * Test method chaining with multiple parameters.
	 */
	public function test_method_chaining(): void {
		$builder = new PostQueryBuilder( Post::class );
		$result  = $builder
			->published()
			->author( 1 )
			->cat( 5 )
			->posts_per_page( 10 )
			->order( 'DESC' )
			->orderby( 'date' )
			->fetch();

		$this->assertSame( 'publish', $result->wp_query->query_vars['post_status'] );
		$this->assertSame( '1', $result->wp_query->query_vars['author'] );
		$this->assertSame( '5', $result->wp_query->query_vars['cat'] );
		$this->assertSame( 10, $result->wp_query->query_vars['posts_per_page'] );
		$this->assertSame( 'DESC', $result->wp_query->query_vars['order'] );
		$this->assertSame( 'date', $result->wp_query->query_vars['orderby'] );
	}

	/**
	 * Test array parameters are reindexed with array_values.
	 */
	public function test_array_parameters_are_reindexed(): void {
		$builder = new PostQueryBuilder( Post::class );
		$builder->post__in(
			[
				'a' => 1,
				'b' => 2,
				'c' => 3,
			]
		);
		$result = $builder->fetch();

		$this->assertSame( [ 1, 2, 3 ], $result->wp_query->query_vars['post__in'] );
	}

	/**
	 * Test from_id creates model instance.
	 */
	public function test_from_id_creates_model(): void {
		$post_id = self::factory()->post->create();
		$builder = new PostQueryBuilder( Post::class );

		$model = $builder->from_id( $post_id );

		$this->assertInstanceOf( Post::class, $model );
		$this->assertSame( $post_id, $model->post_id );
	}

	/**
	 * Test integration: query returns correct posts.
	 */
	public function test_integration_query_returns_posts(): void {
		$post_id1 = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		$post_id2 = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		self::factory()->post->create( [ 'post_status' => 'draft' ] );

		$builder = new PostQueryBuilder( Post::class );
		$result  = $builder->published()->fetch();

		$this->assertGreaterThanOrEqual( 2, $result->wp_query->found_posts );
		$this->assertContains( $post_id1, wp_list_pluck( $result->wp_query->posts, 'ID' ) );
		$this->assertContains( $post_id2, wp_list_pluck( $result->wp_query->posts, 'ID' ) );
	}

	/**
	 * Test integration: query with author filter.
	 */
	public function test_integration_query_with_author(): void {
		$user_id = self::factory()->user->create();
		$post_id = self::factory()->post->create(
			[
				'post_author' => $user_id,
				'post_status' => 'publish',
			]
		);
		self::factory()->post->create( [ 'post_status' => 'publish' ] );

		$builder = new PostQueryBuilder( Post::class );
		$result  = $builder->published()->author( $user_id )->fetch();

		$this->assertSame( 1, $result->wp_query->found_posts );
		$this->assertSame( $post_id, $result->wp_query->posts[0]->ID );
	}

	/**
	 * Test integration: query with search.
	 */
	public function test_integration_query_with_search(): void {
		$post_id = self::factory()->post->create(
			[
				'post_title'  => 'Unique Search Term Here',
				'post_status' => 'publish',
			]
		);
		self::factory()->post->create(
			[
				'post_title'  => 'Different Title',
				'post_status' => 'publish',
			]
		);

		$builder = new PostQueryBuilder( Post::class );
		$result  = $builder->published()->search( 'Unique Search Term' )->fetch();

		$this->assertSame( 1, $result->wp_query->found_posts );
		$this->assertSame( $post_id, $result->wp_query->posts[0]->ID );
	}

	/**
	 * Test integration: query with ordering.
	 */
	public function test_integration_query_with_ordering(): void {
		$post_id1 = self::factory()->post->create(
			[
				'post_title'  => 'Alpha Post',
				'post_status' => 'publish',
			]
		);
		$post_id2 = self::factory()->post->create(
			[
				'post_title'  => 'Zulu Post',
				'post_status' => 'publish',
			]
		);

		$builder = new PostQueryBuilder( Post::class );
		$result  = $builder
			->published()
			->orderby( 'title' )
			->order( 'DESC' )
			->posts_per_page( 2 )
			->fetch();

		$ids = wp_list_pluck( $result->wp_query->posts, 'ID' );

		$this->assertSame( $post_id2, $ids[0] );
		$this->assertSame( $post_id1, $ids[1] );
	}
}
