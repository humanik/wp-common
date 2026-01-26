<?php

namespace Humanik\WP\Database;

/**
 * @template T of PostModel
 *
 * @phpstan-type OrderBy 'none'|'ID'|'author'|'title'|'name'|'type'|'date'|'modified'|'parent'|'rand'|'comment_count'|'relevance'|'menu_order'|'meta_value'|'meta_value_num'|'post__in'|'post_name__in'|'post_parent__in'
 */
class PostQueryBuilder {

	protected readonly \Args\WP_Query $args;

	/**
	 * @phpstan-param class-string<T> $model
	 */
	public function __construct( protected string $model ) {
		$this->args            = new \Args\WP_Query();
		$this->args->post_type = $model::get_post_type();
	}

	/**
	 * Create a model instance from a post ID.
	 *
	 * @param  int  $id  The post ID.
	 * @return T The model instance.
	 */
	public function from_id( int $id ): PostModel {
		$model = $this->model;

		return $model::from_id( $id );
	}

	/**
	 * Filter by author ID.
	 *
	 * @param  int  $author_id  Author ID.
	 */
	public function author( int $author_id ): static {
		$this->args->author = $author_id;

		return $this;
	}

	/**
	 * Filter by author nicename.
	 *
	 * @param  string  $author_name  Author nicename (user_nicename).
	 */
	public function author_name( string $author_name ): static {
		$this->args->author_name = $author_name;

		return $this;
	}

	/**
	 * Filter to include posts by specific author IDs.
	 *
	 * @param  array<int>  $author_ids  Author IDs to include.
	 */
	public function author__in( array $author_ids ): static {
		$this->args->author__in = \array_values( $author_ids );

		return $this;
	}

	/**
	 * Filter to exclude posts by specific author IDs.
	 *
	 * @param  array<int>  $author_ids  Author IDs to exclude.
	 */
	public function author__not_in( array $author_ids ): static {
		$this->args->author__not_in = \array_values( $author_ids );

		return $this;
	}

	/**
	 * Filter by category ID (includes children).
	 *
	 * @param  int  $category_id  Category ID.
	 */
	public function cat( int $category_id ): static {
		$this->args->cat = $category_id;

		return $this;
	}

	/**
	 * Filter by category slug (includes children).
	 *
	 * @param  string  $category_name  Category slug.
	 */
	public function category_name( string $category_name ): static {
		$this->args->category_name = $category_name;

		return $this;
	}

	/**
	 * Filter by category IDs - posts must be in ALL specified categories.
	 *
	 * @param  array<int>  $category_ids  Category IDs (AND relationship).
	 */
	public function category__and( array $category_ids ): static {
		$this->args->category__and = \array_values( $category_ids );

		return $this;
	}

	/**
	 * Filter by category IDs - posts must be in ANY specified category.
	 *
	 * @param  array<int>  $category_ids  Category IDs (OR relationship).
	 */
	public function category__in( array $category_ids ): static {
		$this->args->category__in = \array_values( $category_ids );

		return $this;
	}

	/**
	 * Exclude posts in specified category IDs.
	 *
	 * @param  array<int>  $category_ids  Category IDs to exclude.
	 */
	public function category__not_in( array $category_ids ): static {
		$this->args->category__not_in = \array_values( $category_ids );

		return $this;
	}

	/**
	 * Filter by tag slug.
	 *
	 * @param  string  $tag  Tag slug.
	 */
	public function tag( string $tag ): static {
		$this->args->tag = $tag;

		return $this;
	}

	/**
	 * Filter by tag ID.
	 *
	 * @param  int  $tag_id  Tag ID.
	 */
	public function tag_id( int $tag_id ): static {
		$this->args->tag_id = $tag_id;

		return $this;
	}

	/**
	 * Filter by tag IDs - posts must have ALL specified tags.
	 *
	 * @param  array<int>  $tag_ids  Tag IDs (AND relationship).
	 */
	public function tag__and( array $tag_ids ): static {
		$this->args->tag__and = \array_values( $tag_ids );

		return $this;
	}

