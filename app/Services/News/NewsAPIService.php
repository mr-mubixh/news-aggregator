<?php

namespace App\Services\News;

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

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        if (!isset($data['articles'])) {
            return [];
        }

        $articles = [];
        foreach ($data['articles'] as $item) {
            $articles[] = [
                'title' => $item['title'] ?? 'No Title',
                'description' => $item['description'] ?? 'No Description',
                'source' => $item['source']['name'] ?? 'Unknown',
                'source_site' => 'news-api',
                'author' => $item['author'] ?? 'Unknown',
                'url' => $item['url'],
                'urlToImage' => $item['urlToImage'] ?? null,
                'published_at' => date('Y-m-d H:i:s', strtotime($item['publishedAt'] ?? now())),
                'content' => $item['content'] ?? 'No Content',
            ];
        }

        return $articles;
    }
}
