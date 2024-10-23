<?php

return [
    'cache' => [
        'key_prefix' => 'instagram_',
        'duration' => 60 * 60 * 24, // 1 day
    ],

    'credentials' => [
        'username' => env('STATAMIC_INSTAGRAM_USERNAME'),
        'password' => env('STATAMIC_INSTAGRAM_PASSWORD'),
    ],
];