	/**
	 * Filter by tag IDs - posts must have ANY specified tag.
	 *
	 * @param  array<int>  $tag_ids  Tag IDs (OR relationship).
	 */
	public function tag__in( array $tag_ids ): static {
		$this->args->tag__in = \array_values( $tag_ids );

		return $this;
	}

	/**
	 * Exclude posts with specified tag IDs.
	 *
	 * @param  array<int>  $tag_ids  Tag IDs to exclude.
	 */
	public function tag__not_in( array $tag_ids ): static {
		$this->args->tag__not_in = \array_values( $tag_ids );

		return $this;
	}

	/**
	 * Filter by tag slugs - posts must have ALL specified tags.
	 *
	 * @param  array<string>  $tag_slugs  Tag slugs (AND relationship).
	 */
	public function tag_slug__and( array $tag_slugs ): static {
		$this->args->tag_slug__and = \array_values( $tag_slugs );

		return $this;
	}

	/**
	 * Filter by tag slugs - posts must have ANY specified tag.
	 *
	 * @param  array<string>  $tag_slugs  Tag slugs (OR relationship).
	 */
	public function tag_slug__in( array $tag_slugs ): static {
		$this->args->tag_slug__in = \array_values( $tag_slugs );

		return $this;
	}

	/**
	 * Search posts by keyword(s).
	 *
	 * @param  string  $search  Search keyword(s).
	 */
	public function search( string $search ): static {
		$this->args->s = $search;

		return $this;
	}

	public function post_title( string $title ): static {
		$this->args->title = $title;

		return $this;
	}

	public function post_parent( int $post_parent ): static {
		$this->args->post_parent = $post_parent;

		return $this;
	}

	/**
	 * @param  array<int>  $ids
	 */
	public function post_parent__in( array $ids ): static {
		$this->args->post_parent__in = \array_values( $ids );

		return $this;
	}

	/**
	 * @param  array<int>  $ids
	 */
	public function post_parent__not_in( array $ids ): static {
		$this->args->post_parent__not_in = \array_values( $ids );

		return $this;
	}

	/**
	 * Filter to include specific post IDs.
	 *
	 * @param  array<int>  $post_ids  Post IDs to include.
	 */
	public function post__in( array $post_ids ): static {
		$this->args->post__in = \array_values( $post_ids );

		return $this;
	}

	/**
	 * Filter to exclude specific post IDs.
	 *
	 * @param  array<int>  $post_ids  Post IDs to exclude.
	 */
	public function post__not_in( array $post_ids ): static {
		$this->args->post__not_in = \array_values( $post_ids );

		return $this;
	}

	/**
	 * Filter by post slugs.
	 *
	 * @param  array<string>  $slugs  Post slugs to include.
	 */
	public function post_name__in( array $slugs ): static {
		$this->args->post_name__in = \array_values( $slugs );

		return $this;
	}

	/**
	 * Filter by page ID.
	 *
	 * @param  int  $page_id  Page ID.
	 */
	public function page_id( int $page_id ): static {
		$this->args->page_id = $page_id;

		return $this;
	}

	/**
	 * Filter by page slug.
	 *
	 * @param  string  $pagename  Page slug.
	 */
	public function pagename( string $pagename ): static {
		$this->args->pagename = $pagename;

		return $this;
	}

	/**
	 * Filter by post status.
	 *
	 * @param  string|array<int,string>  $status  Post status or array of statuses.
	 */
	public function post_status( string|array $status ): static {
		$this->args->post_status = $status;

		return $this;
	}

	/**
	 * Filter by comment status.
	 *
	 * @phpstan-param 'open'|'closed' $status Comment status.
	 */
	public function comment_status( string $status ): static {
		$this->args->comment_status = $status;

		return $this;
	}

	/**
	 * Filter by ping status.
	 *
	 * @phpstan-param 'open'|'closed' $status Ping status.
	 */
	public function ping_status( string $status ): static {
		$this->args->ping_status = $status;

		return $this;
	}

	/**
	 * Set the number of posts per page.
	 *
	 * @phpstan-param -1|int<1,max> $count Number of posts (-1 for all).
	 */
	public function posts_per_page( int $count ): static {
		$this->args->posts_per_page = $count;

		return $this;
	}

