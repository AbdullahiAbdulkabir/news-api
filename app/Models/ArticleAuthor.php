<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleAuthor extends Pivot
{
    protected $fillable = ['article_id', 'author_id'];

    protected $table = 'article_authors';
}
