<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Menu admin — manage menus and menu items (nestable).
 */
class MenuController extends AdminController
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

    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $menus = $this->db()->query('SELECT * FROM menus ORDER BY name', [])->fetchAll(\PDO::FETCH_ASSOC);

        return $this->admin('menu.list', [
            'menus' => $menus,
        ]);
    }

    public function create(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        return $this->admin('menu.form', [
            'menu' => [],
            'action' => '/admin/menus',
            'heading' => 'New Menu',
        ]);
    }

    public function store(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $name = (string) $this->request->input('name', '');
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));

        $this->db()->insert('menus', [
            'name' => $name,
            'slug' => $slug,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->redirect($this->adminPrefix() . '/menus');
    }

    public function edit(string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $menu = $this->db()->query('SELECT * FROM menus WHERE id = ?', [$id])->fetchAll(\PDO::FETCH_ASSOC)[0] ?? null;
        $items = $this->db()->query('SELECT * FROM menu_items WHERE menu_id = ? ORDER BY sort_order', [$id])->fetchAll(\PDO::FETCH_ASSOC);

        return $this->admin('menu.form', [
            'menu' => $menu ?? [],
            'items' => $items,
            'action' => '/admin/menus/' . $id,
            'heading' => 'Edit Menu: ' . ($menu['name'] ?? ''),
        ]);
    }

    public function update(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $name = (string) $this->request->input('name', '');

        $this->db()->update('menus', [
            'name' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        // Update items
        $items = $this->request->input('items', []);
        $this->syncItems((int) $id, $items);

        return $this->redirect($this->adminPrefix() . '/menus');
    }

    public function destroy(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->db()->delete('menu_items', ['menu_id' => $id]);
        $this->db()->delete('menus', ['id' => $id]);

        return $this->redirect($this->adminPrefix() . '/menus');
    }

    public function addItem(string $menuId): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $label = (string) $this->request->input('label', '');
        $url = (string) $this->request->input('url', '/');
        $parentId = (int) $this->request->input('parent_id', 0);

        $this->db()->insert('menu_items', [
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'label' => $label,
            'url' => $url,
            'sort_order' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->redirect($this->adminPrefix() . '/menus/' . $menuId . '/edit');
    }

    public function deleteItem(string $menuId, string $itemId): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->db()->delete('menu_items', ['id' => $itemId, 'menu_id' => $menuId]);

        return $this->redirect($this->adminPrefix() . '/menus/' . $menuId . '/edit');
    }

    private function syncItems(int $menuId, array $items): void
    {
        // Delete existing items
        $this->db()->delete('menu_items', ['menu_id' => $menuId]);

        // Insert new items
        $sort = 0;
        foreach ($items as $item) {
            $this->db()->insert('menu_items', [
                'menu_id' => $menuId,
                'parent_id' => (int) ($item['parent_id'] ?? 0),
                'label' => $item['label'] ?? '',
                'url' => $item['url'] ?? '/',
                'sort_order' => $sort++,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function db()
    {
        return app('db');
    }
}
