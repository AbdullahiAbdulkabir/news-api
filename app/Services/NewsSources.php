<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\NewsInterface;
use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleCategory;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

readonly class NewsSources
{
    public Collection $sources;

    public function __construct()
    {
        $this->sources = Collection::make();
    }

    public function addSource(NewsInterface $source): static
    {
        $this->sources->push($source);

        return $this;
    }

    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function sync(?string $specificSource = null): void
    {
        $this->sources->when($specificSource,
            fn ($s) => $s->filter(fn ($s): bool => strtolower($s->__toString()) === strtolower($specificSource)))
            ->each(function (NewsInterface $source): void {
                $data = $source->fetchArticles();
                $data->chunk(100)->each(function (Collection $articles): void {
                    $this->handleArticleSaving($articles);
                });
            });
    }

    public function handleArticleSaving(Collection $data): void
    {
        DB::transaction(function () use ($data): void {
            $categories = $this->handleCategorySaving($data);
            $authors = $this->handleAuthorsSaving($data);

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
            $this->handleArticleAuthorSaving($data, $authors);
        });

        $this->clearCache();
    }

    private function clearCache(): void
    {
        Cache::tags(Article::CACHE_KEY)->flush();
    }

    public function handleCategorySaving(Collection $data): Collection
    {
        $categories = $data
            ->pluck('category')->filter()
            ->flatMap(fn ($category): array => explode(',', $category))
            ->map(fn ($category): string => trim($category))->filter()->unique()
            ->values();

        if ($categories->isEmpty()) {
            return $categories;
        }

        $payload = $categories->map(fn ($category): array => [
            'name' => $category,
        ]);

        Category::upsert(
            $payload->toArray(),
            ['name'],
            ['updated_at']
        );

        return Category::query()
            ->whereIn('name', $payload->toArray())
            ->get()
            ->keyBy('name');

    }

    public function handleAuthorsSaving(Collection $data): Collection
    {
        $authors = $data
            ->pluck('author')->filter()
            ->flatMap(fn ($author): array => explode(',', $author))
            ->map(fn ($author): string => trim($author))->filter()->unique()
            ->values();

        if ($authors->isEmpty()) {

            return $authors;
        }

        $payload = $authors->map(fn ($author): array => [
            'name' => $author,
        ]);

        Author::upsert(
            $payload->toArray(),
            ['name'],
            ['updated_at']
        );

        return Author::query()
            ->whereIn('name', $payload->toArray())
            ->get()
            ->keyBy('name');
    }

    private function handleArticleCategorySaving(Collection $articles, Collection $categories): void
    {
        $savedArticles = $this->getSavedArticles($articles);

        $pivot = $articles
            ->filter(fn ($article) => $article->category)
            ->flatMap(function ($article) use ($savedArticles, $categories) {

                $savedArticle = $savedArticles->get($article->external_url);

                if (! $savedArticle) {
                    return [];
                }

                $categoryNames = collect(explode(',', $article->category))
                    ->map(fn ($name): string => trim($name));

                return collect($categoryNames)
                    ->map(function ($name) use ($savedArticle, $categories): ?array {

                        $category = $categories->get($name);

                        if (! $category) {
                            return null;
                        }

                        return [
                            'article_id' => $savedArticle->id,
                            'category_id' => $category->id,
                        ];
                    })
                    ->filter()
                    ->values();
            })
            ->values();

        ArticleCategory::query()->upsert(
            $pivot->toArray(),
            ['article_id', 'category_id']
        );
    }

    private function handleArticleAuthorSaving(Collection $articles, Collection $authors): void
    {
        $savedArticles = $this->getSavedArticles($articles);

        $pivot = $articles
            ->filter(fn ($article) => $article->author)
            ->flatMap(function ($article) use ($savedArticles, $authors) {

                $savedArticle = $savedArticles->get($article->external_url);

                if (! $savedArticle) {
                    return [];
                }

                $authorNames = str($article->author)
                    ->explode(',')
                    ->map('trim')
                    ->filter();

                return $authorNames->map(function ($name) use ($savedArticle, $authors): ?array {
                    $author = $authors->get($name);

                    if (! $author) {
                        return null;
                    }

                    return [
                        'article_id' => $savedArticle->id,
                        'author_id' => $author->id,
                    ];
                })->filter();
            })
            ->values();

        ArticleAuthor::query()->upsert(
            $pivot->toArray(),
            ['article_id', 'author_id']
        );
    }

    private function getSavedArticles(Collection $articles): Collection
    {
        return Article::query()
            ->whereIn('external_url', $articles->pluck('external_url'))
            ->get()
            ->keyBy('external_url');
    }
}
