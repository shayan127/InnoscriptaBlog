<?php
namespace App\Services\Elasticsearch;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncBlogToElasticsearch implements ShouldQueue
{
    use Queueable;

    protected $blog;

    public function __construct($blog)
    {
        $this->blog = $blog;
    }

    public function handle(ElasticsearchService $elasticsearchService)
    {
        $elasticsearchService->indexBlog($this->blog);
    }
}
