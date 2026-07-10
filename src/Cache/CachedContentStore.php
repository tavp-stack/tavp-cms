<?php

declare(strict_types=1);

namespace Tavp\Cms\Cache;

use Tavp\Cms\Content\ContentType;
use Tavp\Cms\Storage\ContentStore;

/**
 * Cache decorator around ContentStore.
 *
 * Wraps any driver (database / flatfile) and caches reads in-memory + on
 * disk. Writes invalidate the relevant cache keys automatically.
 * The rest of the CMS sees a ContentStore and doesn't know about caching.
 */
class CachedContentStore implements ContentStore
{
    /** @var array<string,mixed> */
    private array $memoryCache = [];

    public function __construct(
        private readonly ContentStore $inner,
        private readonly string $cachePath = 'storage/cms/cache',
        private readonly int $ttl = 300,
    ) {
    }

    /**
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function all(ContentType $type, array $filters = []): array
    {
        $key = $this->key('all', $type->name, $filters);

        if ($this->has($key)) {
            return $this->get($key);
        }

        $result = $this->inner->all($type, $filters);
        $this->set($key, $result);

        return $result;
    }

    public function find(ContentType $type, string|int $id): ?array
    {
        $key = $this->key('find', $type->name, $id);

        if ($this->has($key)) {
            return $this->get($key);
        }

        $result = $this->inner->find($type, $id);
        $this->set($key, $result);

        return $result;
    }

    public function findBySlug(ContentType $type, string $slug): ?array
    {
        $key = $this->key('slug', $type->name, $slug);

        if ($this->has($key)) {
            return $this->get($key);
        }

        $result = $this->inner->findBySlug($type, $slug);
        $this->set($key, $result);

        return $result;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function create(ContentType $type, array $data): array
    {
        $result = $this->inner->create($type, $data);
        $this->invalidateType($type->name);

        return $result;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(ContentType $type, string|int $id, array $data): array
    {
        $result = $this->inner->update($type, $id, $data);
        $this->invalidateType($type->name);

        return $result;
    }

    public function delete(ContentType $type, string|int $id): bool
    {
        $result = $this->inner->delete($type, $id);
        $this->invalidateType($type->name);

        return $result;
    }

    // --- Cache helpers --------------------------------------------------------

    private function key(string $op, string $type, mixed $arg = null): string
    {
        return $op . ':' . $type . ':' . (is_array($arg) ? md5(json_encode($arg)) : (string) $arg);
    }

    private function has(string $key): bool
    {
        if (isset($this->memoryCache[$key])) {
            $entry = $this->memoryCache[$key];
            if ($entry['expires'] > microtime(true)) {
                return true;
            }
            unset($this->memoryCache[$key]);
        }

        $file = $this->cacheFile($key);

        if (is_file($file) && (filemtime($file) + $this->ttl) > time()) {
            $this->memoryCache[$key] = [
                'data' => unserialize((string) file_get_contents($file)),
                'expires' => microtime(true) + $this->ttl,
            ];

            return true;
        }

        return false;
    }

    private function get(string $key): mixed
    {
        return $this->memoryCache[$key]['data'] ?? null;
    }

    private function set(string $key, mixed $data): void
    {
        $this->memoryCache[$key] = [
            'data' => $data,
            'expires' => microtime(true) + $this->ttl,
        ];

        $dir = dirname($this->cacheFile($key));

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->cacheFile($key), serialize($data));
    }

    private function invalidateType(string $type): void
    {
        $prefix = ':' . $type . ':';

        foreach ($this->memoryCache as $key => &$entry) {
            if (str_contains($key, $prefix)) {
                unset($this->memoryCache[$key]);
            }
        }
        unset($entry);

        $dir = rtrim($this->cachePath, '/') . '/' . $type;

        if (is_dir($dir)) {
            $files = glob($dir . '/*.cache') ?: [];
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    private function cacheFile(string $key): string
    {
        return rtrim($this->cachePath, '/') . '/' . str_replace(['/', ':'], '_', $key) . '.cache';
    }
}
