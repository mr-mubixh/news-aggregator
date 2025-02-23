<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianAPIService implements NewsFetcherInterface
{
    public function fetchNews(): array
    {
        try {
            $response = Http::timeout(30) // Set a 10-second timeout
            ->retry(3, 1000) // Retry 3 times, with a 1-second delay between retries
            ->get('https://content.guardianapis.com/search', [
                'api-key' => env('GUARDIAN_API_KEY'),
                'show-fields' => 'trailText,thumbnail,body', // Fetch additional fields
                'page-size' => 10, // Limit results
                'order-by' => 'newest'
            ]);

            if (!$response->successful()) {
                Log::error("Guardian API request failed: " . $response->status());
                return [];
            }

            $data = $response->json();
            if (!isset($data['response']['results'])) {
                Log::warning("Guardian API returned an unexpected response format.");
                return [];
            }

            return $this->transformArticles($data['response']['results']);

        } catch (\Exception $e) {
            Log::error("Guardian API request timed out: " . $e->getMessage());
            return [];
        }
    }

    private function transformArticles(array $results): array
    {
        $articles = [];
        foreach ($results as $item) {
            $articles[] = [
                'title' => $item['webTitle'] ?? 'No Title',
                'description' => $item['fields']['trailText'] ?? 'No Description',
                'source' => 'The Guardian',
                'source_site' => 'the-guardian',
                'author' => 'Unknown', // Guardian API does not provide author name
                'url' => $item['webUrl'],
                'urlToImage' => $item['fields']['thumbnail'] ?? null, // Image field
                'published_at' => date('Y-m-d H:i:s', strtotime($item['webPublicationDate'])),
                'content' => $item['fields']['body'] ?? 'No Content',
            ];
        }
        return $articles;
    }
}
