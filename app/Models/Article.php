<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'source',
        'image_url',
        'external_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'source' => 'string',
        'image_url' => 'string',
        'external_url' => 'string',
        'description' => 'string',
    ];


    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            ArticleAuthor::class,
        )->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            ArticleCategory::class,
        )->withTimestamps();
    }
}
