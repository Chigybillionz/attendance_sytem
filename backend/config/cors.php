<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    tells your Laravel backend which frontend URLs are allowed to make requests to it.
    */

    'paths' => ['api/*', 
    'sanctum/csrf-cookie',  
    'login',
     ],

    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_filter(array_map(
        static fn ($origin) => trim($origin),
        explode(',', (string) env(
            'CORS_ALLOWED_ORIGINS',
            implode(',', [
                'http://localhost:3000',
                'http://localhost:5173',
                'http://127.0.0.1:3000',
                'http://127.0.0.1:5173',
                env('FRONTEND_URL', ''),
            ])
        ))
    ))),


    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
