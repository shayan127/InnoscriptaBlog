<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Number of attempts
    |--------------------------------------------------------------------------
    */
    'tries' => 3,

    /*
    |--------------------------------------------------------------------------
    | Active Providers
    |--------------------------------------------------------------------------
    */

    'active_origins' => [
        'NewsAPI',
        'NYT',
    ],

    /*
    |--------------------------------------------------------------------------
    | News provider configs
    |--------------------------------------------------------------------------    |
    */

    'origins' => [
        'NewsAPI' => [
            'api_key' => env('NEWS_API_KEY'),
            'base_url' => 'https://newsapi.org',
            'limit' => 30,
            'interval' => 30,
        ],
        'NYT' => [
            'api_key' => env('NYT_API_KEY'),
            'base_url' => 'https://api.nytimes.com',
            'limit' => 30,
            'interval' => 30,
        ],
    ],

];
