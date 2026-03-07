<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleCategory extends Pivot
{
    protected $fillable = [
        'article_id',
        'category_id',
    ];

    protected $table = 'article_categories';
}
