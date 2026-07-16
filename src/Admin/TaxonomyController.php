<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Cms\Taxonomy\TaxonomyManager;
use Tavp\Core\Http\Response;

/**
 * CRUD for taxonomy terms (categories / tags).
 */
class TaxonomyController extends AdminController
{
    protected function adminPrefix(): string
    {
        $dbPrefix = null;
        try {
            $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
            $dbPrefix = $settings?->get('admin.route_prefix');
        } catch (\Throwable) {}
        return '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');
    }

    public function index(string $type): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $sort = $_GET['sort'] ?? 'name';
        $dir = strtoupper($_GET['dir'] ?? 'ASC');
        if (!in_array($sort, ['name', 'slug', 'id'], true)) { $sort = 'name'; }
        if (!in_array($dir, ['ASC', 'DESC'], true)) { $dir = 'ASC'; }

        $taxonomy = $this->taxonomy();
        $terms = array_map(
            static fn ($term) => $term instanceof \Tavp\Cms\Taxonomy\Term ? $term->toArray() : $term,
            $taxonomy->all($type)
        );

        // Sort in PHP (taxonomy->all() returns array)
        usort($terms, function ($a, $b) use ($sort, $dir) {
            $va = $a[$sort] ?? '';
            $vb = $b[$sort] ?? '';
            $cmp = strcasecmp((string) $va, (string) $vb);
            return $dir === 'DESC' ? -$cmp : $cmp;
        });

        return $this->admin('taxonomy_list', [
            'terms' => $terms,
            'termType' => $type,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function create(string $type): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        return $this->admin('taxonomy_form', [
            'termType' => $type,
            'term' => [],
            'action' => "/admin/taxonomy/{$type}",
            'heading' => 'New ' . ucfirst($type),
        ]);
    }

    public function store(string $type): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->taxonomy()->create([
            'type' => $type,
            'name' => (string) $this->request->input('name', ''),
            'description' => (string) $this->request->input('description', ''),
            'parent_id' => (int) $this->request->input('parent_id', 0),
            'sort' => (int) $this->request->input('sort', 0),
        ]);

        return $this->redirect("/admin/taxonomy/{$type}");
    }

    public function edit(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $term = $this->taxonomy()->find((int) $id);

        if ($term === null) {
            return $this->redirect("/admin/taxonomy/{$type}");
        }

        if ($term instanceof \Tavp\Cms\Taxonomy\Term) {
            $term = $term->toArray();
        }

        return $this->admin('taxonomy_form', [
            'termType' => $type,
            'term' => $term,
            'action' => "/admin/taxonomy/{$type}/{$id}",
            'heading' => 'Edit ' . ucfirst($type),
        ]);
    }

    public function update(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->taxonomy()->update((int) $id, [
            'name' => (string) $this->request->input('name', ''),
            'description' => (string) $this->request->input('description', ''),
            'parent_id' => (int) $this->request->input('parent_id', 0),
            'sort' => (int) $this->request->input('sort', 0),
        ]);

        return $this->redirect("/admin/taxonomy/{$type}");
    }

    public function destroy(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->taxonomy()->delete((int) $id);

        return $this->redirect("/admin/taxonomy/{$type}");
    }

    private function taxonomy(): TaxonomyManager
    {
        return app()->getService(TaxonomyManager::class);
    }
}
