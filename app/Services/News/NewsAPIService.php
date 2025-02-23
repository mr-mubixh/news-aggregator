<?php

namespace App\Services\News;

use App\Services\News\NewsFetcherInterface;
use Illuminate\Support\Facades\Http;

class NewsAPIService implements NewsFetcherInterface
{

    public function fetchNews(): array
    {
        $response = Http::get('https://newsapi.org/v2/everything', [
            'domains' => 'bbc.co.uk',
            'sortBy' => 'publishedAt',
            'apiKey' => env('NEWS_API_KEY'),
        ]);

        return $response->successful() ? $response->json()['articles'] : [];
    }
}
