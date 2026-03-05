<?php
declare(strict_types=1);
return [
    'newsapi' => [
        'api_key' => env('NEWS_API_KEY'),
        'url' => env('NEWSAPI_URL', 'https://newsapi.org/v2/'),
    ],
    'nytimes' => [
        'api_key' => env('NYTIMES_API_KEY'),
        'url' => env('NYTIMES_URL', 'https://api.nytimes.com/svc/search/v2/'),
    ],
    'guardian' => [
        'api_key' => env('GUARDIAN_API_KEY'),
        'url' => env('NYTIMES_URL', 'https://content.guardianapis.com/'),
    ],
];

