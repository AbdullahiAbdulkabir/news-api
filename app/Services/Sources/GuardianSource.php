<?php

declare(strict_types=1);

namespace App\Services\Sources;

use App\DTOs\ArticleDTO;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class GuardianSource extends NewsAbstract
{
    public function fetchArticles(): Collection
    {
        $data = $this->fetch();

        return LazyCollection::make(Arr::get($data, 'response.results'))
            ->map(fn (array $article): ArticleDTO => $this->map($article))->collect();
    }

    public function __toString(): string
    {
        return 'The Guardian';
    }

    private function fetch(string $url = 'search'): array
    {
        return $this->loadData([
            'api-key' => Arr::get($this->sourceConfig(), 'api_key'),
            'show-fields' => 'all',
        ], $url);
    }

    public function sourceConfig(): array
    {
        return config('news.guardian');
    }

    public function map(array $data): ArticleDTO
    {
        return new ArticleDTO(
            title: Arr::get($data, 'webTitle'),
            description: Arr::get($data, 'fields.bodyText'),
            content: Arr::get($data, 'content'),
            author: Arr::get($data, 'fields.byline'),
            category: Arr::get($data, 'sectionId'),
            source: $this->__toString().'- '.Arr::get($data, 'fields.publication'),
            image_url: Arr::get($data, 'urlToImage'),
            external_url: Arr::get($data, 'webUrl'),
            published_at: CarbonImmutable::parse(Arr::get($data, 'webPublicationDate')),
        );
    }
}
