<?php

namespace App\Providers;

use App\Services\NewsSources;
use App\Services\Sources\NewYorkTimeSource;
use Illuminate\Support\ServiceProvider;

class NewsSourceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->singleton(NewsSources::class, function ($app) {
            return new NewsSources(
                collect([
                    //                    new NewsApiSource(),
                    //                    new GuardianSource,
                    new NewYorkTimeSource,
                ])
            );
        });
    }
}
