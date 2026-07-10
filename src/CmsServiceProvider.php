<?php

declare(strict_types=1);

namespace Tavp\Cms;

use Tavp\Cms\Bread\BreadManager;
use Tavp\Cms\Storage\ContentStore;
use Tavp\Cms\Storage\DatabaseStore;
use Tavp\Cms\Storage\FlatFileStore;
use Tavp\Cms\Theme\ThemeManager;
use Tavp\Core\Module\ServiceProvider;

/**
 * Wires TAVP CMS into a TAVP application.
 *
 * - Chooses the storage driver from config (database | flatfile)
 * - Registers the BreadManager with the configured content types
 * - Loads CMS routes (admin + front-end), migrations and views
 */
class CmsServiceProvider implements ServiceProvider
{
    public function register(): void
    {
        $app = app();

        $app->bind(ContentStore::class, fn () => $this->makeStore());

        $app->bind(BreadManager::class, function ($app) {
            $manager = new BreadManager($app->getService(ContentStore::class));
            $manager->registerFromConfig((array) config('cms.content_types', []));

            return $manager;
        });

        $app->bind(ThemeManager::class, fn () => new ThemeManager(
            (string) config('cms.theme.path', base_path('themes')),
            (string) config('cms.theme.active', 'default'),
        ));
    }

    public function boot(): void
    {
        // Register CMS content types as BREAD resources in the tavphub admin.
        // tavphub reads the BreadManager registry and builds list/form views.
    }

    public function loadRoutes(): void
    {
        require __DIR__ . '/../routes/web.php';
    }

    public function loadMigrations(): void
    {
        // Migrations live in database/migrations and are discovered by tavp-core.
    }

    public function loadViews(): void
    {
        // The active theme registers its Volt view path with the view factory.
    }

    private function makeStore(): ContentStore
    {
        $driver = (string) config('cms.storage', 'database');

        return match ($driver) {
            'flatfile' => new FlatFileStore(
                basePath: (string) config('cms.drivers.flatfile.path', base_path('content')),
            ),
            default => new DatabaseStore(
                connection: fn () => app('db'),
            ),
        };
    }
}
