<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Cms\Content\ContentType;
use Tavp\Cms\Content\ValidationException;
use Tavp\Core\Http\Response;

/**
 * Generic Browse/Read/Edit/Add/Delete for any content type.
 */
class ContentController extends AdminController
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

        if (!$this->can("content.browse")) {
            return $this->redirect($this->adminPrefix());
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect($this->adminPrefix());
        }

        return $this->admin('list', [
            'type' => $contentType,
            'records' => $this->bread()->browse($type),
        ]);
    }

    public function create(string $type): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.create")) {
            return $this->redirect($this->adminPrefix());
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect($this->adminPrefix());
        }

        // Construct $content for the form view (empty for create)
        $content = ['__type' => $type, '__fields' => $this->fieldsToArray($contentType->fields)];

        return $this->admin('form', [
            'content' => $content,
            'action' => "/admin/c/{$type}",
            'heading' => 'New ' . $contentType->singular,
        ]);
    }

    public function store(string $type): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.create")) {
            return $this->redirect($this->adminPrefix());
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect($this->adminPrefix());
        }

        $data = $this->collect($contentType);
        $data['author'] = $this->getCurrentUserName();

        try {
            $this->bread()->add($type, $data);
        } catch (ValidationException $e) {
            $this->flashErrors($e->errors());
            $this->flashOld($data);
            return $this->redirect("/admin/c/{$type}/create");
        }

        $this->clearCmsCache();
        return $this->redirect("/admin/c/{$type}");
    }

    public function edit(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.edit")) {
            return $this->redirect($this->adminPrefix());
        }

        $contentType = $this->type($type);
        // Cast $id to integer if numeric (routes pass string "5" but store expects int)
        $readId = is_numeric($id) ? (int) $id : $id;
        $record = $contentType ? $this->bread()->read($type, $readId) : null;

        if ($contentType === null || $record === null) {
            return $this->redirect("/admin/c/{$type}");
        }

        // Construct $content for the form view
        // $record already has decoded field values from hydrate()
        $content = $record;
        $content['__type'] = $type;
        $content['__fields'] = $this->fieldsToArray($contentType->fields);

        return $this->admin('form', [
            'content' => $content,
            'action' => "/admin/c/{$type}/{$id}",
            'heading' => 'Edit ' . $contentType->singular,
        ]);
    }

    public function update(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.edit")) {
            return $this->redirect($this->adminPrefix());
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect($this->adminPrefix());
        }

        $data = $this->collect($contentType);
        $data['author'] = $this->getCurrentUserName();

        // Cast $id to integer if numeric (routes pass string "5" but store expects int)
        $editId = is_numeric($id) ? (int) $id : $id;

        try {
            $this->bread()->edit($type, $editId, $data);
        } catch (ValidationException $e) {
            $this->flashErrors($e->errors());
            $this->flashOld($data);
            return $this->redirect("/admin/c/{$type}/{$id}/edit");
        }

        $this->clearCmsCache();
        return $this->redirect("/admin/c/{$type}");
    }

    public function destroy(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.delete")) {
            return $this->redirect($this->adminPrefix());
        }

        $this->bread()->delete($type, is_numeric($id) ? (int) $id : $id);

        return $this->redirect("/admin/c/{$type}");
    }

    /**
     * Get current logged-in user's name for author field.
     */
    private function getCurrentUserName(): string
    {
        $email = $_SESSION['cms_admin'] ?? '';
        if ($email === '') {
            return '';
        }
        try {
            $rows = app('db')->fetchAll(
                'SELECT name FROM users WHERE email = :email LIMIT 1',
                \PDO::FETCH_ASSOC,
                ['email' => $email]
            );
            return $rows[0]['name'] ?? '';
        } catch (\Throwable) {
            return '';
        }
    }

    private function type(string $name): ?\Tavp\Cms\Content\ContentType
    {
        return $this->bread()->type($name);
    }

    /**
     * Convert Field objects to arrays for the form view.
     *
     * @param \Tavp\Cms\Content\Field[] $fields
     * @return array<int,array<string,mixed>>
     */
    private function fieldsToArray(array $fields): array
    {
        return array_map(fn (\Tavp\Cms\Content\Field $f) => [
            'name' => $f->name,
            'type' => $f->type->value,
            'required' => $f->required,
            'default' => $f->default,
            'options' => $f->options,
        ], $fields);
    }

    private function clearCmsCache(): void
    {
        try {
            $cachePath = config('cms.cache.path', storage_path('cms/cache'));
            if (is_dir($cachePath)) {
                $files = glob($cachePath . '/*');
                foreach ($files as $f) {
                    if (is_file($f)) unlink($f);
                }
            }
            // Also clear compiled Volt templates
            $voltPath = storage_path('compiled/volt');
            if (is_dir($voltPath)) {
                $files = glob($voltPath . '/*');
                foreach ($files as $f) {
                    if (is_file($f)) unlink($f);
                }
            }
        } catch (\Throwable) {
            // Ignore cache clearing errors
        }
    }
}
