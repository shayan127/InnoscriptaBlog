<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Services\News\FetchNewsJob;
use App\Services\News\NewsFactory;
use Illuminate\Console\Command;
use Carbon\Carbon;

class FetchNewsCommand extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news from active providers';

    public function handle()
    {
        $newsConfig = config('news', []);

        foreach ($newsConfig['active_origins'] as $origin) {
            $config = $newsConfig['origins'][$origin];

            $lastRun = cache("news_last_run_{$origin}", null);
            $interval = $config['interval'] ?? 60;

            if ($lastRun && Carbon::parse($lastRun)->addMinutes($interval)->isFuture()) {
                //continue;
            }

            $blog = $this->getLastBlogByOrigin($origin);
            $strategy = NewsFactory::create($origin, $config, $blog);

            if ($strategy) {
                FetchNewsJob::dispatch($strategy);
                cache(["news_last_run_{$origin}" => now()], now()->addMinutes($interval));
            }
        }

        $this->info('News fetching jobs dispatched successfully.');
    }

    private function getLastBlogByOrigin($origin)
    {
        return Blog::where('origin', $origin)->orderBy('id', 'desc')->first();
    }
}
