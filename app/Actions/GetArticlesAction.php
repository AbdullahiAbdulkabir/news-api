<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\ArticleDTO;
use App\Models\Article;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetArticlesAction
{
    public function __invoke(): array|Paginator|CursorPaginator
    {
        $query = Request::query();
        $cacheKey = $this->cacheKey($query);
        $paginate = Cache::tags([Article::CACHE_KEY])->flexible($cacheKey,
            [200, 400],
            fn () => $this->buildQuery());

        return ArticleDTO::collect($paginate);
    }

    private function buildQuery(): CursorPaginator
    {
        $perPage = Request::integer('per_page', 15);

        return QueryBuilder::for(Article::class)
            ->with(['authors', 'categories'])
            ->allowedFilters([
                AllowedFilter::partial('title'),
                AllowedFilter::partial('authors.name'),
                AllowedFilter::partial('categories.name'),
                AllowedFilter::partial('source'),
                AllowedFilter::exact('published_at'),
            ])
            ->allowedIncludes('authors', 'categories')
            ->defaultSort('-published_at')
            ->allowedSorts(['title', 'authors.name', 'categories.name', 'source', 'published_at'])
            ->cursorPaginate($perPage);
    }

    private function cacheKey(array $params): string
    {
        $filteredParams = collect($params)
            ->except(['cursor'])
            ->sortKeys()
            ->all();

        $hash = md5(json_encode($filteredParams));

        return 'articles:list:'.$hash;
    }
}
