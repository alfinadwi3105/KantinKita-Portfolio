
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for a variety of credentials.
    |
    */

    'api' => [
        'key' => env('API_KEY'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cuaca' => [
        'url' => env('CUACA_URL', 'https://api.open-meteo.com/v1'),
        'lat' => env('CUACA_LAT', -6.973),
        'lon' => env('CUACA_LON', 107.630),
        'ttl' => env('CUACA_TTL', 600),
    ],

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'snap_url'   => env('MIDTRANS_SNAP_URL'),
        'api_url'    => env('MIDTRANS_API_URL'),
        'prefix'     => env('MIDTRANS_PREFIX', 'KF'),
    ],
];
