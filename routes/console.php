<?php

use App\Services\NewsService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fetch-news', function () {
    $newsService = app(NewsService::class);
    $newsService->fetchAllNews();

})->purpose('Fetch News Articles');
Schedule::command('fetch-news')->daily();
