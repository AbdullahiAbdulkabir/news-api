<?php

declare(strict_types=1);

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface NewsInterface extends \Stringable
{
    public function fetchArticles(): Collection;
}
