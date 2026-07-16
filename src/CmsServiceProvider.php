<?php

declare(strict_types=1);

namespace Tavp\Cms;

use Tavp\Cms\Bread\BreadManager;
use Tavp\Cms\Cache\CachedContentStore;
use Tavp\Cms\Publishing\PublishScheduler;
use Tavp\Cms\Revisions\RevisionStore;
use Tavp\Cms\Search\SearchEngine;
use Tavp\Cms\Seo\{
    AiMetaGenerator,
    BacklinkMonitor,
    ContentSyndicator,
    LinkChecker,
    RedirectManager,
    RobotsController,
    RssController,
    SchemaGenerator,
    SeoAdminController,
    SeoAnalyzer,
    SeoManager,
    SitemapController,
    SitemapGenerator,
    SocialSharing,
};
use Tavp\Cms\Storage\ContentStore;
use Tavp\Cms\Storage\DatabaseStore;
use Tavp\Cms\Storage\FlatFileStore;
use Tavp\Cms\Taxonomy\DatabaseTaxonomyFactory;
use Tavp\Cms\Taxonomy\TaxonomyManager;
use Tavp\Cms\Theme\ThemeManager;
use Tavp\Cms\Webhooks\DatabaseWebhookFactory;
use Tavp\Cms\Webhooks\WebhookManager;
use Tavp\Core\Module\ServiceProvider;

/**
 * Wires TAVP CMS into a TAVP application.
 *
 * Uses tavpid for auth/RBAC — no duplication.
 */
