<?php
namespace App\Services\Elasticsearch;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
class RemoveBlogFromElasticsearch implements ShouldQueue
{
    use Queueable;

    protected $blogId;

    public function __construct($blogId)
    {
        $this->blogId = $blogId;
    }

    public function handle(ElasticsearchService $elasticsearchService)
    {
        $elasticsearchService->deleteBlog($this->blogId);
    }
}
