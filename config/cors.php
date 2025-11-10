<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_origins' => [
        'https://admin.danieleshopdemo.com',
        'https://danieleshopdemo.com',
        'https://www.danieleshopdemo.com',
    ],

    'supports_credentials' => true,
];