class CmsServiceProvider implements ServiceProvider
{
    public function register(): void
    {
        $app = app();

        // --- Storage layer ---------------------------------------------------
        $app->bind(ContentStore::class, fn () => $this->makeStore());

        // --- Auth (via tavpid) -----------------------------------------------
        $app->bind('tavpid.auth', function () {
            $otp = new \Tavp\Tavpid\Auth\OtpService(
                (int) config('cms.admin.otp_ttl_minutes', 10),
            );
            return new \Tavp\Tavpid\Auth\AuthService($otp, app('tavpid.user_provider'));
        });

        $app->bind('tavpid.user_provider', function () {
            return new \Tavp\Cms\Auth\CmsUserProvider();
        });

        // --- RBAC (via tavpid) -----------------------------------------------
        $app->bind('tavpid.rbac', function () {
            $rbac = new \Tavp\Tavpid\Rbac\AccessControl();
            // Load email-to-role mapping
            $emailRoles = (array) config('cms.admin.roles', []);
            foreach ($emailRoles as $email => $role) {
                $rbac->setUserRole($email, $role);
            }
            // Load role permissions
            $rbac->loadRoles((array) config('cms.admin.permissions', []));
            return $rbac;
        });

        // --- Token Service (via tavpid) -------------------------------------
        $app->bind('tavpid.token', function () {
            $secret = (string) config('app.key', 'tavp-secret-key-change-me');
            return new \Tavp\Tavpid\Auth\TokenService($secret);
        });

        // --- Taxonomy -------------------------------------------------------
        if (config('cms.taxonomy.enabled', true)) {
            // Load the factory function file (not auto-loaded by Composer).
            require_once __DIR__ . '/Taxonomy/DatabaseTaxonomyFactory.php';
            $app->bind(TaxonomyManager::class, function () use ($app) {
                $db = $app->getService('db');
                return \Tavp\Cms\Taxonomy\buildDatabaseTaxonomy($db);
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
        if (config('cms.webhooks.enabled', false)) {
            require_once __DIR__ . '/Webhooks/DatabaseWebhookFactory.php';
            $app->bind(WebhookManager::class, function () use ($app) {
                $db = $app->getService('db');
                return buildDatabaseWebhooks(
                    $db,
                    (int) config('cms.webhooks.timeout', 5),
                );
            });
        }

        // --- BREAD Manager --------------------------------------------------
        $app->bind(BreadManager::class, function ($app) {
            $revisions = config('cms.revisions.enabled', true)
                ? $app->getService(RevisionStore::class)
                : null;

            $webhooks = config('cms.webhooks.enabled', false)
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

        // --- Publish Scheduler ----------------------------------------------
        $app->bind(PublishScheduler::class, function ($app) {
            return new PublishScheduler($app->getService(BreadManager::class));
        });

        // --- Media Library --------------------------------------------------
        $app->bind(\Tavp\Cms\Media\MediaLibrary::class, fn () => new \Tavp\Cms\Media\MediaLibrary(
            config: (array) config('cms.media', []),
            persist: function (array $data) {
                $db = app('db');
                $now = date('Y-m-d H:i:s');
                $db->execute(
                    'INSERT INTO media (name, mime_type, path, size, created_at)
                     VALUES (:name, :mime_type, :path, :size, :created_at)',
                    ['name' => $data['name'], 'mime_type' => $data['mime_type'], 'path' => $data['path'], 'size' => $data['size'], 'created_at' => $now]
                );
                return (int) $db->lastInsertId();
            },
        ));

        // --- Settings -------------------------------------------------------
        $app->bind(\Tavp\Cms\Settings\Settings::class, function () {
            return new \Tavp\Cms\Settings\Settings(
                loader: function () {
                    $db = app('db');
                    $result = $db->query("SELECT `group`, `key`, value FROM settings", []);
                    $rows = $result->fetchAll();
                    $settings = [];
                    foreach ($rows as $row) {
                        $settings[$row['group'] . '.' . $row['key']] = $row['value'];
                    }
                    return $settings;
                },
                writer: function (string $key, mixed $value) {
                    $db = app('db');
                    $parts = explode('.', $key, 2);
                    $group = $parts[0] ?? 'general';
                    $keyName = $parts[1] ?? $key;
                    $now = date('Y-m-d H:i:s');
                    $result = $db->query("SELECT id FROM settings WHERE `group` = :group AND `key` = :key", ['group' => $group, 'key' => $keyName]);
                    $rows = $result->fetchAll();
                    if (!empty($rows)) {
                        $db->execute(
                            "UPDATE settings SET value = :value, updated_at = :updated_at WHERE `group` = :group AND `key` = :key",
                            ['value' => $value, 'updated_at' => $now, 'group' => $group, 'key' => $keyName]
                        );
                    } else {
                        $db->execute(
                            "INSERT INTO settings (`group`, `key`, value, type, created_at, updated_at)
                             VALUES (:group, :key, :value, 'text', :created_at, :updated_at)",
                            ['group' => $group, 'key' => $keyName, 'value' => $value, 'created_at' => $now, 'updated_at' => $now]
                        );
                    }
                },
            );
        });

        // --- SEO Manager ----------------------------------------------------
        if (config('seo.enabled', true)) {
            $app->bind(SeoManager::class, function () {
                return new SeoManager(
                    db: app('db')->getAdapter(),
                    config: (array) config('seo', []),
                );
            });

            $app->bind(SchemaGenerator::class, fn () => new SchemaGenerator(
                config: (array) config('seo', []),
            ));

            $app->bind(SitemapGenerator::class, function () {
                return new SitemapGenerator(
                    db: app('db')->getAdapter(),
                    config: (array) config('seo', []),
                );
            });

            $app->bind(SeoAnalyzer::class, fn () => new SeoAnalyzer(
                config: (array) config('seo', []),
            ));

            $app->bind(RedirectManager::class, function () {
                return new RedirectManager(
                    db: app('db')->getAdapter(),
                    config: (array) config('seo', []),
                );
            });

            $app->bind(LinkChecker::class, fn () => new LinkChecker(
                db: app('db')->getAdapter(),
            ));

            $app->bind(ContentSyndicator::class, fn () => new ContentSyndicator(
                config: (array) config('seo', []),
            ));

            $app->bind(BacklinkMonitor::class, fn () => new BacklinkMonitor(
                db: app('db')->getAdapter(),
                config: (array) config('seo', []),
            ));

            $app->bind(AiMetaGenerator::class, fn () => new AiMetaGenerator());
        }
    }

    public function boot(): void {}

    public function loadRoutes(): void
    {
        require __DIR__ . '/../routes/web.php';

        if (config('seo.enabled', true) && isset($router)) {
            $router->get('/sitemap.xml', [SitemapController::class, '__invoke']);
            $router->get('/robots.txt', [RobotsController::class, '__invoke']);
            $router->get('/feed', [RssController::class, 'feed']);

            $dbPrefix = null;
            try {
                $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
                $dbPrefix = $settings?->get('admin.route_prefix');
            } catch (\Throwable) {}
            $p = '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');
            $router->get("{$p}/seo", [SeoAdminController::class, 'index']);
            $router->get("{$p}/seo/settings", [SeoAdminController::class, 'settings']);
            $router->post("{$p}/seo/settings", [SeoAdminController::class, 'settings']);
            $router->get("{$p}/seo/redirects", [SeoAdminController::class, 'redirects']);
            $router->post("{$p}/seo/redirects", [SeoAdminController::class, 'redirects']);
            $router->post("{$p}/seo/redirects/delete", [SeoAdminController::class, 'deleteRedirect']);
            $router->get("{$p}/seo/analyzer", [SeoAdminController::class, 'analyzer']);
            $router->post("{$p}/seo/ping", [SeoAdminController::class, 'ping']);
        }

        if (config('cms.api.enabled', true) && isset($router)) {
            \Tavp\Cms\Api\ApiModule::routes($router);
        }
    }

    public function loadMigrations(): void {}

    public function loadViews(): void {}

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
