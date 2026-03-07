<?php
declare(strict_types=1);

namespace App\Services\Sources;

use App\DTOs\ArticleDTO;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class NewsApiSource extends NewsAbstract
{
    public function fetchArticles(): Collection
    {
        $data = $this->fetch('top-headlines');

        return LazyCollection::make(Arr::get($data, 'articles'))
            ->map(fn($article) => $this->map($article))->collect();
    }

    public function __toString(): string
    {
        return 'NewsAPI';
    }

    private function fetch(string $url): array
    {
        return $this->loadData([
            'apiKey' => Arr::get($this->sourceConfig(), 'api_key'),
            'language' => 'en'
        ], $url);
    }

    public function sources(): Collection
    {
        $data = $this->fetch('top-headlines/sources');
        return collect($data);
    }

    public function sourceConfig(): array
    {
        return config('news.newsapi');
    }

    public function map(array $data): ArticleDTO
    {
        $sources = $this->sources();

        return new ArticleDTO(
            title: Arr::get($data, 'title'),
            description: Arr::get($data, 'description'),
            content: Arr::get($data, 'content'),
            author: Arr::get($data, 'author'),
            category: Arr::get($sources->firstWhere('name', Arr::get($data, 'source.name')),'category'),
            source: $this->__toString(). "- " .Arr::get($data, 'source.name'),
            image_url: Arr::get($data, 'urlToImage'),
            external_url: Arr::get($data, 'url'),
            published_at: Carbon::parse(Arr::get($data, 'publishedAt')),
        );
    }
}
