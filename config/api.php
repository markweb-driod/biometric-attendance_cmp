<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for third-party API access including rate limits,
    | authentication, and response formatting.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | API Key Settings
    |--------------------------------------------------------------------------
    */
    'key_prefix' => env('API_KEY_PREFIX', 'sk_'),
    'key_length' => 64,

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'default_rate_limit' => [
        'per_minute' => env('API_RATE_LIMIT_PER_MINUTE', 60),
        'per_hour' => env('API_RATE_LIMIT_PER_HOUR', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Format
    |--------------------------------------------------------------------------
    */
    'default_response_format' => 'simple', // simple or detailed

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    */
    'cors' => [
        'allowed_origins' => env('API_CORS_ORIGINS', '*'),
        'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-API-Key'],
        'max_age' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'eligibility_ttl' => env('API_CACHE_ELIGIBILITY_TTL', 300), // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhooks' => [
        'enabled' => env('API_WEBHOOKS_ENABLED', false),
        'timeout' => env('API_WEBHOOK_TIMEOUT', 5), // seconds
        'retry_attempts' => env('API_WEBHOOK_RETRY_ATTEMPTS', 3),
    ],
];

