<?php

declare(strict_types=1);

namespace App\Services\Sources;

use App\DTOs\ArticleDTO;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class NewYorkTimeSource extends NewsAbstract
{
    public function fetchArticles(): Collection
    {
        $data = $this->fetch('articlesearch.json');

        return LazyCollection::make(Arr::get($data, 'response.docs'))
            ->map($this->mapCallback())->collect();
    }

    public function __toString(): string
    {
        return 'New York Times';
    }

    private function fetch(string $url): array
    {
        return $this->loadData([
            'api-key' => Arr::get($this->sourceConfig(), 'api_key'),
        ], $url);
    }

    public function sourceConfig(): array
    {
        return config('news.nytimes');
    }

    public function mapCallback(): \Closure
    {
        return fn ($data): ArticleDTO => ArticleDTO::from([
            'title' => Arr::get($data, 'headline.main'),
            'description' => Arr::get($data, 'snippet'),
            'content' => Arr::get($data, 'abstract'),
            'author' => Arr::get($data, 'byline.original'),
            'category' => Arr::get($data, 'section_name'),
            'source' => $this->__toString().'- '.Arr::get($data, 'source'),
            'image_url' => Arr::get($data, 'multimedia.default.url'),
            'external_url' => Arr::get($data, 'web_url'),
            'published_at' => CarbonImmutable::parse(Arr::get($data, 'pub_date')),
        ]);
    }
}
