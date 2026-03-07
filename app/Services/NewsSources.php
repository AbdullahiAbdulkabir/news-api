<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Author;
use App\Models\Category;
use App\Services\Sources\NewsAbstract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class NewsSources
{
    public function __construct(protected Collection $sources) {}

    public function sync(): void
    {
        $this->sources->each(function (NewsAbstract $source): void {
            $data = $source->fetchArticles();
            $data->chunk(100)->each(function (Collection $news): void {
                $this->handleArticleSaving($news);
            });

        });

    }

    public function handleArticleSaving(Collection $data): void
    {
        DB::transaction(function () use ($data): void {
            $categories = $this->handleCategorySaving($data);
            $this->handleAuthorsSaving($data);

            $articles = $data->map(function ($article): array {
                return [
                    'external_url' => $article->external_url,
                    'title' => $article->title,
                    'description' => $article->description,
                    'content' => $article->content,
                    'source' => $article->source,
                    'image_url' => $article->image_url,
                    'published_at' => $article->published_at,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
            Article::query()->upsert(
                $articles->toArray(),
                ['external_url'],
                ['title', 'description', 'content', 'source', 'image_url', 'published_at', 'updated_at']
            );
            $this->handleArticleCategorySaving($data, $categories);
        });
    }

    public function handleCategorySaving(Collection $data): Collection
    {
        $categories = collect($data->pluck('category'))->filter()->unique();
        if ($categories->isNotEmpty()) {
            $payload = $categories->map(fn ($category): array => [
                'name' => $category,
            ]);

            Category::upsert(
                $payload->toArray(),
                ['name'],
                ['updated_at']
            );
        }

        return Category::query()
            ->whereIn('name', $payload->toArray())
            ->get()
            ->keyBy('name');
    }

    public function handleAuthorsSaving(Collection $data): Collection
    {
        $authors = collect($data->pluck('author'))->filter()->unique();
        if ($authors->isNotEmpty()) {
            $payload = $authors->map(fn ($category): array => [
                'name' => $category,
            ]);

            Author::upsert(
                $payload->toArray(),
                ['name'],
                ['updated_at']
            );
        }

        return $authors;
    }

    private function handleArticleCategorySaving(Collection $articles, Collection $categories): void
    {
        $savedArticles = Article::query()
            ->whereIn('external_url', $articles->pluck('external_url'))
            ->get()
            ->keyBy('external_url');

        $pivot = $articles
            ->filter(fn ($article) => $article->category)
            ->map(function ($article) use ($savedArticles, $categories): ?array {

                $savedArticle = $savedArticles->get($article->external_url);
                $category = $categories->get($article->category);

                if (! $savedArticle || ! $category) {
                    return null;
                }

                return [
                    'article_id' => $savedArticle->id,
                    'category_id' => $category->id,
                ];

            })
            ->filter()
            ->values();

        ArticleCategory::query()->upsert(
            $pivot->toArray(),
            ['article_id', 'category_id']
        );
    }
}
