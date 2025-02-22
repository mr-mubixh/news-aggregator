<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class NewsService
{
    public function fetchNewsFromAPI(): void
    {
        echo "Im called";
        $newsSources = [
            'https://newsapi.org/v2/everything?domains=bbc.co.uk&sortBy=publishedAt&apiKey=' . env('NEWS_API_KEY'),
//            'https://api.nytimes.com/svc/topstories/v2/home.json?api-key=' . env('NYTIMES_API_KEY'),
//            'https://content.guardianapis.com/search?api-key=' . env('GUARDIAN_API_KEY')
        ];

        foreach ($newsSources as $url) {
            $response = Http::get($url);
            Log::info($response);
            if ($response->successful()) {
                $this->storeArticles($response->json());
            }
        }
    }

    private function storeArticles($data)
    {
        if ($data['status'] !== 'ok' || empty($data['articles'])) {
            return; // Ensure data is valid before processing
        }

        foreach ($data['articles'] as $article) {
            Article::updateOrCreate(
                ['url' => $article['url']], // Prevent duplicate entries
                [
                    'title' => $article['title'],
                    'description' => $article['description'] ?? null,
                    'source' => $article['source']['name'] ?? 'Unknown',
                    'author' => $article['author'] ?? 'Unknown',
                    'urlToImage' => $article['urlToImage'] ?? null,
                    'published_at' => $article['publishedAt'],
                    'content' => $article['content'] ?? null, // Save full content if available
                ]
            );
        }
    }
}
