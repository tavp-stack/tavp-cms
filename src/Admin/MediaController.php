<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Media admin — upload, list, and delete media files.
 */
class MediaController extends AdminController
{
    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $media = $this->getMediaLibrary()->all();

        // Sort
        $sort = $_GET['sort'] ?? 'name';
        $dir = strtoupper($_GET['dir'] ?? 'ASC');
        if (!in_array($sort, ['name', 'mime_type', 'size', 'id'], true)) { $sort = 'name'; }
        if (!in_array($dir, ['ASC', 'DESC'], true)) { $dir = 'ASC'; }

        usort($media, function ($a, $b) use ($sort, $dir) {
            $va = $a[$sort] ?? 0;
            $vb = $b[$sort] ?? 0;
            if ($sort === 'size') {
                $cmp = (int) $va <=> (int) $vb;
            } else {
                $cmp = strcasecmp((string) $va, (string) $vb);
            }
            return $dir === 'DESC' ? -$cmp : $cmp;
        });

        return $this->admin('media.list', [
            'media' => $media,
        ]);
    }

    public function upload(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $file = $_FILES['file'] ?? null;

        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'No file uploaded or upload error.');
            return $this->redirect('/admin/media');
        }

        try {
            $this->getMediaLibrary()->upload($file);
            $this->flash('success', 'File uploaded successfully.');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/media');
    }

    public function destroy(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $media = $this->getMediaLibrary()->find((int) $id);

        if ($media !== null) {
            $path = $this->getMediaRoot() . '/' . $media['path'];
            if (is_file($path)) {
                unlink($path);
            }
            $this->getMediaLibrary()->delete((int) $id);
            $this->flash('success', 'File deleted.');
        }

        return $this->redirect('/admin/media');
    }

    /**
     * API endpoint for Toast UI Editor image upload.
     * Returns JSON response with image URL.
     */
    public function uploadApi(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $file = $_FILES['file'] ?? $_FILES['image'] ?? null;

        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['success' => false, 'message' => 'No file uploaded or upload error.'], 400);
        }

        try {
            $id = $this->getMediaLibrary()->upload($file);
            $media = $this->getMediaLibrary()->find($id);
            $url = '/uploads/' . ($media['path'] ?? $file['name']);

            return $this->json([
                'success' => true,
                'url' => $url,
                'name' => $media['name'] ?? $file['name'],
                'id' => $id,
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Return JSON response.
     */
    protected function json(mixed $data, int $status = 200): Response
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        return new Response();
    }

    private function getMediaLibrary(): \Tavp\Cms\Media\MediaLibrary
    {
        return app()->getService(\Tavp\Cms\Media\MediaLibrary::class);
    }

    private function getMediaRoot(): string
    {
        return base_path('public');
    }
}
