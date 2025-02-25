<?php

namespace App\Services\News;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchNewsJob implements ShouldQueue
{
    use Queueable;

    protected AbstractNewsStrategy $strategy;

    public function __construct(AbstractNewsStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function handle()
    {
        $this->strategy->handle();
        $articles = $this->strategy->getArticles();

        foreach ($articles as $article) {
            $blog = Blog::create($article);
            $categoryIds = [];
            foreach ($article['categories'] as $categoryName) {
                $category = Category::firstOrCreate(['name' => $categoryName]);
                $categoryIds[] = $category->id;
            }
            $blog->categories()->attach($categoryIds);
        }
    }
}
