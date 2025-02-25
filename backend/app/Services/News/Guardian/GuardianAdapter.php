<?php

namespace App\Services\News\Guardian;

use App\Services\News\NewsAdapterInterface;
use Carbon\Carbon;

class GuardianAdapter implements NewsAdapterInterface
{
    public function transform($data, $origin): array
    {
        $article = [
            'title' => $data['webTitle'] ?? '',
            'content' => '', // this origin does not have content
            'snippet' => '', // this origin does not have snippet
            'published_at' => Carbon::parse($data['webPublicationDate']),
            'source' => 'The Guardian', // this origin does not have source
            'authors' => ['The Guardian'], // this origin does not have authors
            'image' => null, // this origin does not have image
            'url' => $data['webUrl'] ?? null,
            'origin' => $origin,
            'categories' => [],
        ];
        if(isset($data['sectionName'])){
            $article['categories'][] = $data['sectionName'];
        }
        if(isset($data['pillarName'])){
            $article['categories'][] = $data['pillarName'];
        }
        return $article;
    }
}
