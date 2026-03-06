<?php
declare(strict_types=1);

namespace App\Services\Sources;

use App\DTOs\ArticleDTO;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class GuardianSource extends NewsAbstract
{
    public function fetchArticles(): Collection
    {
        $data = $this->fetch();

        return LazyCollection::make(Arr::get($data, 'response.results'))
            ->map(fn($article) => $this->map($article))->collect();
    }

    public function __toString(): string
    {
        return 'The Guardian';
    }

    private function fetch(string $url = 'search'): array
    {
        return $this->loadData([
            'api-key' => Arr::get($this->sourceConfig(), 'api_key'),
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
            description: Arr::get($data, 'description'),
            content: Arr::get($data, 'content'),
            author: Arr::get($data, 'author'),
            category: Arr::get($data, 'sectionId'),
            source: $this->__toString(),
            image_url: Arr::get($data, 'urlToImage'),
            url: Arr::get($data, 'webUrl'),
            published_at: Carbon::parse(Arr::get($data, 'webPublicationDate')),
        );
    }
}
