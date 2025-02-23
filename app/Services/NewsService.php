<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class NewsService
{
    private function formatDate($date)
    {
        return $date ? date('Y-m-d H:i:s', strtotime($date)) : now();
    }

    public function fetchNewsFromAPI(): void
    {
        $newsSources = config('news.sources');

        foreach ($newsSources as $url) {
            $response = Http::get($url);
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
                    'published_at' => $this->formatDate($article['publishedAt'] ?? null),
                    'content' => $article['content'] ?? null, // Save full content if available
                ]
            );
        }
    }
}
