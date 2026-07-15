<?php

return [
    'enabled' => (bool) env('CMS_SEO_ENABLED', true),

    'meta' => [
        'title_suffix' => env('APP_NAME', 'TAVP'),
        'separator' => ' | ',
        'description_max' => 160,
        'title_max' => 60,
    ],

    'sitemap' => [
        'enabled' => true,
        'path' => '/sitemap.xml',
        'max_urls' => 50000,
        'cache_ttl' => 3600,
        'ping_google' => true,
        'ping_bing' => true,
    ],

    'robots' => [
        'enabled' => true,
        'path' => '/robots.txt',
        'allow' => ['/'],
        'disallow' => ['/admin', '/api'],
        'sitemap_url' => '/sitemap.xml',
    ],

    'open_graph' => [
        'enabled' => true,
        'default_image' => '',
        'default_type' => 'website',
        'facebook_app_id' => '',
    ],

    'twitter' => [
        'enabled' => true,
        'card_type' => 'summary_large_image',
        'site_handle' => '',
        'creator_handle' => '',
    ],

    'schemas' => [
        'enabled' => true,
        'types' => [
            'page' => 'WebPage',
            'post' => 'Article',
            'product' => 'Product',
        ],
        'organization' => [
            'name' => '',
            'logo' => '',
            'url' => '',
        ],
    ],

    'rss' => [
        'enabled' => true,
        'path' => '/feed',
        'title' => '',
        'description' => '',
        'limit' => 20,
    ],

    'webmaster' => [
        'google_verification' => env('GOOGLE_SITE_VERIFICATION', ''),
        'bing_verification' => env('BING_SITE_VERIFICATION', ''),
        'yandex_verification' => env('YANDEX_SITE_VERIFICATION', ''),
    ],

    'analytics' => [
        'google_analytics_id' => env('GOOGLE_ANALYTICS_ID', ''),
        'google_tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID', ''),
    ],

    'redirects' => [
        'enabled' => true,
        'ignore_case' => true,
        'track_hits' => true,
        'ignore_trailing_slash' => true,
    ],

    'analyzer' => [
        'enabled' => true,
        'min_title_length' => 30,
        'max_title_length' => 60,
        'min_description_length' => 120,
        'max_description_length' => 160,
        'min_keyword_density' => 1.0,
        'max_keyword_density' => 3.0,
    ],

    'social_sharing' => [
        'enabled' => true,
        'platforms' => ['twitter', 'facebook', 'linkedin', 'whatsapp', 'telegram'],
    ],
];
