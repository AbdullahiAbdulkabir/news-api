<?php
declare(strict_types=1);

namespace App\DTOs;


use Carbon\Carbon;

// You can also  use spatie data package
readonly class ArticleDTO
{
    public function __construct(
        public string  $title,
        public ?string $description,
        public ?string $content,
        public ?string $author,
        public ?string $category,
        public string  $source,
        public ?string $imageUrl,
        public ?string $url,
        public ?Carbon $publishedAt,
    )
    {
    }
}
