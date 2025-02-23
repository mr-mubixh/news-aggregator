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
            $publishedAt = isset($article['publishedAt'])
                ? date('Y-m-d H:i:s', strtotime($article['publishedAt']))
                : now();

            Article::updateOrCreate(
                ['url' => $article['url']],
                [
                    'title' => $article['title'] ?? 'No Title',
                    'description' => $article['description'] ?? 'No Description',
                    'source' => $article['source']['name'] ?? 'Unknown',
                    'author' => $article['author'] ?? 'Unknown',
                    'urlToImage' => $article['urlToImage'] ?? null,
                    'published_at' => $publishedAt,
                    'content' => $article['content'] ?? 'No Content',
                ]
            );
        }
    }
}
