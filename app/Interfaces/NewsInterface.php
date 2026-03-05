<?php
declare(strict_types=1);

namespace App\Interfaces;

interface NewsInterface extends \Stringable
{
    public function fetchArticles();
}

