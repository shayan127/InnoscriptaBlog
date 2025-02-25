<?php

namespace App\Observers;

use App\Models\Blog;
use App\Services\Elasticsearch\RemoveBlogFromElasticsearch;
use App\Services\Elasticsearch\SyncBlogToElasticsearch;

class BlogObserver
{
    public function updated(Blog $blog)
    {
        SyncBlogToElasticsearch::dispatch($blog);
    }

    public function deleted(Blog $blog)
    {
        RemoveBlogFromElasticsearch::dispatch($blog->id);
    }
}

