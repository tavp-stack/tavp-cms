<?php

declare(strict_types=1);

namespace Tavp\Cms\Storage;

use Tavp\Cms\Content\ContentType;

/**
 * Database-backed content store (WordPress/Voyager style).
 *
 * Every content type shares a single "contents" table with a JSON `data`
 * column for its custom fields, plus first-class columns for the fields
 * every record needs (type, slug, status). This keeps custom content types
 * (BREAD) working without a schema migration per type.
 */
class DatabaseStore implements ContentStore
{
    public function __construct(
        private readonly \Closure $connection
    ) {
    }

    /**
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function all(ContentType $type, array $filters = []): array
    {
        $db = ($this->connection)();

        $where = ['type = :type'];
        $bind = ['type' => $type->name];

        foreach ($filters as $key => $value) {
            if (in_array($key, ['status', 'slug'], true)) {
                $where[] = "{$key} = :{$key}";
                $bind[$key] = $value;
            }
        }

        $sql = 'SELECT * FROM contents WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC';
        $rows = $db->query($sql, $bind);

        return array_map([$this, 'hydrate'], $rows);
    }

    public function find(ContentType $type, string|int $id): ?array
    {
        $db = ($this->connection)();
        $rows = $db->query(
            'SELECT * FROM contents WHERE type = :type AND id = :id LIMIT 1',
            ['type' => $type->name, 'id' => $id]
        );

        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function findBySlug(ContentType $type, string $slug): ?array
    {
        $db = ($this->connection)();
        $rows = $db->query(
            'SELECT * FROM contents WHERE type = :type AND slug = :slug LIMIT 1',
            ['type' => $type->name, 'slug' => $slug]
        );

        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function create(ContentType $type, array $data): array
    {
        $db = ($this->connection)();
        $record = $this->splitColumns($type, $data);

        $id = $db->insert('contents', [
            'type' => $type->name,
            'slug' => $record['slug'],
            'status' => $record['status'],
            'data' => json_encode($record['data']),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->find($type, $id) ?? [];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(ContentType $type, string|int $id, array $data): array
    {
        $db = ($this->connection)();
        $record = $this->splitColumns($type, $data);

        $db->update('contents', [
            'slug' => $record['slug'],
            'status' => $record['status'],
            'data' => json_encode($record['data']),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id, 'type' => $type->name]);

        return $this->find($type, $id) ?? [];
    }

    public function delete(ContentType $type, string|int $id): bool
    {
        $db = ($this->connection)();

        return $db->delete('contents', ['id' => $id, 'type' => $type->name]);
    }

    /**
     * Separate first-class columns (slug, status) from the JSON data blob.
     *
     * @param array<string,mixed> $data
     * @return array{slug:string,status:string,data:array<string,mixed>}
     */
    private function splitColumns(ContentType $type, array $data): array
    {
        $slug = (string) ($data['slug'] ?? '');
        $status = (string) ($data['status'] ?? 'draft');

        unset($data['id'], $data['type'], $data['slug'], $data['status']);

        return ['slug' => $slug, 'status' => $status, 'data' => $data];
    }

    /**
     * Flatten a DB row back into a single record array.
     *
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function hydrate(array $row): array
    {
        $data = [];

        if (!empty($row['data'])) {
            $decoded = json_decode((string) $row['data'], true);
            $data = is_array($decoded) ? $decoded : [];
        }

        return array_merge($data, [
            'id' => $row['id'],
            'type' => $row['type'],
            'slug' => $row['slug'],
            'status' => $row['status'],
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ]);
    }
}
