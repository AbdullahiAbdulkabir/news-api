<?php

declare(strict_types=1);

namespace App\Services\Sources;

use App\DTOs\ArticleDTO;
use App\Exceptions\HttpException;
use App\Interfaces\NewsInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class NewsAbstract implements NewsInterface
{
    abstract public function __toString(): string;

    abstract public function fetchArticles(): Collection;

    abstract public function sourceConfig(): array;

    abstract public function map(array $data): ArticleDTO;

    public function getClient(string $baseUrl): PendingRequest
    {
        return Http::baseUrl($baseUrl)
            ->timeout(30);
    }

    public function loadData(array $params, string $url): array
    {
        $config = $this->sourceConfig();

        try {
            $response = $this->getClient(
                Arr::get($config, 'url')
            )->retry(2, 2000)->withQueryParameters($params)
                ->get($url);

            if (! $response->successful()) {
                throw new HttpException(message: "Unable to load news {$this->__toString()}", data: ['error' => $response->json()]);
            }

            Log::info("Successful loading news {$this->__toString()}");


            return $response->json();
        } catch (\Throwable $throwable) {
            Log::error("An error occurred while load news {$this->__toString()}", ['exception' => $throwable]);

            return [];
        }

    }
}
