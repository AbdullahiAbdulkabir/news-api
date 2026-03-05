<?php
declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class SourceFilter
{
    public function handle(Builder $builder, \Closure $next)
    {

        if (Request::filled('source')) {
            $source = Request::get('source');
            $builder->where('source', $source);
        }

        return $next($builder);

    }
}
