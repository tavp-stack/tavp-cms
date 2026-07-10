<?php

declare(strict_types=1);

namespace Tavp\Cms\Api;

use Tavp\Core\Routing\Router;

/**
 * Registers the headless REST API routes.
 */
class ApiModule
{
    public static function routes(Router $router): void
    {
        $router->group(['prefix' => (string) config('cms.api.prefix', 'api/cms')], static function (Router $r) {
            // Content types
            $r->get('/types', [\Tavp\Cms\Api\ApiController::class, 'types']);

            // Search
            $r->get('/search', [\Tavp\Cms\Api\ApiController::class, 'search']);

            // Taxonomy
            $r->get('/taxonomy/{type}', [\Tavp\Cms\Api\ApiController::class, 'taxonomyIndex']);
            $r->post('/taxonomy', [\Tavp\Cms\Api\ApiController::class, 'taxonomyStore']);

            // Content CRUD
            $r->get('/{type}', [\Tavp\Cms\Api\ApiController::class, 'index']);
            $r->get('/{type}/{id}', [\Tavp\Cms\Api\ApiController::class, 'show']);
            $r->post('/{type}', [\Tavp\Cms\Api\ApiController::class, 'store']);
            $r->put('/{type}/{id}', [\Tavp\Cms\Api\ApiController::class, 'update']);
            $r->delete('/{type}/{id}', [\Tavp\Cms\Api\ApiController::class, 'destroy']);

            // Revisions
            $r->get('/{type}/{id}/revisions', [\Tavp\Cms\Api\ApiController::class, 'revisions']);
            $r->post('/{type}/{id}/rollback/{timestamp}', [\Tavp\Cms\Api\ApiController::class, 'rollback']);
        });
    }
}
