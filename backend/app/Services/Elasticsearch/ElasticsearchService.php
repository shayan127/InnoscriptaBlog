<?php

namespace App\Services\Elasticsearch;


use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', '')])
            ->build();
    }

    public function indexBlog($blog)
    {
        return $this->client->index([
            'index' => 'blogs',
            'id'    => $blog->id,
            'body'  => [
                'title'   => $blog->title,
                'content' => $blog->content,
                'published_at' => $blog->content,
                'source'    => $blog->source,
                'authors'    => $blog->authors,
                'categories' => $blog->categories()->pluck('name')->toArray(),
                'categories_ids' => $blog->categories()->pluck('category_id')->toArray(),
            ],
        ]);
    }

    public function deleteBlog($blogId)
    {
        return $this->client->delete([
            'index' => 'blogs',
            'id'    => $blogId,
        ]);
    }
}
