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
    // Admin (back-office) — self-contained OTP auth, BREAD CRUD.
    // ---------------------------------------------------------------------
    'admin' => [
        'route_prefix' => env('CMS_ADMIN_PREFIX', 'admin'),
        'brand' => 'TAVP CMS',
        'otp_ttl_minutes' => 10,

        // Passwordless OTP is restricted to these e-mails.
        'emails' => array_filter(array_map('trim', explode(',', (string) env('CMS_ADMIN_EMAILS', '')))),

        // RBAC-lite: map an allowed e-mail to a role. Roles map to the
        // permissions below. Unknown e-mails default to "editor".
        'roles' => [
            // 'admin@site.com' => 'admin',
        ],
        'permissions' => [
            'admin' => ['content.*', 'taxonomy.*', 'media.*', 'menu.*', 'settings.*', 'webhook.*', 'api.*'],
            'editor' => ['content.*', 'taxonomy.*', 'media.*', 'menu.*'],
            'author' => ['content.create', 'content.edit', 'content.delete', 'media.*'],
        ],
    ],

    'mail' => [
        'driver' => env('CMS_MAIL_DRIVER', 'smtp'),
        'host' => env('CMS_MAIL_HOST', '127.0.0.1'),
        'port' => (int) env('CMS_MAIL_PORT', 1025),
        'username' => env('CMS_MAIL_USERNAME', ''),
        'password' => env('CMS_MAIL_PASSWORD', ''),
        'from' => env('CMS_MAIL_FROM', 'noreply@tavp.web.id'),
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
        // Generate responsive derivatives on upload (requires GD/Imagick).
        'responsive' => false,
    ],

    // ---------------------------------------------------------------------
    // Theming — Volt + Tailwind front-end themes under /themes.
    // ---------------------------------------------------------------------
    'theme' => [
        'active' => env('CMS_THEME', 'default'),
        'path' => base_path('themes'),
    ],

    // ---------------------------------------------------------------------
    // Caching — wraps the storage layer so reads are cheap.
    // driver: "file" | "array" (array = request-only, good for dev).
    // ---------------------------------------------------------------------
    'cache' => [
        'enabled' => (bool) env('CMS_CACHE_ENABLED', true),
        'driver' => env('CMS_CACHE_DRIVER', 'file'),
        'path' => storage_path('cms/cache'),
        'ttl' => (int) env('CMS_CACHE_TTL', 300), // seconds
    ],

    // ---------------------------------------------------------------------
    // Taxonomy — categories (hierarchical) + tags (flat).
    // ---------------------------------------------------------------------
    'taxonomy' => [
        'enabled' => true,
        'types' => ['category', 'tag'],
        'hierarchical' => ['category'],
    ],

    // ---------------------------------------------------------------------
    // Revisions — keep a point-in-time snapshot on every save.
    // ---------------------------------------------------------------------
    'revisions' => [
        'enabled' => true,
        'limit' => (int) env('CMS_REVISIONS_LIMIT', 50),
        'path' => storage_path('cms/revisions'),
    ],

    // ---------------------------------------------------------------------
    // Search — simple in-process full-text over title/body/excerpt.
    // ---------------------------------------------------------------------
    'search' => [
        'enabled' => true,
        'fields' => ['title', 'body', 'excerpt', 'slug'],
    ],

    // ---------------------------------------------------------------------
    // Headless REST API.
    // ---------------------------------------------------------------------
    'api' => [
        'enabled' => true,
        'prefix' => env('CMS_API_PREFIX', 'api/cms'),
        'tokens' => array_filter(array_map('trim', explode(',', (string) env('CMS_API_TOKENS', '')))),
        'tokens_file' => storage_path('cms/api_tokens.json'),
        'per_page' => (int) env('CMS_API_PER_PAGE', 15),
        'max_per_page' => 100,
    ],

    // ---------------------------------------------------------------------
    // Webhooks — POST to external URLs on content events.
    // Enable when you have a webhook receiver to configure.
    // ---------------------------------------------------------------------
    'webhooks' => [
        'enabled' => false,
        'timeout' => 5,
        'events' => ['content.created', 'content.updated', 'content.deleted'],
    ],

    // ---------------------------------------------------------------------
    // SEO — per-record meta + sitemap.
    // ---------------------------------------------------------------------
    'seo' => [
        'enabled' => true,
        'sitemap_path' => '/sitemap.xml',
        'default_title_suffix' => '',
    ],

    // ---------------------------------------------------------------------
    // Publishing — scheduled posts via published_at field.
    // Requires: `php bin/cms cms:publish` cron job (not yet implemented).
    // ---------------------------------------------------------------------
    'publishing' => [
        'enabled' => false,
        'sleep_until_field' => 'published_at',
    ],

    // ---------------------------------------------------------------------
    // Analytics — page view tracking, fraud detection, experiments.
    // Requires: tavp/tavp-analytics package.
    // ---------------------------------------------------------------------
    'analytics' => [
        'enabled' => false,
        'track_page_views' => true,
        'track_events' => true,
        'fraud_detection' => false,
        'dashboard_enabled' => true,
    ],

    // ---------------------------------------------------------------------
    // Built-in content types. These are the defaults; more can be defined
    // from the admin UI (BREAD-style) and stored via the active driver.
    // SEO fields (seo_title, seo_description) are auto-appended when
    // cms.seo.enabled is true.
    // ---------------------------------------------------------------------
    'content_types' => [
        'page' => [
            'label' => 'Pages',
            'singular' => 'Page',
            'icon' => 'document',
            'route' => '/{slug}',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'required' => true, 'rules' => ['max:200']],
                ['name' => 'slug', 'type' => 'slug', 'from' => 'title', 'rules' => ['unique']],
                ['name' => 'body', 'type' => 'richtext'],
                ['name' => 'status', 'type' => 'select', 'options' => ['draft', 'published'], 'default' => 'draft'],
                ['name' => 'featured_image', 'type' => 'media'],
                ['name' => 'seo_title', 'type' => 'text', 'help' => 'Override the <title> tag (max 60 chars)'],
                ['name' => 'seo_description', 'type' => 'textarea', 'help' => 'Meta description for search engines (max 160 chars)'],
                ['name' => 'categories', 'type' => 'relation', 'relation' => 'category', 'multiple' => true],
                ['name' => 'tags', 'type' => 'relation', 'relation' => 'tag', 'multiple' => true],
            ],
        ],
        'post' => [
            'label' => 'Posts',
            'singular' => 'Post',
            'icon' => 'newspaper',
            'route' => '/blog/{slug}',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'required' => true, 'rules' => ['max:200']],
                ['name' => 'slug', 'type' => 'slug', 'from' => 'title', 'rules' => ['unique']],
                ['name' => 'excerpt', 'type' => 'textarea', 'rules' => ['max:500']],
                ['name' => 'body', 'type' => 'richtext'],
                ['name' => 'status', 'type' => 'select', 'options' => ['draft', 'published', 'scheduled'], 'default' => 'draft'],
                ['name' => 'featured_image', 'type' => 'media'],
                ['name' => 'published_at', 'type' => 'datetime'],
                ['name' => 'seo_title', 'type' => 'text', 'help' => 'Override the <title> tag (max 60 chars)'],
                ['name' => 'seo_description', 'type' => 'textarea', 'help' => 'Meta description for search engines (max 160 chars)'],
                ['name' => 'categories', 'type' => 'relation', 'relation' => 'category', 'multiple' => true],
                ['name' => 'tags', 'type' => 'relation', 'relation' => 'tag', 'multiple' => true],
            ],
        ],
    ],
];
