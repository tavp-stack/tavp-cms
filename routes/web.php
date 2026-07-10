<?php

declare(strict_types=1);

use Tavp\Cms\Bread\BreadManager;

/**
 * TAVP CMS front-end routes.
 *
 * A catch-all resolves a request path to a published content record via the
 * active storage driver, then renders it with the active theme template.
 * The admin (back-office) routes are registered by tavphub.
 *
 * $router is provided by the host application when this file is required.
 */

/** @var \Tavp\Core\Routing\Router $router */
if (!isset($router)) {
    return;
}

// Blog post: /blog/{slug}
$router->get('/blog/{slug}', function (string $slug) {
    $bread = app()->getService(BreadManager::class);
    $post = $bread->readBySlug('post', $slug);

    if ($post === null || ($post['status'] ?? 'draft') !== 'published') {
        return response()->notFound();
    }

    return view('post', ['content' => $post]);
});

// Page: /{slug} (catch-all, keep last)
$router->get('/{slug}', function (string $slug) {
    $bread = app()->getService(BreadManager::class);
    $page = $bread->readBySlug('page', $slug);

    if ($page === null || ($page['status'] ?? 'draft') !== 'published') {
        return response()->notFound();
    }

    return view('page', ['content' => $page]);
});
