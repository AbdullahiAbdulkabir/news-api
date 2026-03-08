<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;

test('fetch articles', function (): void {
    $category = Category::factory()->create();
    $author = Author::factory()->create();
    Article::factory()->count(2)->hasAttached($author)->hasAttached($category)->create();

    $response = $this->getJson('/api/articles');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['title', 'image_url', 'external_url', 'source', 'content', 'published_at',
                    'authors' => ['*' => ['name']],
                    'categories' => ['*' => ['name']]],
            ],
        ]);
});

test('filters articles by author and categories', function (): void {
    $category = Category::factory()->create(['name' => 'politics']);
    $author = Author::factory()->create(['name' => 'Joan']);
    Article::factory()->hasAttached($author)->hasAttached($category)->create();

    $response = $this->getJson('/api/articles?filter[authors.name]=Joan&filter[categories.name]=politics&include=authors,categories');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['title', 'image_url', 'external_url', 'source', 'content', 'published_at',
                    'authors' => ['*' => ['name']],
                    'categories' => ['*' => ['name']]],
            ],

        ])
        ->assertJsonFragment([
            'categories' => [['name' => 'politics']],
            'authors' => [['name' => 'Joan']],
        ]);
});

test('filters articles by title', function (): void {
    Article::factory()->create(['title' => 'Live Update']);
    Article::factory()->create(['title' => 'Mr President']);

    $response = $this->getJson('/api/articles?filter[title]=Update');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['title' => 'Live Update']);
});

test('filters articles by sources', function (): void {
    Article::factory()->count(3)->create(['source' => 'NewsAPI']);

    $response = $this->getJson('/api/articles?filter[source]=NewsAPI');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment(['source' => 'NewsAPI']);
});
