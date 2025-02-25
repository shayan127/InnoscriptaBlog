<?php

namespace App\Services\News\NYT;

use App\Services\News\NewsAdapterInterface;
use Carbon\Carbon;

class NYTAdapter implements NewsAdapterInterface
{
    public function transform($data, $origin): array
    {
        $article = [
            'title' => $data['headline']['main'] ?? '',
            'content' => $data['lead_paragraph'] ?? '', // todo : this is not whole content
            'snippet' => $data['snippet'] ?? '',
            'published_at' => Carbon::parse($data['pub_date']),
            'source' => $data['source'] ?? '',
            'authors' => isset($data['byline']['original']) ? [$data['byline']['original']] : [], // todo : get all people
            'image' => null,
            'url' => $data['web_url'] ?? null,
            'origin' => $origin,
            'categories' => [],
        ];

        // Use keywords as categories
        foreach ($data['keywords'] as $category) {
            $article['categories'][] = $category['value'];
        }

        foreach ($data['multimedia'] as $media) {
            if ($media['type'] === 'image') {
                $article['image'] = 'https://static01.nyt.com/' . $media['url']; // todo : I'm not sure this is the only base url
                break;
            }
        }
        return $article;
    }
}
