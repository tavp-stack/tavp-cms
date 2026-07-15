<?php

return [
    'enabled' => (bool) env('CMS_REDIRECTS_ENABLED', true),

    'table' => 'redirects',

    'defaults' => [
        'status_code' => 301,
        'ignore_case' => true,
        'ignore_trailing_slash' => true,
    ],

    'patterns' => [
        [
            'from' => '/old-blog/{slug}',
            'to' => '/blog/{slug}',
            'status' => 301,
        ],
    ],

    'auto_redirect' => [
        'enabled' => true,
        'on_slug_change' => true,
        'on_delete' => true,
        'default_status' => 301,
    ],
];
