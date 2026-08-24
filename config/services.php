<?php

return [
    'external_api' => [
        'base_url' => env('EXTERNAL_API_BASE_URL', 'https://api.sire2.swan-manila.com/api'),
        'timeout' => env('EXTERNAL_API_TIMEOUT', 30),
    ],
];
