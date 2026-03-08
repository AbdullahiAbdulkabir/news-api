<?php

namespace App\Providers;

use App\Services\NewsSources;
use App\Services\Sources\GuardianSource;
use App\Services\Sources\NewsApiSource;
use App\Services\Sources\NewYorkTimeSource;
use Illuminate\Support\ServiceProvider;

class NewsSourceServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->singleton(NewsSources::class, function () {
            $source = new NewsSources;

            $source->addSource(new NewsApiSource);
            if (! app()->environment('testing')) {
                $source->addSource(new GuardianSource);
                $source->addSource(new NewYorkTimeSource);
            }

            return $source;
        });
    }
}
