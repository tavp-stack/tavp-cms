<?php

declare(strict_types=1);

namespace Tavp\Cms\Storage;

use Symfony\Component\Yaml\Yaml;
use Tavp\Cms\Content\ContentType;

/**
 * Flat-file content store (Statamic style).
 *
 * Each record is a Markdown file with YAML front matter, stored under
 * content/<type>/<slug>.md. No database required — content is git-friendly
 * and diff-able. The record id is the slug.
 *
 *   ---
 *   title: Hello World
 *   status: published
 *   ---
 *   Body markdown goes here.
 */
class FlatFileStore implements ContentStore
{
    public function __construct(
        private readonly string $basePath,
        private readonly string $bodyField = 'body'
    ) {
    }

    /**
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function all(ContentType $type, array $filters = []): array
    {
        $dir = $this->typeDir($type);

        if (!is_dir($dir)) {
            return [];
        }

        $records = [];
        foreach (glob($dir . '/*.md') ?: [] as $file) {
            $record = $this->readFile($type, $file);

            foreach ($filters as $key => $value) {
                if (($record[$key] ?? null) !== $value) {
                    continue 2;
                }
            }

            $records[] = $record;
        }

        usort($records, fn ($a, $b) => ($b['created_at'] ?? '') <=> ($a['created_at'] ?? ''));

        return $records;
    }

    public function find(ContentType $type, string|int $id): ?array
    {
        return $this->findBySlug($type, (string) $id);
    }

    public function findBySlug(ContentType $type, string $slug): ?array
    {
        $file = $this->typeDir($type) . '/' . $this->safe($slug) . '.md';

        return is_file($file) ? $this->readFile($type, $file) : null;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function create(ContentType $type, array $data): array
    {
        $slug = $this->safe((string) ($data['slug'] ?? $data['title'] ?? uniqid('c')));
        $data['slug'] = $slug;
        $data['created_at'] ??= date('c');
        $data['updated_at'] = date('c');

        $this->writeFile($type, $slug, $data);

        return $this->findBySlug($type, $slug) ?? $data;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(ContentType $type, string|int $id, array $data): array
    {
        $existing = $this->findBySlug($type, (string) $id) ?? [];
        $merged = array_merge($existing, $data);
        $merged['updated_at'] = date('c');

        $newSlug = $this->safe((string) ($merged['slug'] ?? $id));

        // Slug changed → remove the old file.
        if ($newSlug !== (string) $id) {
            $this->delete($type, $id);
        }

        $this->writeFile($type, $newSlug, $merged);

        return $this->findBySlug($type, $newSlug) ?? $merged;
    }

    public function delete(ContentType $type, string|int $id): bool
    {
        $file = $this->typeDir($type) . '/' . $this->safe((string) $id) . '.md';

        return is_file($file) ? unlink($file) : false;
    }

    /**
     * @return array<string,mixed>
     */
    private function readFile(ContentType $type, string $file): array
    {
        $raw = (string) file_get_contents($file);
        $front = [];
        $body = $raw;

        if (preg_match('/^---\s*\n(.*?)\n---\s*\n?(.*)$/s', $raw, $m)) {
            $front = Yaml::parse($m[1]) ?: [];
            $body = $m[2];
        }

        $slug = basename($file, '.md');

        return array_merge($front, [
            'id' => $slug,
            'type' => $type->name,
            'slug' => $slug,
            $this->bodyField => trim($body),
            'status' => $front['status'] ?? 'draft',
        ]);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function writeFile(ContentType $type, string $slug, array $data): void
    {
        $dir = $this->typeDir($type);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $body = (string) ($data[$this->bodyField] ?? '');
        unset($data[$this->bodyField], $data['id'], $data['type']);

        $contents = "---\n" . Yaml::dump($data, 4, 2) . "---\n\n" . $body . "\n";
        file_put_contents($dir . '/' . $slug . '.md', $contents);
    }

    private function typeDir(ContentType $type): string
    {
        return rtrim($this->basePath, '/') . '/' . $type->name;
    }

    private function safe(string $slug): string
    {
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';

        return trim($slug, '-') ?: 'untitled';
    }
}
