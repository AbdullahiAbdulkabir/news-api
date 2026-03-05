<?php
declare(strict_types=1);

namespace App\Services;

use App\Filters\CategoryFilter;
use App\Filters\PublishedAtFilter;
use App\Filters\SourceFilter;
use App\Filters\TitleFilter;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Pipeline;

class ArticleService
{
    public function fetchArticles(): AnonymousResourceCollection
    {
        $articles = Pipeline::send(Article::query()->with(['authors','categories']))->through([
            SourceFilter::class,
            PublishedAtFilter::class,
            TitleFilter::class,
            CategoryFilter::class,
        ])->thenReturn();

        return ArticleResource::collection($articles->paginate(20));
    }
}
