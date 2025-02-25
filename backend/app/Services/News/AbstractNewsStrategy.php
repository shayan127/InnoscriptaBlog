<?php

namespace App\Services\News;

use App\Services\News\NewsApi\NewsAPIAdapter;
use App\Services\News\NewsApi\NewsAPIStrategy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

abstract class AbstractNewsStrategy implements ShouldQueue
{
    use Queueable;
    public string $origin;
    protected array $config;
    protected int $limit = 100;
    protected int $pages = 1;
    private array $articles = [];
    protected mixed $adapter;

    public function __construct(array $config, NewsAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->limit = $config['limit'];
        $this->config = $config;
    }

    public function handle(): void
    {
        $this->prepare();
    }

    protected function prepare(): void
    {
        // todo : It's better to add each one to queue
        for($i = 1; $i <= $this->pages; $i++) {
            $fetchedData = $this->fetch($i);

            foreach ($fetchedData as $article) {
                // in order to avoid duplication
                if ($this->isDuplicate($article)) {
                    break 2; // break for
                }
                $this->articles[] = $this->adapter->transform($article, $this->origin);
            }
        }
    }

    public function getArticles(): array
    {
        return $this->articles;
    }

    abstract protected function fetch($page);
    abstract protected function isDuplicate($data);
}