	/**
	 * Set the page number.
	 *
	 * @param  int  $page  Page number.
	 */
	public function paged( int $page ): static {
		$this->args->paged = $page;

		return $this;
	}

	/**
	 * Set the offset for posts.
	 *
	 * @param  int  $offset  Number of posts to skip.
	 */
	public function offset( int $offset ): static {
		$this->args->offset = $offset;

		return $this;
	}

	/**
	 * Disable pagination (show all posts).
	 *
	 * @param  bool  $nopaging  Whether to disable pagination.
	 */
	public function nopaging( bool $nopaging = true ): static {
		$this->args->nopaging = $nopaging;

		return $this;
	}

	/**
	 * Set the sort order.
	 *
	 * @phpstan-param 'ASC'|'DESC' $order Sort order.
	 */
	public function order( string $order ): static {
		$this->args->order = $order;

		return $this;
	}

	/**
	 * Set the field(s) to order by.
	 *
	 * @phpstan-param OrderBy|list<OrderBy> $orderby Field or array of fields.
	 */
	public function orderby( string|array $orderby ): static {
		$this->args->orderby = $orderby;

		return $this;
	}

	/**
	 * Filter by year.
	 *
	 * @param  int  $year  Four-digit year.
	 */
	public function year( int $year ): static {
		$this->args->year = $year;

		return $this;
	}

	/**
	 * Filter by month.
	 *
	 * @phpstan-param int<1,12> $monthnum Month number (1-12).
	 */
	public function monthnum( int $monthnum ): static {
		$this->args->monthnum = $monthnum;

		return $this;
	}

	/**
	 * Filter by week number.
	 *
	 * @phpstan-param int<0,53> $week Week number (0-53).
	 */
	public function week( int $week ): static {
		$this->args->w = $week;

		return $this;
	}

	/**
	 * Filter by day of month.
	 *
	 * @phpstan-param int<1,31> $day Day (1-31).
	 */
	public function day( int $day ): static {
		$this->args->day = $day;

		return $this;
	}

	/**
	 * Filter by hour.
	 *
	 * @phpstan-param int<0,23> $hour Hour (0-23).
	 */
	public function hour( int $hour ): static {
		$this->args->hour = $hour;

		return $this;
	}

	/**
	 * Filter by minute.
	 *
	 * @phpstan-param int<0,59> $minute Minute (0-59).
	 */
	public function minute( int $minute ): static {
		$this->args->minute = $minute;

		return $this;
	}

	/**
	 * Filter by second.
	 *
	 * @phpstan-param int<0,59> $second Second (0-59).
	 */
	public function second( int $second ): static {
		$this->args->second = $second;

		return $this;
	}

	/**
	 * Set meta comparison operator.
	 *
	 * @phpstan-param '='|'!='|'>'|'>='|'<'|'<='|'LIKE'|'NOT LIKE'|'IN'|'NOT IN'|'BETWEEN'|'NOT BETWEEN'|'EXISTS'|'NOT EXISTS'|'REGEXP'|'NOT REGEXP'|'RLIKE' $compare Comparison operator.
	 */
	public function meta_compare( string $compare ): static {
		$this->args->meta_compare = $compare;

		return $this;
	}

	/**
	 * Set complex meta query.
	 */
	public function meta_query( \Args\MetaQuery\Query $query ): static {
		$this->args->meta_query = $query;

		return $this;
	}

