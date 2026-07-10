<?php

declare(strict_types=1);

/**
 * TAVP CMS configuration.
 *
 * The storage layer is driver-based: you are not locked into a database.
 * Use "database" for a WordPress/Voyager-style setup, or "flatfile" for a
 * Statamic-style git-friendly setup — the rest of the CMS behaves the same.
 */
return [
    // ---------------------------------------------------------------------
    // Storage driver: "database" | "flatfile"
    // ---------------------------------------------------------------------
    'storage' => env('CMS_STORAGE', 'database'),

    'drivers' => [
        'database' => [
            // Uses the tavp-core database connection + Phalcon ORM.
            'connection' => env('CMS_DB_CONNECTION', 'default'),
        ],

        'flatfile' => [
            // Content stored as Markdown + YAML front matter under this path.
            'path' => base_path('content'),
            'format' => 'markdown', // markdown | yaml | json
        ],
    ],

    // ---------------------------------------------------------------------
    // Admin (back-office) — built on top of tavphub, auth via tavpid.
    // ---------------------------------------------------------------------
    'admin' => [
        'route_prefix' => env('CMS_ADMIN_PREFIX', 'admin'),
        'brand' => 'TAVP CMS',
        'auth_guard' => 'tavpid',
    ],

    // ---------------------------------------------------------------------
    // Media library.
    // ---------------------------------------------------------------------
    'media' => [
        'disk' => env('CMS_MEDIA_DISK', 'public'),
        'path' => 'uploads',
        'max_size' => 10 * 1024 * 1024, // 10 MB
        'allowed' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'mp4', 'webm'],
        'image_sizes' => [
            'thumb' => [150, 150],
            'medium' => [640, 640],
            'large' => [1280, 1280],
        ],
    ],

    // ---------------------------------------------------------------------
    // Theming — Volt + Tailwind front-end themes under /themes.
    // ---------------------------------------------------------------------
    'theme' => [
        'active' => env('CMS_THEME', 'default'),
        'path' => base_path('themes'),
    ],

    // ---------------------------------------------------------------------
    // Built-in content types. These are the defaults; more can be defined
    // from the admin UI (BREAD-style) and stored via the active driver.
    // ---------------------------------------------------------------------
    'content_types' => [
        'page' => [
            'label' => 'Pages',
            'singular' => 'Page',
            'icon' => 'document',
            'route' => '/{slug}',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'required' => true],
                ['name' => 'slug', 'type' => 'slug', 'from' => 'title'],
                ['name' => 'body', 'type' => 'richtext'],
                ['name' => 'status', 'type' => 'select', 'options' => ['draft', 'published'], 'default' => 'draft'],
                ['name' => 'featured_image', 'type' => 'media'],
            ],
        ],
        'post' => [
            'label' => 'Posts',
            'singular' => 'Post',
            'icon' => 'newspaper',
            'route' => '/blog/{slug}',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'required' => true],
                ['name' => 'slug', 'type' => 'slug', 'from' => 'title'],
                ['name' => 'excerpt', 'type' => 'textarea'],
                ['name' => 'body', 'type' => 'richtext'],
                ['name' => 'status', 'type' => 'select', 'options' => ['draft', 'published'], 'default' => 'draft'],
                ['name' => 'featured_image', 'type' => 'media'],
                ['name' => 'published_at', 'type' => 'datetime'],
            ],
        ],
    ],
];
