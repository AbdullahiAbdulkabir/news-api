<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class PublishedAtFilter
{
    public function handle(Builder $builder, \Closure $next)
    {
        if (Request::filled('from') && Request::filled('to')) {
            $from = Request::get('from');
            $to = Request::get('to');
            $builder->whereBetween('published_at', [$from, $to]);
        }

        return $next($builder);

    }
}
