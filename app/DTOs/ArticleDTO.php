<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Article;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\PaginatedDataCollection;

class ArticleDTO extends Data
{
    public function __construct(
        public readonly ?int $id,
        public string $title,
        public ?string $description,
        public ?string $content,
        public ?string $author,
        public ?string $category,
        public string $source,
        public ?string $image_url,
        public ?string $external_url,
        public ?CarbonImmutable $published_at,
        #[DataCollectionOf(AuthorDTO::class)]
        public DataCollection|Lazy|null $authors,
        #[DataCollectionOf(CategoryDTO::class)]
        public DataCollection|Lazy|null $categories,
    ) {}

    public static function fromModel(Article $article): self
    {
        return self::from([
            ...$article->toArray(),
            'published_at' => CarbonImmutable::parse($article->published_at),
            'authors' => Lazy::whenLoaded('authors', $article, fn (): Collection|PaginatedDataCollection|CursorPaginatedDataCollection|array => AuthorDTO::collect($article->authors)),
            'categories' => Lazy::whenLoaded('categories', $article, fn (): Collection|PaginatedDataCollection|CursorPaginatedDataCollection|array => CategoryDto::collect($article->categories)),
        ])->exclude('author', 'category');
    }
}
