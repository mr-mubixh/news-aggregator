<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Article;

class NewsService
{
    public function fetchNewsFromAPI(): void
    {
        $newsSources = [
            'https://newsapi.org/v2/top-headlines?country=us&apiKey=' . env('NEWS_API_KEY'),
            'https://api.nytimes.com/svc/topstories/v2/home.json?api-key=' . env('NYTIMES_API_KEY'),
            'https://content.guardianapis.com/search?api-key=' . env('GUARDIAN_API_KEY')
        ];

        foreach ($newsSources as $url) {
            $response = Http::get($url);
            if ($response->successful()) {
                $this->storeArticles($response->json());
            }
        }
    }

    private function storeArticles($data)
    {
        foreach ($data['articles'] as $article) {
            Article::updateOrCreate(
                ['url' => $article['url']],
                [
                    'title' => $article['title'],
                    'description' => $article['description'],
                    'source' => $article['source']['name'],
                    'author' => $article['author'] ?? 'Unknown',
                    'published_at' => $article['publishedAt'],
                ]
            );
        }
    }
}
