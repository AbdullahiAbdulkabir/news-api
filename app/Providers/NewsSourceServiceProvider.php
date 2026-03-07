<?php

namespace App\Providers;

use App\Services\NewsSources;
use App\Services\Sources\NewsApiSource;
use Illuminate\Support\ServiceProvider;

class NewsSourceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->singleton(NewsSources::class, function () {
            return new NewsSources(
                collect([
                    new NewsApiSource,
                    //                                        new GuardianSource,
                    //                    new NewYorkTimeSource,
                ])
            );
        });
    }
}
