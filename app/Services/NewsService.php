<?php

namespace App\Services;

use App\Models\Article;
use App\Services\News\NewsFetcherInterface;
use Illuminate\Support\Facades\Log;

class NewsService
{
    protected array $newsFetchers;

    public function __construct(array $newsFetchers)
    {
        $this->newsFetchers = $newsFetchers;
    }

    public function fetchAllNews()
    {
        foreach ($this->newsFetchers as $fetcher) {
            $articles = $fetcher->fetchNews();
            $this->storeArticles($articles);
        }
    }

    private function storeArticles(array $articles)
    {
        foreach ($articles as $article) {
            try {
                Article::updateOrCreate(
                    ['url' => $article['url']], // Unique constraint
                    [
                        'title' => $article['title'] ?? 'No Title',
                        'description' => $article['description'] ?? 'No Description',
                        'source' => $article['source'] ?? 'Unknown',
                        'source_site' => $article['source_site'] ?? 'Unknown',
                        'author' => $article['author'] ?? 'Unknown',
                        'urlToImage' => $article['urlToImage'] ?? null,
                        'published_at' => $this->formatDate($article['published_at'] ?? null),
                        'content' => $article['content'] ?? 'No Content',
                    ]
                );
            } catch (\Exception $e) {
                Log::error("Failed to save article: " . $e->getMessage(), ['article' => $article]);
            }
        }
    }

    private function formatDate($date)
    {
        return $date ? date('Y-m-d H:i:s', strtotime($date)) : now();
    }
}
