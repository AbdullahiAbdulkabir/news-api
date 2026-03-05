<?php
declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class TitleFilter
{
    public function handle(Builder $builder, \Closure $next)
    {
        if (Request::filled('title')) {
            $title = Request::get('title');
            $builder->where('title', $title);
        }
        return $next($builder);

    }
}