	/**
	 * Add a meta query clause.
	 *
	 * @param  string  $key  Meta key.
	 * @param  string|array<int,string>|null  $value  Meta value (optional for EXISTS/NOT EXISTS).
	 *
	 * @phpstan-param '='|'!='|'>'|'>='|'<'|'<='|'LIKE'|'NOT LIKE'|'IN'|'NOT IN'|'BETWEEN'|'NOT BETWEEN'|'EXISTS'|'NOT EXISTS'|'REGEXP'|'NOT REGEXP'|'RLIKE' $compare Comparison operator.
	 * @phpstan-param 'NUMERIC'|'BINARY'|'CHAR'|'DATE'|'DATETIME'|'DECIMAL'|'SIGNED'|'TIME'|'UNSIGNED' $type Value type for CAST.
	 */
	public function where_meta( string $key, string|array|null $value = null, string $compare = '=', string $type = 'CHAR' ): static {
		$clause          = new \Args\MetaQuery\Clause();
		$clause->key     = $key;
		$clause->compare = $compare;
		$clause->type    = $type;

		if ( ! \is_null( $value ) ) {
			$clause->value = $value;
		}

		$this->args->meta_query->addClause( $clause );

		return $this;
	}

	/**
	 * Set complex taxonomy query.
	 */
	public function tax_query( \Args\TaxQuery\Query $query ): static {
		$this->args->tax_query = $query;

		return $this;
	}

	/**
	 * Add a taxonomy query clause.
	 *
	 * @param  string  $taxonomy  Taxonomy name.
	 * @param  int|string|array<int,int|string>  $terms  Term ID(s), slug(s), or name(s).
	 *
	 * @phpstan-param 'term_id'|'name'|'slug'|'term_taxonomy_id' $field Field to match.
	 * @phpstan-param 'IN'|'NOT IN'|'AND'|'EXISTS'|'NOT EXISTS' $operator Operator.
	 */
	public function where_taxonomy( string $taxonomy, int|string|array $terms, string $field = 'term_id', string $operator = 'IN' ): static {
		$clause           = new \Args\TaxQuery\Clause();
		$clause->taxonomy = $taxonomy;
		$clause->terms    = $terms;
		$clause->field    = $field;
		$clause->operator = $operator;

		$this->args->tax_query->addClause( $clause );

		return $this;
	}

	/**
	 * Filter by user permission.
	 *
	 * @phpstan-param 'readable'|'editable' $perm Permission.
	 */
	public function permission( string $perm ): static {
		$this->args->perm = $perm;

		return $this;
	}

	/**
	 * Filter by post mime type (for attachments).
	 *
	 * @param  string  $mime_type  Mime type.
	 */
	public function post_mime_type( string $mime_type ): static {
		$this->args->post_mime_type = $mime_type;

		return $this;
	}

	/**
	 * Enable or disable result caching.
	 *
	 * @param  bool  $cache  Whether to cache results.
	 */
	public function cache_results( bool $cache ): static {
		$this->args->cache_results = $cache;

		return $this;
	}

	/**
	 * Enable or disable post meta cache.
	 *
	 * @param  bool  $update  Whether to update post meta cache.
	 */
	public function update_post_meta_cache( bool $update ): static {
		$this->args->update_post_meta_cache = $update;

		return $this;
	}

	/**
	 * Enable or disable post term cache.
	 *
	 * @param  bool  $update  Whether to update post term cache.
	 */
	public function update_post_term_cache( bool $update ): static {
		$this->args->update_post_term_cache = $update;

		return $this;
	}

	/**
	 * Filter to published posts only.
	 */
	public function published(): static {
		return $this->post_status( 'publish' );
	}

	/**
	 * Filter to draft posts only.
	 */
	public function draft(): static {
		return $this->post_status( 'draft' );
	}

	/**
	 * Filter to pending posts only.
	 */
	public function pending(): static {
		return $this->post_status( 'pending' );
	}

	/**
	 * Filter to trashed posts only.
	 */
	public function trashed(): static {
		return $this->post_status( 'trash' );
	}

	/**
	 * Order by date descending (newest first).
	 */
	public function latest(): static {
		$this->args->orderby = 'date';
		$this->args->order   = 'DESC';

		return $this;
	}

	/**
	 * Order by date ascending (oldest first).
	 */
	public function oldest(): static {
		$this->args->orderby = 'date';
		$this->args->order   = 'ASC';

		return $this;
	}

	/**
	 * @phpstan-return PostQueryResult<T>
	 */
	public function result(): PostQueryResult {
		$wp_query = new \WP_Query( $this->args->toArray() );

		return new PostQueryResult( $this->model, $wp_query );
	}
}
