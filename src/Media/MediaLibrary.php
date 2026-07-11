<?php

declare(strict_types=1);

namespace Tavp\Cms\Media;

/**
 * Media library — upload, validate, and catalogue files.
 *
 * Storage-agnostic: the actual bytes go to a disk (public/local/s3 via the
 * host app), and the catalogue row is persisted through the CMS store.
 */
class MediaLibrary
{
    /**
     * @param array<string,mixed> $config the cms.media config block
     */
    public function __construct(
        private readonly array $config,
        private readonly \Closure $persist
    ) {
    }

    /**
     * Get all media records.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        try {
            $db = app('db');
            $result = $db->query("SELECT * FROM media ORDER BY created_at DESC", []);
            $rows = $result->fetchAll();
            return $rows;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Find a media record by ID.
     */
    public function find(int $id): ?array
    {
        try {
            $db = app('db');
            $result = $db->query("SELECT * FROM media WHERE id = ? LIMIT 1", [$id]);
            $rows = $result->fetchAll();
            return !empty($rows) ? $rows[0] : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Delete a media record.
     */
    public function delete(int $id): bool
    {
        try {
            $db = app('db');
            $db->execute('DELETE FROM media WHERE id = :id', ['id' => $id]);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Validate + store an uploaded file.
     *
     * @param array{name:string,tmp_name:string,size:int,type:string} $file
     * @return array<string,mixed> the stored media record
     */
    public function upload(array $file): array
    {
        $this->guard($file);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = $this->uniqueName($file['name'], $ext);
        $relative = trim((string) ($this->config['path'] ?? 'uploads'), '/') . '/' . $fileName;
        $target = $this->diskRoot() . '/' . $relative;

        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        $this->moveUploaded($file['tmp_name'], $target);

        $record = [
            'name' => pathinfo($file['name'], PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => $file['type'],
            'path' => $relative,
            'disk' => (string) ($this->config['disk'] ?? 'public'),
            'size' => (int) $file['size'],
        ];

        $record['id'] = (int) ($this->persist)($record);

        return $record;
    }

    /**
     * @param array{name:string,tmp_name:string,size:int,type:string} $file
     */
    private function guard(array $file): void
    {
        $max = (int) ($this->config['max_size'] ?? 10 * 1024 * 1024);
        if ($file['size'] > $max) {
            throw new \RuntimeException('File exceeds the maximum allowed size.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = (array) ($this->config['allowed'] ?? []);
        if ($allowed && !in_array($ext, $allowed, true)) {
            throw new \RuntimeException("File type .{$ext} is not allowed.");
        }
    }

    private function uniqueName(string $original, string $ext): string
    {
        $base = preg_replace('/[^a-z0-9]+/', '-', strtolower(pathinfo($original, PATHINFO_FILENAME))) ?? 'file';
        $base = trim($base, '-') ?: 'file';

        return $base . '-' . substr(bin2hex(random_bytes(4)), 0, 8) . '.' . $ext;
    }

    private function diskRoot(): string
    {
        return function_exists('base_path')
            ? base_path('public')
            : (getcwd() ?: '.') . '/public';
    }

    private function moveUploaded(string $from, string $to): void
    {
        if (is_uploaded_file($from)) {
            move_uploaded_file($from, $to);

            return;
        }

        // Fallback for CLI/tests.
        copy($from, $to);
    }
}
