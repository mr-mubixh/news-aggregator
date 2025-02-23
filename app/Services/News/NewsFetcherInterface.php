<?php

namespace App\Services\News;

interface NewsFetcherInterface
{
    public function fetchNews(): array;
}
