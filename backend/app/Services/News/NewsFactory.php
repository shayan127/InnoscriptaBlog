<?php

namespace App\Services\News;

use App\Models\Blog;
use App\Services\News\Guardian\GuardianAdapter;
use App\Services\News\Guardian\GuardianStrategy;
use App\Services\News\NewsApi\NewsAPIAdapter;
use App\Services\News\NewsApi\NewsAPIStrategy;
use App\Services\News\NYT\NYTAdapter;
use App\Services\News\NYT\NYTStrategy;

class NewsFactory
{
    public static function create(string $origin, array $config, ?Blog $blog): ?AbstractNewsStrategy
    {
        $config['last_news_datetime'] = $blog['published_at'] ?? null;
        return match ($origin) {
            'NewsAPI' => new NewsAPIStrategy($config, new NewsAPIAdapter()),
            'NYT' => new NYTStrategy($config, new NYTAdapter()),
            'Guardian' => new GuardianStrategy($config, new GuardianAdapter()),
            default => null,
        };
    }
}
