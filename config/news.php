<?php

return [
    'sources' => [
        'newsapi' => 'https://newsapi.org/v2/everything?domains=bbc.co.uk&sortBy=publishedAt&apiKey=' . env('NEWS_API_KEY'),
//        'nytimes' => 'https://api.nytimes.com/svc/topstories/v2/home.json?api-key=' . env('NYTIMES_API_KEY'),
        'guardian' => 'https://content.guardianapis.com/search?api-key=' . env('GUARDIAN_API_KEY'),
    ]
];
