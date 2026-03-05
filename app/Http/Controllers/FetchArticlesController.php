<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FetchArticlesController extends Controller
{
    public function __invoke(ArticleService $articleService): AnonymousResourceCollection
    {
        return $articleService->fetchArticles();
    }
}
