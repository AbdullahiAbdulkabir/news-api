<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ArticleDTO extends Data
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $content,
        public ?string $author,
        public ?string $category,
        public string $source,
        public ?string $image_url,
        public ?string $external_url,
        public ?Carbon $published_at,
    ) {}
}
