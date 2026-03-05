<?php
declare(strict_types=1);

namespace App\Services\Sources;


use App\Interfaces\NewsInterface;
use Illuminate\Support\Collection;

abstract class NewsAbstract implements NewsInterface
{
    abstract public function __toString(): string;

    abstract public function fetchArticles(): Collection;
}
