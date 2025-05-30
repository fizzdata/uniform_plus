<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'graphql', 'graphql-playground'],

    'allowed_methods' => ['GET', 'POST', 'OPTIONS', 'PUT', 'DELETE'],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],

    'allowed_origins' => ['http://localhost:5173', 'https://front-uniformplus.fizzdata.com'], 
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
