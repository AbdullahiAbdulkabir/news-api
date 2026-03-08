<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    const CACHE_KEY = 'articles';

    protected $fillable = [
        'title',
        'description',
        'content',
        'source',
        'image_url',
        'external_url',
        'published_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Author, $this>
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            ArticleAuthor::class,
        )->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            ArticleCategory::class,
        )->withTimestamps();
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'source' => 'string',
            'image_url' => 'string',
            'external_url' => 'string',
            'description' => 'string',
        ];
    }
}
