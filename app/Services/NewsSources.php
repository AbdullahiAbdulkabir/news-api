<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Services\Sources\NewsAbstract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class NewsSources
{
    public function __construct(protected Collection $sources)
    {

    }

    public function sync(): void
    {
        $this->sources->each(function (NewsAbstract $source) {
            $data = $source->fetchArticles();
            $data->chunk(100)->each(function ($news) use ($source) {
                $this->handleArticleSaving($news);
            });

        });

    }

    public function handleArticleSaving(Collection $data): void
    {
        DB::transaction(function () use ($data) {
            $this->handleCategorySaving($data);
            $data->each(function ($article) {
                $article = Article::query()->updateOrCreate([
                    'external_url' => $article->url,
                ], [
                    'title' => $article->title,
                    'description' => $article->description,
                    'content' => $article->content,
                    'source' => $article->source,
                    'image_url' => $article->image_url,
                    'published_at' => $article->published_at,
                ]);

//                $article->categories()->sync($article->categories);
            });
        });
    }

    public function handleCategorySaving(Collection $data): Collection
    {
        $categories = collect($data->pluck('category'))->filter()->unique();

        if ($categories->isNotEmpty()) {
            $categories->each(function ($category) {
                Category::updateOrCreate(['name' => $category]);
            });
        }

        return $categories;
    }
}
