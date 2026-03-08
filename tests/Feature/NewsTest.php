<?php

declare(strict_types=1);

use App\Services\NewsSources;
use App\Services\Sources\NewsApiSource;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Config::set('news.newsapi.api_key', 'news-api-key');
    CarbonImmutable::setTestNow();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

// Fetching command test
test('fetch:news command is scheduled to run every 30 minutes', function (): void {
    $schedule = app()->make(Schedule::class);

    $scheduledCommand = collect($schedule->events())->first(fn (Event $event): bool => str_contains($event->command, 'fetch:news'));

    expect($scheduledCommand)->not->toBeNull()
        ->and($scheduledCommand->expression)->toBe('*/30 * * * *');
});

test('command successfully fetches and stores news articles', function (): void {
    Http::fake([
        'newsapi.org/*' => Http::response([
            'articles' => [
                [
                    'title' => 'Breaking Tech Update',
                    'description' => 'AI breakthroughs in 2026 are transforming industries.',
                    'content' => 'Detailed content about AI breakthroughs and industry impact.',
                    'author' => 'Jane Doe',
                    'source' => 'Tech Daily',
                    'category' => 'Technology',
                    'url' => 'https://example.com/tech-update',
                    'urlToImage' => 'https://example.com/images/tech.jpg',
                    'publishedAt' => '2026-03-08T12:30:00Z',
                ],
                [
                    'title' => 'Global Sports Highlights',
                    'description' => 'Top moments from the world of sports this week.',
                    'content' => 'A recap of major sports events including scores and highlights.',
                    'author' => 'John Smith',
                    'source' => 'Sports Weekly',
                    'category' => 'Sports',
                    'url' => 'https://example.com/sports-highlights',
                    'urlToImage' => 'https://example.com/images/sports.jpg',
                    'publishedAt' => '2026-03-08T10:45:00Z',
                ],
            ],
        ]),
    ]);

    $newsApiSource = new NewsApiSource;
    $newsSources = new NewsSources;
    $newsSources->addSource($newsApiSource);

    $result = Artisan::call('fetch:news');

    expect($result)->toBe(0)
        ->and(Http::recorded())->toHaveCount(2);

    $this->assertDatabaseHas('articles', [
        'title' => 'Breaking Tech Update',
        'external_url' => 'https://example.com/tech-update',
        'source' => 'NewsAPI',
    ]);

    $this->assertDatabaseHas('articles', [
        'title' => 'Global Sports Highlights',
        'external_url' => 'https://example.com/sports-highlights',
        'source' => 'NewsAPI',
    ]);
});

test('command throws when API errors', function (): void {

    Http::fake([
        'newsapi.org/*' => Http::response(
            ['error' => 'API Error'],
            404
        ),
    ]);

    $exitCode = Artisan::call('fetch:news');

    expect($exitCode)->toBe(0);
});

test('command handles empty API responses', function (): void {
    Http::fake([
        'newsapi.org/*' => Http::response([
            'articles' => [],
        ]),
    ]);

    $exitCode = Artisan::call('fetch:news');

    expect($exitCode)->toBe(0)
        ->and(App\Models\Article::count())->toBe(0);
});

test('fetch:news command returns error if source unavailable', function () {
    $newsSources = new NewsSources;
    $newsSources->addSource(new NewsApiSource);

    $exitCode = Artisan::call('fetch:news', ['--source' => 'NonExistentSource']);

    expect($exitCode)->toBe(Command::FAILURE);
});
