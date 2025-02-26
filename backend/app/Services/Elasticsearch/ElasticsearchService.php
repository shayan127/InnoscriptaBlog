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
                'published_at' => $blog->published_at,
                'source'    => $blog->source,
                'origin'    => $blog->origin,
                'authors'    => implode(',',$blog->authors),
                'categories' => implode(',', $blog->categories()->pluck('name')->toArray()),
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

    public function searchBlogs($query, $from, $to, $category, $source, $origin, $preferredCategories, $preferredSources, $preferredAuthors)
    {
        $esQuery = [
            'index' => 'blogs',
            'body' => [
                'size' => 1000,
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => [],
                        'should' => [],
                        'minimum_should_match' => 0,
                    ],
                ],
                'sort' => [
                    '_score' => ['order' => 'desc'],
                    'published_at' => ['order' => 'desc'],
                ],
            ],
        ];

        if (!empty($query)) {
            $esQuery['body']['query']['bool']['filter'][] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^2', 'content'],
                    'fuzziness' => 'AUTO',
                ],
            ];
        }

        if (!empty($from) || !empty($to)) {
            $rangeFilter = ['range' => ['published_at' => []]];
            if (!empty($from)) {
                $rangeFilter['range']['published_at']['gte'] = $from;
            }
            if (!empty($to)) {
                $rangeFilter['range']['published_at']['lte'] = $to;
            }
            $esQuery['body']['query']['bool']['filter'][] = $rangeFilter;
        }

        if (!empty($category)) {
            $esQuery['body']['query']['bool']['filter'][] = [
                'terms' => ['categories_ids' => (array) $category],
            ];
        }

        if (!empty($source)) {
            $esQuery['body']['query']['bool']['filter'][] = [
                'match' => ['source' => $source],
            ];
        }

        if (!empty($origin)) {
            $esQuery['body']['query']['bool']['filter'][] = [
                'match' => ['origin' => $origin],
            ];
        }

        if (!empty($preferredCategories)) {
            $preferredCategoriesString = '*' . implode('* OR *', $preferredCategories) . '*';
            $esQuery['body']['query']['bool']['should'][] = [
                'query_string' => [
                    'default_field' => 'categories',
                    'query' => $preferredCategoriesString,
                ],
            ];
        }

        if (!empty($preferredSources)) {
            array_walk($preferredSources, function(&$value)
            {
                $value = strtolower($value);
            });
            $esQuery['body']['query']['bool']['should'][] = [
                'terms' => [
                    'source' => $preferredSources,
                    'boost' => 1,
                ],
            ];
        }

        if (!empty($preferredAuthors)) {
            $preferredAuthorsString = '*' . implode('* OR *', $preferredAuthors) . '*';
            $esQuery['body']['query']['bool']['should'][] = [
                'query_string' => [
                    'default_field' => 'authors',
                    'query' => $preferredAuthorsString,
                ],
            ];
        }

        $esQuery['body']['query']['bool']['should'][] = [
            'range' => [
                'published_at' => [
                    'boost' => 2,
                    'gte' => 'now-7d/d',
                ],
            ],
        ];

        if (!empty($preferredCategories) || !empty($preferredSources) || !empty($preferredAuthors)) {
            $esQuery['body']['query']['bool']['minimum_should_match'] = 1;
        }
        try{
            $response = $this->client->search($esQuery);
            return $response['hits']['hits'] ?? [];
        } catch (\Exception $e){
            logger($e);
        }
        return [];
    }

}
