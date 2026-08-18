<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'olamaps' => [
        'api_key' => env('OLA_MAPS_API_KEY', env('OLA_MAPS_CLIENT_ID', '')),
        'client_secret' => env('OLA_MAPS_CLIENT_SECRET', env('OLA_MAPS_SECRET_KEY', '')),
        'tiles_url' => env('OLA_MAPS_TILES_URL', 'https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard/style.json'),
        'api_base_url' => env('OLA_MAPS_BASE_URL', 'https://api.olamaps.io'),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_KEY', env('GOOGLE_MAPS_API_KEY', '')),
    ],

];
