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
    }
}
