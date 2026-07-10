<?php

declare(strict_types=1);

namespace Tavp\Cms\Storage;

use Tavp\Cms\Content\ContentType;

/**
 * The storage contract that every driver implements.
 *
 * This is the seam that lets TAVP CMS be database-driven (WordPress/Voyager
 * style) or flat-file (Statamic style) without the rest of the app caring.
 * A record is a plain associative array keyed by field name, always
 * containing at least: id, type, slug, status.
 */
interface ContentStore
{
    /**
     * List records of a content type, optionally filtered.
     *
     * @param array<string,mixed> $filters e.g. ['status' => 'published']
     * @return array<int,array<string,mixed>>
     */
    public function all(ContentType $type, array $filters = []): array;

    /**
     * Find a single record by its id.
     *
     * @return array<string,mixed>|null
     */
    public function find(ContentType $type, string|int $id): ?array;

    /**
     * Find a single record by its slug.
     *
     * @return array<string,mixed>|null
     */
    public function findBySlug(ContentType $type, string $slug): ?array;

    /**
     * Create a record. Returns the stored record (with generated id).
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function create(ContentType $type, array $data): array;

    /**
     * Update a record by id. Returns the updated record.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(ContentType $type, string|int $id, array $data): array;

    /**
     * Delete a record by id.
     */
    public function delete(ContentType $type, string|int $id): bool;
}
