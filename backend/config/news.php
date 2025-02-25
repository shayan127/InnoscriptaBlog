<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Number of attempts
    |--------------------------------------------------------------------------
    */
    'tries' => 3, // todo

    /*
    |--------------------------------------------------------------------------
    | Active Providers
    |--------------------------------------------------------------------------
    */

    'active_origins' => [
        'NewsAPI',
        'NYT',
        'Guardian',
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
            'limit' => 100,
            'interval' => 30,
        ],
        'NYT' => [
            'api_key' => env('NYT_API_KEY'),
            'base_url' => 'https://api.nytimes.com',
            'limit' => 200,
            'interval' => 30,
        ],
        'Guardian' => [
            'api_key' => env('GUARDIAN_API_KEY'),
            'base_url' => 'https://content.guardianapis.com',
            'limit' => 1000,
            'interval' => 30,
        ],
    ],

];
