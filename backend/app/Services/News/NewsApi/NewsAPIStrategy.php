<?php

namespace App\Services\News\NewsApi;

use App\Services\News\AbstractNewsStrategy;
use App\Services\News\NewsAdapterInterface;
use App\Services\News\NYT\NYTAdapter;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class NewsAPIStrategy extends AbstractNewsStrategy
{
    public string $origin = 'NewsAPI';
    protected int $max_per_page = 100;
    public function __construct(array $config, NewsAdapterInterface $adapter)
    {
        parent::__construct($config, $adapter);
        $this->limit = min(2000, $config['limit']);  // base on the document
        $this->pages = $this->limit % 100 == 0 ? $this->limit / 100 : $this->limit / 100 + 1; // base on the document
    }

    protected function isDuplicate($data): bool
    {
        $publishedAt = Carbon::parse($data['publishedAt']);
        if (isset($this->config['last_news_datetime']) && $publishedAt->lessThanOrEqualTo($this->config['last_news_datetime'])) {
            return true;
        }
        return false;
    }

    protected function fetch($page): array
    {
        $response = Http::get($this->config['base_url'] . '/v2/everything', [
            'apiKey' => $this->config['api_key'],
            'domains' => 'techcrunch.com,thenextweb.com',
            'sortBy' => 'publishedAt',
            'pageSize' => $this->max_per_page,
            'page' => $page,
        ]);

        if ($response->successful() && !isset($data['articles'])) {
            $data = $response->json();
            return $data['articles'];
        }

        return [];
    }
}
