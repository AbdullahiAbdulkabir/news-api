<?php

declare(strict_types=1);

use App\DTOs\ArticleDTO;
use App\Interfaces\NewsInterface;
use App\Models\Article;
use App\Services\NewsSources;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $this->newsSources = new NewsSources;
});

test('news sources provider starts with no sources', function (): void {
    $newsSource = new NewsSources;
    expect($newsSource)->toHaveProperty('sources')
        ->and($newsSource->sources)->toBeInstanceOf(Collection::class)
        ->and($newsSource->sources)->toBeEmpty();
});

test('can add news source to sources provider', function (): void {
    $source = Mockery::mock(NewsInterface::class);
    $this->newsSources->addSource($source);
    expect($this->newsSources->sources)->toHaveCount(1)
        ->and($this->newsSources->sources->first())->toBe($source);
});

// Article Fetching Tests
test('fetch news calls fetchArticles on all sources', function (): void {
    // Create mock sources
    $source1 = Mockery::mock(NewsInterface::class);
    $source1->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([]));

    $source2 = Mockery::mock(NewsInterface::class);
    $source2->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([]));

    $this->newsSources->addSource($source1);
    $this->newsSources->addSource($source2);

    $this->newsSources->sync();
});

// Article Saving Tests
test('saves new article with single author', function (): void {
    $articleData = ArticleDTO::from([
        'title' => 'Test Article',
        'external_url' => 'https://example.com/external_url',
        'content' => 'Test content',
        'source' => 'NewsAPI',
        'published_at' => now(),
        'author' => 'Abdullahi Abd',
    ]);

    $source = Mockery::mock(NewsInterface::class);
    $source->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([$articleData]));

    $this->newsSources->addSource($source);
    $this->newsSources->sync();

    $this->assertDatabaseHas('articles', [
        'title' => 'Test Article',
        'external_url' => 'https://example.com/external_url',
    ]);

    $this->assertDatabaseHas('authors', [
        'name' => 'Abdullahi Abd',
    ]);
});

test('saves article with multiple authors', function (): void {
    $articleData = ArticleDTO::from([
        'title' => 'Test Article',
        'external_url' => 'https://example.com/test',
        'content' => 'Test content',
        'source' => '',
        'published_at' => now(),
        'author' => 'John Doe, Jane Smith',
    ]);

    $source = Mockery::mock(NewsInterface::class);
    $source->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([$articleData]));

    $this->newsSources->addSource($source);
    $this->newsSources->sync();

    $article = Article::where('external_url', 'https://example.com/test')->first();
    expect($article->authors)->toHaveCount(2)
        ->and($article->authors->pluck('name')->toArray())->toContain('John Doe', 'Jane Smith');
});

test('updates existing article without creating duplicate', function (): void {
    // Create initial article data
    $initial = ArticleDTO::from([
        'title' => 'Original Title',
        'external_url' => 'https://example.com/test',
        'source' => '',
        'content' => 'Original content',
        'published_at' => now(),
        'author' => 'John Doe',
    ]);

    // Updated version of the same article
    $updated = ArticleDTO::from([
        'title' => 'Updated Title',
        'external_url' => 'https://example.com/test',
        'content' => 'Updated content',
        'source' => '',
        'published_at' => now(),
        'author' => 'John Doe',
    ]);

    // Mock the first news source
    $source1 = Mockery::mock(NewsInterface::class);
    $source1->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([$initial]));

    // Add the first source and fetch news
    $this->newsSources->addSource($source1);
    $this->newsSources->sync();

    // Mock the second news source
    $source2 = Mockery::mock(NewsInterface::class);
    $source2->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([$updated]));

    // Reset the news sources provider to avoid re-fetching from the first source
    $this->newsSources = new NewsSources;

    // Add the second source and fetch news
    $this->newsSources->addSource($source2);
    $this->newsSources->sync();

    // Assert that there is only one article with the given URL
    $articles = Article::where('external_url', 'https://example.com/test')->get();
    expect($articles)->toHaveCount(1)
        ->and($articles->first()->title)->toBe('Updated Title');
});

// Edge Cases
test('handles article with no author', function (): void {
    $articleData = ArticleDTO::from([
        'title' => 'Test Article',
        'external_url' => 'https://example.com/test',
        'content' => 'Test content',
        'source' => '',
        'published_at' => now(),
        'author' => '',
    ]);

    $source = Mockery::mock(NewsInterface::class);
    $source->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([$articleData]));

    $this->newsSources->addSource($source);
    $this->newsSources->sync();

    $article = Article::where('external_url', 'https://example.com/test')->first();

    expect($article)->not->toBeNull()
        ->and($article->authors)->toBeEmpty();
});

test('handles empty response from news sources', function (): void {
    $source = Mockery::mock(NewsInterface::class);
    $source->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([]));

    $this->newsSources
        ->addSource($source);

    $this->newsSources->sync();
});

test('handles malformed author strings', function (): void {
    $articleData = ArticleDTO::from([
        'title' => 'Test Article',
        'external_url' => 'https://example.com/test',
        'content' => 'Test content',
        'source' => '',
        'published_at' => now(),
        'author' => '  Jan Felix  ,  Abdullahi Abu',
    ]);

    $source = Mockery::mock(NewsInterface::class);
    $source->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect([$articleData]));

    $this->newsSources->addSource($source);
    $this->newsSources->sync();

    $article = Article::where('external_url', 'https://example.com/test')->first();
    expect($article->authors)->toHaveCount(2)
        ->and($article->authors->pluck('name')->toArray())->toContain('Jan Felix', 'Abdullahi Abu');
});

afterEach(function (): void {
    Mockery::close();
});
