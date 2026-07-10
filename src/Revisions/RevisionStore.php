<?php

declare(strict_types=1);

namespace Tavp\Cms\Revisions;

/**
 * Stores content revisions as JSON files under storage/cms/revisions/.
 * Driver-agnostic — works identically with database and flatfile stores.
 *
 * Each snapshot is a timestamped JSON file keyed by content_type + content_id.
 */
class RevisionStore
{
    public function __construct(
        private readonly string $path,
        private readonly int $limit = 50,
    ) {
    }

    /**
     * Record a snapshot of a content record.
     *
     * @param array<string,mixed> $data
     */
    public function snapshot(
        string $contentType,
        string|int $contentId,
        array $data,
        ?string $author = null,
        ?string $note = null,
    ): void {
        $dir = $this->dir($contentType, $contentId);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $revision = [
            'content_type' => $contentType,
            'content_id' => (string) $contentId,
            'data' => $data,
            'author' => $author,
            'note' => $note,
            'created_at' => date('c'),
        ];

        $file = $dir . '/' . $this->safeTimestamp() . '.json';
        file_put_contents($file, json_encode($revision, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->prune($contentType, $contentId);
    }

    /**
     * Get all revisions for a content record, newest first.
     *
     * @return array<int,array<string,mixed>>
     */
    public function history(string $contentType, string|int $contentId): array
    {
        $dir = $this->dir($contentType, $contentId);

        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/*.json') ?: [];
        usort($files, fn (string $a, string $b) => filemtime($b) <=> filemtime($a));

        return array_map(
            fn (string $f) => json_decode((string) file_get_contents($f), true),
            $files,
        );
    }

    /**
     * Get a specific revision by its timestamp filename.
     */
    public function get(string $contentType, string|int $contentId, string $timestamp): ?array
    {
        $file = $this->dir($contentType, $contentId) . '/' . $this->safe($timestamp) . '.json';

        if (!is_file($file)) {
            return null;
        }

        return json_decode((string) file_get_contents($file), true);
    }

    /**
     * Delete all revisions for a content record.
     */
    public function prune(string $contentType, string|int $contentId): void
    {
        $dir = $this->dir($contentType, $contentId);

        if (!is_dir($dir)) {
            return;
        }

        $files = glob($dir . '/*.json') ?: [];

        if (count($files) <= $this->limit) {
            return;
        }

        usort($files, fn (string $a, string $b) => filemtime($a) <=> filemtime($b));

        $toDelete = array_slice($files, 0, count($files) - $this->limit);

        foreach ($toDelete as $file) {
            unlink($file);
        }
    }

    private function dir(string $contentType, string|int $contentId): string
    {
        return rtrim($this->path, '/') . '/' . $contentType . '/' . $contentId;
    }

    private function safeTimestamp(): string
    {
        $ts = microtime(true);
        $sec = (int) $ts;
        $usec = (int) round(($ts - $sec) * 1000000);

        return date('Y-m-d_H-i-s') . '_' . str_pad((string) $usec, 6, '0', STR_PAD_LEFT);
    }

    private function safe(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-.]/', '', $value) ?: 'unknown';
    }
}
