<?php

namespace App\Services\News\NewsApi;

use App\Services\News\NewsAdapterInterface;
use Carbon\Carbon;

class NewsAPIAdapter implements NewsAdapterInterface
{
    public function transform($data, $origin): array
    {
        return [
            'title' => $data['title'] ?? '',
            'content' => $data['content'] ?? '', // todo : this is not whole content
            'snippet' => $data['description'] ?? '',
            'published_at' => Carbon::parse($data['publishedAt']),
            'source' => $data['source']['name'] ?? '',
            'authors' => isset($data['author']) ? [$data['author']] : [], // This origin has one author
            'image' => $data['urlToImage'] ?? null,
            'url' => $data['url'] ?? null,
            'origin' => $origin,
            'categories' => [], // This origin has no category
        ];
    }
}
