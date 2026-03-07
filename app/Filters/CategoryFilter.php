<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class CategoryFilter
{
    public function handle(Builder $builder, \Closure $next)
    {
        if (Request::filled('category')) {
            $category = Request::get('category');
            $builder->whereHas('categories', fn ($query) => $query->where('name', $category));
        }

        return $next($builder);

    }
}
