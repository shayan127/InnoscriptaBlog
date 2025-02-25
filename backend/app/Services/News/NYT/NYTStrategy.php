<?php

namespace App\Services\News\NYT;

use App\Services\News\AbstractNewsStrategy;
use App\Services\News\NewsAdapterInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class NYTStrategy extends AbstractNewsStrategy
{
    public string $origin = 'NYT';
    public function __construct(array $config, NewsAdapterInterface $adapter)
    {
        parent::__construct($config, $adapter);
        $this->limit = min(2000, $config['limit']);  // base on the document
        $this->pages = $this->limit % 10 == 0 ? $this->limit / 10 : $this->limit / 10 + 1; // base on the document
    }


    protected function isDuplicate($data): bool
    {
        $publishedAt = Carbon::parse($data['pub_date']);
        if (isset($this->config['last_news_datetime']) && $publishedAt->lessThanOrEqualTo($this->config['last_news_datetime'])) {
            return true;
        }
        return false;
    }

    protected function fetch($page): array
    {
        $response = Http::get($this->config['base_url'] . '/svc/search/v2/articlesearch.json', [
            'api-key' => $this->config['api_key'],
            'sort' => 'newest',
            'page' => $page,
        ]);

        if ($response->successful() && !isset($data['response']['docs'])) {
            $data = $response->json();
            return $data['response']['docs'];
        }

        return [];
    }


}
