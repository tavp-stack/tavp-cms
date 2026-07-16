<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Browse revision history and rollback for any content record.
 */
class RevisionController extends AdminController
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

    public function history(string $type, string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $revisions = $this->bread()->history($type, $id);
        $record = $this->bread()->read($type, $id);

        if ($record === null) {
            return $this->redirect("/admin/c/{$type}");
        }

        $contentType = $this->bread()->type($type);

        return $this->admin('revisions', [
            'type' => $contentType,
            'record' => $record,
            'revisions' => $revisions,
        ]);
    }

    public function rollback(string $type, string $id, string $timestamp): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $revisionStore = $this->bread()->revisions();

        if ($revisionStore === null) {
            return $this->redirect("/admin/c/{$type}/{$id}/revisions");
        }

        $revision = $revisionStore->get($type, $id, $timestamp);

        if ($revision === null) {
            return $this->redirect("/admin/c/{$type}/{$id}/revisions");
        }

        $this->bread()->restore($type, $id, $revision['data'] ?? []);

        return $this->redirect("/admin/c/{$type}/{$id}/edit");
    }
}
