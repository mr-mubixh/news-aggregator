<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fetch-news', function () {
    (new \App\Services\NewsService())->fetchNewsFromAPI();
})->purpose('Fetch News Articles');

Schedule::call([\App\Services\NewsService::class, 'fetchNewsFromAPI'])->daily();
