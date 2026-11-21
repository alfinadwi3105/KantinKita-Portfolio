<?php

return [
    'host' => env('RABBITMQ_HOST', '127.0.0.1'),
    'port' => (int) env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'pass' => env('RABBITMQ_PASS', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),

    // exchange dan queue tempat aplikasi ini MENDENGARKAN
    'dengar' => env('RABBITMQ_DENGAR', 'pesanan'),
    'queue' => env('RABBITMQ_QUEUE', 'dapur'),
];
