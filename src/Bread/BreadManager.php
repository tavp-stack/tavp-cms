<?php

declare(strict_types=1);

namespace Tavp\Cms\Bread;

use Tavp\Cms\Content\ContentType;
use Tavp\Cms\Storage\ContentStore;

/**
 * The BREAD (Browse, Read, Edit, Add, Delete) manager.
 *
 * Holds the registry of content types and exposes the generic CRUD
 * operations that the admin (tavphub) and the front-end both use. New
 * content types can be registered at runtime — this is what makes custom
 * content types (Voyager-style) possible without per-type code.
 */
class BreadManager
{
    /** @var array<string,ContentType> */
    private array $types = [];

    public function __construct(
        private readonly ContentStore $store
    ) {
    }

    public function register(ContentType $type): void
    {
        $this->types[$type->name] = $type;
    }

    /**
     * Register many content types from a config array.
     *
     * @param array<string,array<string,mixed>> $config
     */
    public function registerFromConfig(array $config): void
    {
        foreach ($config as $name => $definition) {
            $this->register(ContentType::fromArray($name, $definition));
        }
    }

    public function type(string $name): ?ContentType
    {
        return $this->types[$name] ?? null;
    }

    /**
     * @return array<string,ContentType>
     */
    public function types(): array
    {
        return $this->types;
    }

    // --- BREAD operations --------------------------------------------------

    /**
     * Browse: list records of a type.
     *
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function browse(string $type, array $filters = []): array
    {
        return $this->store->all($this->must($type), $filters);
    }

    /**
     * Read: a single record by id.
     *
     * @return array<string,mixed>|null
     */
    public function read(string $type, string|int $id): ?array
    {
        return $this->store->find($this->must($type), $id);
    }

    /**
     * Read by slug (used by the front-end router).
     *
     * @return array<string,mixed>|null
     */
    public function readBySlug(string $type, string $slug): ?array
    {
        return $this->store->findBySlug($this->must($type), $slug);
    }

    /**
     * Add: create a record.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function add(string $type, array $data): array
    {
        return $this->store->create($this->must($type), $this->applyDefaults($type, $data));
    }

    /**
     * Edit: update a record.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function edit(string $type, string|int $id, array $data): array
    {
        return $this->store->update($this->must($type), $id, $data);
    }

    /**
     * Delete: remove a record.
     */
    public function delete(string $type, string|int $id): bool
    {
        return $this->store->delete($this->must($type), $id);
    }

    private function must(string $type): ContentType
    {
        return $this->types[$type]
            ?? throw new \InvalidArgumentException("Unknown content type: {$type}");
    }

    /**
     * Fill missing fields with their configured defaults + auto slug.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function applyDefaults(string $type, array $data): array
    {
        $contentType = $this->must($type);

        foreach ($contentType->fields as $field) {
            if (!array_key_exists($field->name, $data) && $field->default !== null) {
                $data[$field->name] = $field->default;
            }

            if ($field->type->value === 'slug' && empty($data[$field->name])) {
                $source = $field->from ? ($data[$field->from] ?? '') : '';
                $data[$field->name] = $this->slugify((string) $source);
            }
        }

        return $data;
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

        return trim($value, '-') ?: uniqid('c');
    }
}
