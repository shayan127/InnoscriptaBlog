<?php

namespace App\Services\News\Guardian;

use App\Services\News\AbstractNewsStrategy;
use App\Services\News\NewsAdapterInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GuardianStrategy extends AbstractNewsStrategy
{
    public string $origin = 'Guardian';
    protected int $max_articles = 38000;
    protected int $max_per_page = 200;
    public function __construct(array $config, NewsAdapterInterface $adapter)
    {
        parent::__construct($config, $adapter);
        $this->limit = min($this->max_articles, $config['limit']);  // base on the document
        $this->pages = $this->limit % 200 == 0 ? $this->limit / 200 : $this->limit / 200 + 1; // base on the document
    }

    protected function isDuplicate($data): bool
    {
        $publishedAt = Carbon::parse($data['webPublicationDate']);
        if (isset($this->config['last_news_datetime']) && $publishedAt->lessThanOrEqualTo($this->config['last_news_datetime'])) {
            return true;
        }
        return false;
    }

    protected function fetch($page): array
    {
        sleep(10);
        $response = Http::get($this->config['base_url'] . '/search', [
            'api-key' => $this->config['api_key'],
            'page' => $page,
            'page-size' => $this->max_per_page,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['response']['results'] ?? [];
        }

        return [];
    }


}
