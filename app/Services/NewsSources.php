<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Services\Sources\NewsAbstract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class NewsSources
{
    public function __construct(private NewsAbstract $newsAbstract)
    {

    }

    public function sync(): void
    {
        $data = $this->newsAbstract->fetchArticles();
        $this->handleArticleSaving($data);
    }

    public function handleArticleSaving(Collection $data): void
    {
        DB::transaction(function () use ($data) {
            $data->each(function ($article) {
                Article::updateOrCreate([
                    'external_url' => $article->url,
                ], [
                    'title' => $article->title,
                    'description' => $article->description,
                    'content' => $article->content,
                    'author' => $article->author,
                    'category' => $article->category,
                    'source' => $article->source,
                    'image_url' => $article->image_url,
                    'published_at' => $article->published_at,
                ]);
            });
        });

//    db handle
    }
}
