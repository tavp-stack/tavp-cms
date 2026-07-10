<?php

declare(strict_types=1);

namespace Tavp\Cms;

use Tavp\Cms\Api\ApiController;
use Tavp\Cms\Api\ApiTokenService;
use Tavp\Cms\Bread\BreadManager;
use Tavp\Cms\Cache\CachedContentStore;
use Tavp\Cms\Publishing\PublishScheduler;
use Tavp\Cms\Revisions\RevisionStore;
use Tavp\Cms\Search\SearchEngine;
use Tavp\Cms\Seo\SitemapController;
use Tavp\Cms\Storage\ContentStore;
use Tavp\Cms\Storage\DatabaseStore;
use Tavp\Cms\Storage\FlatFileStore;
use Tavp\Cms\Taxonomy\DatabaseTaxonomyFactory;
use Tavp\Cms\Taxonomy\TaxonomyManager;
use Tavp\Cms\Theme\ThemeManager;
use Tavp\Cms\Webhooks\DatabaseWebhookFactory;
use Tavp\Cms\Webhooks\WebhookManager;
use Tavp\Cms\Auth\RbacGuard;
use Tavp\Core\Module\ServiceProvider;

/**
 * Wires TAVP CMS into a TAVP application.
 *
 * v0.2.0 — adds taxonomy, revisions, search, caching, webhooks, headless API,
 * scheduled publishing, SEO/sitemap, and RBAC-lite.
 */
class CmsServiceProvider implements ServiceProvider
{
    public function register(): void
    {
        $app = app();

        // --- Storage layer (base + optional cache decorator) ----------------
        $app->bind(ContentStore::class, fn () => $this->makeStore());

        // --- Taxonomy -------------------------------------------------------
        if (config('cms.taxonomy.enabled', true)) {
            $app->bind(TaxonomyManager::class, function () {
                $db = app('db');
                return DatabaseTaxonomyFactory::buildDatabaseTaxonomy($db);
            });
        }

        // --- Revisions ------------------------------------------------------
        if (config('cms.revisions.enabled', true)) {
            $app->bind(RevisionStore::class, fn () => new RevisionStore(
                path: (string) config('cms.revisions.path', storage_path('cms/revisions')),
                limit: (int) config('cms.revisions.limit', 50),
            ));
        }

        // --- Webhooks -------------------------------------------------------
        if (config('cms.webhooks.enabled', true)) {
            $app->bind(WebhookManager::class, function () {
                $db = app('db');
                return DatabaseWebhookFactory::buildDatabaseWebhooks(
                    $db,
                    (int) config('cms.webhooks.timeout', 5),
                );
            });
        }

        // --- BREAD Manager (with revisions + webhooks) ----------------------
        $app->bind(BreadManager::class, function ($app) {
            $revisions = config('cms.revisions.enabled', true)
                ? $app->getService(RevisionStore::class)
                : null;

            $webhooks = config('cms.webhooks.enabled', true)
                ? $app->getService(WebhookManager::class)
                : null;

            $manager = new BreadManager(
                store: $app->getService(ContentStore::class),
                revisions: $revisions,
                webhooks: $webhooks,
            );
            $manager->registerFromConfig((array) config('cms.content_types', []));

            return $manager;
        });

        // --- Search Engine --------------------------------------------------
        if (config('cms.search.enabled', true)) {
            $app->bind(SearchEngine::class, function ($app) {
                return new SearchEngine(
                    bread: $app->getService(BreadManager::class),
                    searchFields: (array) config('cms.search.fields', ['title', 'body', 'excerpt', 'slug']),
                );
            });
        }

        // --- Theme Manager --------------------------------------------------
        $app->bind(ThemeManager::class, fn () => new ThemeManager(
            (string) config('cms.theme.path', base_path('themes')),
            (string) config('cms.theme.active', 'default'),
        ));

        // --- API Token Service ----------------------------------------------
        $app->bind(ApiTokenService::class, fn () => new ApiTokenService(
            configTokens: (array) config('cms.api.tokens', []),
            tokensFile: config('cms.api.tokens_file'),
        ));

        // --- RBAC Guard -----------------------------------------------------
        $app->bind(RbacGuard::class, fn () => new RbacGuard(
            roles: (array) config('cms.admin.roles', []),
            permissions: (array) config('cms.admin.permissions', []),
        ));

        // --- Publish Scheduler ----------------------------------------------
        $app->bind(PublishScheduler::class, function ($app) {
            return new PublishScheduler($app->getService(BreadManager::class));
        });

        // --- Analytics (optional, if tavp-analytics is installed) -----------
        if (config('cms.analytics.enabled', false) && class_exists(\Tavp\Analytics\AnalyticsManager::class)) {
            $app->bind(\Tavp\Analytics\AnalyticsManager::class, fn () => new \Tavp\Analytics\AnalyticsManager());
        }
    }

    public function boot(): void
    {
        // Register CMS content types as BREAD resources in the tavphub admin.
    }

    public function loadRoutes(): void
    {
        require __DIR__ . '/../routes/web.php';

        // SEO sitemap route.
        if (config('cms.seo.enabled', true)) {
            $sitemapPath = (string) config('cms.seo.sitemap_path', '/sitemap.xml');
            $sitemapPath = '/' . ltrim($sitemapPath, '/');

            if (isset($router)) {
                $router->get($sitemapPath, [SitemapController::class, '__invoke']);
            }
        }

        // Headless API routes.
        if (config('cms.api.enabled', true) && isset($router)) {
            \Tavp\Cms\Api\ApiModule::routes($router);
        }
    }

    public function loadMigrations(): void
    {
        // Migrations live in database/migrations and are discovered by tavp-core.
    }

    public function loadViews(): void
    {
        // Theme views are resolved by the ViewFactory using the active theme path.
    }

    private function makeStore(): ContentStore
    {
        $driver = (string) config('cms.storage', 'database');

        $store = match ($driver) {
            'flatfile' => new FlatFileStore(
                basePath: (string) config('cms.drivers.flatfile.path', base_path('content')),
            ),
            default => new DatabaseStore(
                connection: fn () => app('db'),
            ),
        };

        // Wrap with cache decorator if enabled.
        if (config('cms.cache.enabled', true)) {
            return new CachedContentStore(
                inner: $store,
                cachePath: (string) config('cms.cache.path', storage_path('cms/cache')),
                ttl: (int) config('cms.cache.ttl', 300),
            );
        }

        return $store;
    }
}
