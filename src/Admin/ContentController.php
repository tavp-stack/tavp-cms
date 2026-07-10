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
    public function index(string $type): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.browse")) {
            return $this->redirect('/admin');
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect('/admin');
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
            return $this->redirect('/admin');
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect('/admin');
        }

        return $this->admin('form', [
            'type' => $contentType,
            'record' => [],
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
            return $this->redirect('/admin');
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect('/admin');
        }

        $data = $this->collect($contentType);

        try {
            $this->bread()->add($type, $data);
        } catch (ValidationException $e) {
            $this->flashErrors($e->errors());
            $this->flashOld($data);
            return $this->redirect("/admin/c/{$type}/create");
        }

        return $this->redirect("/admin/c/{$type}");
    }

    public function edit(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.edit")) {
            return $this->redirect('/admin');
        }

        $contentType = $this->type($type);
        $record = $contentType ? $this->bread()->read($type, $id) : null;

        if ($contentType === null || $record === null) {
            return $this->redirect("/admin/c/{$type}");
        }

        return $this->admin('form', [
            'type' => $contentType,
            'record' => $record,
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
            return $this->redirect('/admin');
        }

        $contentType = $this->type($type);
        if ($contentType === null) {
            return $this->redirect('/admin');
        }

        $data = $this->collect($contentType);

        try {
            $this->bread()->edit($type, $id, $data);
        } catch (ValidationException $e) {
            $this->flashErrors($e->errors());
            $this->flashOld($data);
            return $this->redirect("/admin/c/{$type}/{$id}/edit");
        }

        return $this->redirect("/admin/c/{$type}");
    }

    public function destroy(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        if (!$this->can("content.delete")) {
            return $this->redirect('/admin');
        }

        $this->bread()->delete($type, $id);

        return $this->redirect("/admin/c/{$type}");
    }

    /**
     * Collect submitted values for a content type's fields.
     *
     * @return array<string,mixed>
     */
    private function collect(ContentType $contentType): array
    {
        $data = [];
        foreach ($contentType->fields as $field) {
            $value = $this->request->input($field->name);
            if ($value !== null) {
                $data[$field->name] = $value;
            }
        }

        return $data;
    }

    private function type(string $name): ?ContentType
    {
        return $this->bread()->type($name);
    }
}
