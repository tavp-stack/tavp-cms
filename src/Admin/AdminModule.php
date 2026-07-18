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
    public static function routes(Router $router, ?string $prefix = null): void
    {
        // Read from database first, fallback to config
        $dbPrefix = null;
        try {
            $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
            $dbPrefix = $settings?->get('admin.route_prefix');
        } catch (\Throwable) {}

        $p = $prefix ?? $dbPrefix ?? config('cms.admin.route_prefix', 'admin');
        $p = '/' . trim($p, '/');

        // Auth
        $router->get("{$p}/login", [AuthController::class, 'showLogin']);
        $router->post("{$p}/login", [AuthController::class, 'sendOtp']);
        $router->get("{$p}/verify", [AuthController::class, 'showVerify']);
        $router->post("{$p}/verify", [AuthController::class, 'verify']);
        $router->post("{$p}/logout", [AuthController::class, 'logout']);

        // Dashboard
        $router->get("{$p}", [DashboardController::class, 'index']);
        $router->get("{$p}/home", [DashboardController::class, 'home']);

        // Content BREAD
        $router->get("{$p}/c/{type}", [ContentController::class, 'index']);
        $router->get("{$p}/c/{type}/create", [ContentController::class, 'create']);
        $router->post("{$p}/c/{type}", [ContentController::class, 'store']);
        $router->get("{$p}/c/{type}/{id}/edit", [ContentController::class, 'edit']);
        $router->post("{$p}/c/{type}/{id}", [ContentController::class, 'update']);
        $router->post("{$p}/c/{type}/{id}/delete", [ContentController::class, 'destroy']);

        // Revisions
        $router->get("{$p}/c/{type}/{id}/revisions", [RevisionController::class, 'history']);
        $router->post("{$p}/c/{type}/{id}/rollback/{timestamp}", [RevisionController::class, 'rollback']);

        // Search
        $router->get("{$p}/search", [SearchController::class, 'search']);

        // Taxonomy
        $router->get("{$p}/taxonomy/{type}", [TaxonomyController::class, 'index']);
        $router->get("{$p}/taxonomy/{type}/create", [TaxonomyController::class, 'create']);
        $router->post("{$p}/taxonomy/{type}", [TaxonomyController::class, 'store']);
        $router->get("{$p}/taxonomy/{type}/{id}/edit", [TaxonomyController::class, 'edit']);
        $router->post("{$p}/taxonomy/{type}/{id}", [TaxonomyController::class, 'update']);
        $router->post("{$p}/taxonomy/{type}/{id}/delete", [TaxonomyController::class, 'destroy']);

        // Settings
        $router->get("{$p}/settings", [SettingsController::class, 'index']);
        $router->post("{$p}/settings", [SettingsController::class, 'update']);

        // Media
        $router->get("{$p}/media", [MediaController::class, 'index']);
        $router->post("{$p}/media/upload", [MediaController::class, 'upload']);
        $router->post("{$p}/media/api/upload", [MediaController::class, 'uploadApi']);
        $router->post("{$p}/media/{id}/delete", [MediaController::class, 'destroy']);

        // Menus
        $router->get("{$p}/menus", [MenuController::class, 'index']);
        $router->get("{$p}/menus/create", [MenuController::class, 'create']);
        $router->post("{$p}/menus", [MenuController::class, 'store']);
        $router->get("{$p}/menus/{id}/edit", [MenuController::class, 'edit']);
        $router->post("{$p}/menus/{id}", [MenuController::class, 'update']);
        $router->post("{$p}/menus/{id}/delete", [MenuController::class, 'destroy']);
        $router->post("{$p}/menus/{menuId}/items", [MenuController::class, 'addItem']);
        $router->post("{$p}/menus/{menuId}/items/{itemId}/delete", [MenuController::class, 'deleteItem']);

        // Users
        $router->get("{$p}/users", [UsersController::class, 'index']);
        $router->get("{$p}/users/create", [UsersController::class, 'create']);
        $router->post("{$p}/users", [UsersController::class, 'store']);
        $router->get("{$p}/users/{id}/edit", [UsersController::class, 'edit']);
        $router->post("{$p}/users/{id}", [UsersController::class, 'update']);
        $router->post("{$p}/users/{id}/delete", [UsersController::class, 'destroy']);

        // Teams
        $router->get("{$p}/teams", [TeamController::class, 'index']);
        $router->get("{$p}/teams/create", [TeamController::class, 'create']);
        $router->post("{$p}/teams", [TeamController::class, 'store']);
        $router->get("{$p}/teams/{id}/edit", [TeamController::class, 'edit']);
        $router->post("{$p}/teams/{id}", [TeamController::class, 'update']);
        $router->post("{$p}/teams/{id}/delete", [TeamController::class, 'destroy']);
        $router->post("{$p}/teams/{teamId}/members", [TeamController::class, 'addMember']);
        $router->post("{$p}/teams/{teamId}/members/{memberId}/remove", [TeamController::class, 'removeMember']);

        // Billing
        $router->get("{$p}/billing", [BillingController::class, 'index']);
        $router->get("{$p}/billing/invoices", [BillingController::class, 'invoices']);
        $router->post("{$p}/billing/subscriptions/{id}/cancel", [BillingController::class, 'cancelSubscription']);

        // Analytics
        $router->get("{$p}/analytics", [AnalyticsController::class, 'index']);

        // Messages (contact-form inbox)
        $router->get("{$p}/messages", [MessagesController::class, 'index']);
        $router->post("{$p}/messages/read", [MessagesController::class, 'markRead']);
        $router->post("{$p}/messages/archive", [MessagesController::class, 'archive']);
        $router->post("{$p}/messages/delete", [MessagesController::class, 'destroy']);

        // SEO
        $router->get("{$p}/seo", [\Tavp\Cms\Seo\SeoAdminController::class, 'index']);
        $router->get("{$p}/seo/settings", [\Tavp\Cms\Seo\SeoAdminController::class, 'settings']);
        $router->post("{$p}/seo/settings", [\Tavp\Cms\Seo\SeoAdminController::class, 'settings']);
        $router->get("{$p}/seo/redirects", [\Tavp\Cms\Seo\SeoAdminController::class, 'redirects']);
        $router->post("{$p}/seo/redirects", [\Tavp\Cms\Seo\SeoAdminController::class, 'redirects']);
        $router->post("{$p}/seo/redirects/delete", [\Tavp\Cms\Seo\SeoAdminController::class, 'deleteRedirect']);
        $router->get("{$p}/seo/analyzer", [\Tavp\Cms\Seo\SeoAdminController::class, 'analyzer']);
        $router->post("{$p}/seo/ping", [\Tavp\Cms\Seo\SeoAdminController::class, 'ping']);

        // BREAD Manager
        $router->get("{$p}/bread", [BreadController::class, 'index']);
    }
}
