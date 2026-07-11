<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Routing\Router;

/**
 * Registers the CMS admin routes.
 *
 * Call from your app's routes file (where $router is in scope):
 *   \Tavp\Cms\Admin\AdminModule::routes($router);
 */
class AdminModule
{
    public static function routes(Router $router): void
    {
        // Auth
        $router->get('/admin/login', [AuthController::class, 'showLogin']);
        $router->post('/admin/login', [AuthController::class, 'sendOtp']);
        $router->get('/admin/verify', [AuthController::class, 'showVerify']);
        $router->post('/admin/verify', [AuthController::class, 'verify']);
        $router->post('/admin/logout', [AuthController::class, 'logout']);

        // Dashboard
        $router->get('/admin', [DashboardController::class, 'index']);

        // Content BREAD (create before {id} so the literal segment wins)
        $router->get('/admin/c/{type}', [ContentController::class, 'index']);
        $router->get('/admin/c/{type}/create', [ContentController::class, 'create']);
        $router->post('/admin/c/{type}', [ContentController::class, 'store']);
        $router->get('/admin/c/{type}/{id}/edit', [ContentController::class, 'edit']);
        $router->post('/admin/c/{type}/{id}', [ContentController::class, 'update']);
        $router->post('/admin/c/{type}/{id}/delete', [ContentController::class, 'destroy']);

        // Revisions
        $router->get('/admin/c/{type}/{id}/revisions', [RevisionController::class, 'history']);
        $router->post('/admin/c/{type}/{id}/rollback/{timestamp}', [RevisionController::class, 'rollback']);

        // Search
        $router->get('/admin/search', [SearchController::class, 'search']);

        // Taxonomy
        $router->get('/admin/taxonomy/{type}', [TaxonomyController::class, 'index']);
        $router->get('/admin/taxonomy/{type}/create', [TaxonomyController::class, 'create']);
        $router->post('/admin/taxonomy/{type}', [TaxonomyController::class, 'store']);
        $router->get('/admin/taxonomy/{type}/{id}/edit', [TaxonomyController::class, 'edit']);
        $router->post('/admin/taxonomy/{type}/{id}', [TaxonomyController::class, 'update']);
        $router->post('/admin/taxonomy/{type}/{id}/delete', [TaxonomyController::class, 'destroy']);

        // Settings
        $router->get('/admin/settings', [SettingsController::class, 'index']);
        $router->post('/admin/settings', [SettingsController::class, 'update']);

        // Media
        $router->get('/admin/media', [MediaController::class, 'index']);
        $router->post('/admin/media/upload', [MediaController::class, 'upload']);
        $router->post('/admin/media/{id}/delete', [MediaController::class, 'destroy']);

        // Menus
        $router->get('/admin/menus', [MenuController::class, 'index']);
        $router->get('/admin/menus/create', [MenuController::class, 'create']);
        $router->post('/admin/menus', [MenuController::class, 'store']);
        $router->get('/admin/menus/{id}/edit', [MenuController::class, 'edit']);
        $router->post('/admin/menus/{id}', [MenuController::class, 'update']);
        $router->post('/admin/menus/{id}/delete', [MenuController::class, 'destroy']);
        $router->post('/admin/menus/{menuId}/items', [MenuController::class, 'addItem']);
        $router->post('/admin/menus/{menuId}/items/{itemId}/delete', [MenuController::class, 'deleteItem']);

        // Analytics
        $router->get('/admin/analytics', [AnalyticsController::class, 'index']);
    }
}
