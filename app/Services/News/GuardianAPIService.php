<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class GuardianAPIService implements NewsFetcherInterface
{
    public function fetchNews(): array
    {
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => env('GUARDIAN_API_KEY'),
//            'show-fields' => 'headline,trailText,thumbnail,body', // Fetch additional fields
//            'page-size' => 10, // Limit results
//            'order-by' => 'newest'
        ]);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        if (!isset($data['response']['results'])) {
            return [];
        }

        $articles = [];
        foreach ($data['response']['results'] as $item) {
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
