<?php

namespace App\Providers;

use App\Services\News\GuardianAPIService;
use App\Services\News\NewsAPIService;
use App\Services\NewsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(NewsService::class, function ($app) {
            return new NewsService([
                new NewsAPIService(),
                new GuardianAPIService(),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
