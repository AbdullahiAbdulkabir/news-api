<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\ArticleDTO;
use App\Models\Article;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetArticlesAction
{
    public function __invoke(): array|Paginator|CursorPaginator
    {
        $query = QueryBuilder::for(Article::class)
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
            ->allowedSorts(['title', 'authors.name', 'categories.name', 'source', 'published_at']);

        return ArticleDTO::collect($query->cursorPaginate());
    }
}
