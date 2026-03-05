<?php
declare(strict_types=1);

namespace App\Services\Sources;

use Illuminate\Support\Collection;

class GuardianSource extends NewsAbstract
{

    public function fetchArticles(): Collection
    {
        // TODO: Implement fetchArticles() method.
    }

    public function __toString(): string
    {
        return 'The Guardian';
    }
}
