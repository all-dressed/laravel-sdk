<?php

return [
    'account' => env('ALLDRESSED_ACCOUNT_ID'),

    'api' => [
        'base' => env('ALLDRESSED_API_BASE', 'https://api.alldressed.io'),

        'key' => env('ALLDRESSED_API_KEY'),
    ],

    'debug' => [
        'log' => env('ALLDRESSED_DEBUG_LOG', false),
    ],

    'request' => [
        'verify' => env('ALLDRESSED_REQUEST_VERIFY_SSL', true),
    ],
];
