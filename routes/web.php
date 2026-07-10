<?php

declare(strict_types=1);

use Tavp\Cms\Bread\BreadManager;

/**
 * TAVP CMS front-end routes.
 *
 * A catch-all resolves a request path to a published content record via the
 * active storage driver, then renders it with the active theme template.
 * The admin (back-office) routes are registered by AdminModule.
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
        return response('404 Not Found', 404);
    }

    // Enrich with taxonomy if available.
    if (config('cms.taxonomy.enabled', true)) {
        try {
            $taxonomy = app()->getService(\Tavp\Cms\Taxonomy\TaxonomyManager::class);
            $post['categories'] = array_map(
                fn ($t) => $t->toArray(),
                $taxonomy->termsFor((int) $post['id'], 'category'),
            );
            $post['tags'] = array_map(
                fn ($t) => $t->toArray(),
                $taxonomy->termsFor((int) $post['id'], 'tag'),
            );
        } catch (\Throwable) {
            // Taxonomy not available — continue without it.
        }
    }

    return view('post', ['content' => $post]);
});

// Taxonomy archive: /category/{slug} and /tag/{slug}
$router->get('/category/{slug}', function (string $slug) {
    $taxonomy = app()->getService(\Tavp\Cms\Taxonomy\TaxonomyManager::class);
    $term = $taxonomy->findBySlug('category', $slug);

    if ($term === null) {
        return response('404 Not Found', 404);
    }

    $bread = app()->getService(BreadManager::class);
    $contentIds = $taxonomy->contentIdsWithTerm((int) $term->id, 'post');

    $posts = [];
    foreach ($contentIds as $id) {
        $post = $bread->read('post', $id);
        if ($post !== null && ($post['status'] ?? 'draft') === 'published') {
            $posts[] = $post;
        }
    }

    return view('category', ['term' => $term->toArray(), 'posts' => $posts]);
});

$router->get('/tag/{slug}', function (string $slug) {
    $taxonomy = app()->getService(\Tavp\Cms\Taxonomy\TaxonomyManager::class);
    $term = $taxonomy->findBySlug('tag', $slug);

    if ($term === null) {
        return response('404 Not Found', 404);
    }

    $bread = app()->getService(BreadManager::class);
    $contentIds = $taxonomy->contentIdsWithTerm((int) $term->id, 'post');

    $posts = [];
    foreach ($contentIds as $id) {
        $post = $bread->read('post', $id);
        if ($post !== null && ($post['status'] ?? 'draft') === 'published') {
            $posts[] = $post;
        }
    }

    return view('tag', ['term' => $term->toArray(), 'posts' => $posts]);
});

// Blog index: /blog
$router->get('/blog', function () {
    $bread = app()->getService(BreadManager::class);
    $posts = $bread->browse('post', ['status' => 'published']);

    return view('blog', ['posts' => $posts]);
});

// Page: /{slug} (catch-all, keep last)
$router->get('/{slug}', function (string $slug) {
    $bread = app()->getService(BreadManager::class);
    $page = $bread->readBySlug('page', $slug);

    if ($page === null || ($page['status'] ?? 'draft') !== 'published') {
        return response('404 Not Found', 404);
    }

    // Enrich with taxonomy if available.
    if (config('cms.taxonomy.enabled', true)) {
        try {
            $taxonomy = app()->getService(\Tavp\Cms\Taxonomy\TaxonomyManager::class);
            $page['categories'] = array_map(
                fn ($t) => $t->toArray(),
                $taxonomy->termsFor((int) $page['id'], 'category'),
            );
            $page['tags'] = array_map(
                fn ($t) => $t->toArray(),
                $taxonomy->termsFor((int) $page['id'], 'tag'),
            );
        } catch (\Throwable) {
            // Taxonomy not available — continue without it.
        }
    }

    return view('page', ['content' => $page]);
});
