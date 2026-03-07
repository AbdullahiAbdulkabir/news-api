<?php

namespace App\Http\Controllers;

use App\Actions\GetArticlesAction;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;

class FetchArticlesController extends Controller
{
    public function __invoke(GetArticlesAction $action): array|Paginator|CursorPaginator
    {
        return $action();
    }
}
