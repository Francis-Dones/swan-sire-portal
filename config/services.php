<?php

return [
    'external_api' => [
        'base_url' => env('EXTERNAL_API_BASE_URL', 'http://10.174.204.52:8000/api'),
        'timeout' => env('EXTERNAL_API_TIMEOUT', 30),
    ],
];
