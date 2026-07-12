<?php

declare(strict_types=1);

namespace Tavp\Cms\Bread;

use Tavp\Cms\Content\ContentType;
use Tavp\Cms\Content\Validator;
use Tavp\Cms\Content\ValidationException;
use Tavp\Cms\Revisions\RevisionStore;
use Tavp\Cms\Webhooks\WebhookManager;
use Tavp\Cms\Storage\ContentStore;

/**
 * The BREAD (Browse, Read, Edit, Add, Delete) manager.
 *
 * Holds the registry of content types and exposes the generic CRUD
 * operations. Integrates validation, revisions, webhooks, and search
 * as middleware on top of the storage layer.
 */
class BreadManager
{
    /** @var array<string,ContentType> */
    private array $types = [];

    private Validator $validator;

    public function __construct(
        private readonly ContentStore $store,
        private readonly ?RevisionStore $revisions = null,
        private readonly ?WebhookManager $webhooks = null,
    ) {
        $this->validator = new Validator();
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
     * Read: a single record by id or slug.
     *
     * @return array<string,mixed>|null
     */
    public function read(string $type, string|int $id): ?array
    {
        $contentType = $this->must($type);

        // If $id is numeric (int or numeric string), treat as ID; otherwise treat as slug.
        if (is_numeric($id)) {
            return $this->store->find($contentType, (int) $id);
        }

        return $this->store->findBySlug($contentType, (string) $id);
    }

    /**
     * ReadBySlug: a single record by slug.
     *
     * @return array<string,mixed>|null
     */
    public function readBySlug(string $type, string $slug): ?array
    {
        $contentType = $this->must($type);
        return $this->store->findBySlug($contentType, $slug);
    }

    /**
     * Add: create a record. Validates, snapshots, fires webhook.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     * @throws ValidationException
     */
    public function add(string $type, array $data): array
    {
        $contentType = $this->must($type);
        $data = $this->applyDefaults($type, $data);

        // Validate.
        $errors = $this->validator->validate($contentType, $data);
        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $record = $this->store->create($contentType, $data);

        // Revision snapshot.
        if ($this->revisions !== null) {
            $author = $_SESSION['cms_admin'] ?? null;
            $this->revisions->snapshot($type, $record['id'], $record, $author, 'Created');
        }

        // Webhook.
        if ($this->webhooks !== null) {
            $this->webhooks->fire('content.created', [
                'type' => $type,
                'id' => $record['id'],
                'data' => $record,
            ]);
        }

        return $record;
    }

    /**
     * Edit: update a record. Validates, snapshots old version, fires webhook.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     * @throws ValidationException
     */
    public function edit(string $type, string|int $id, array $data): array
    {
        $contentType = $this->must($type);
        $findId = is_numeric($id) ? (int) $id : $id;
        $existing = $this->store->find($contentType, $findId);

        if ($existing === null) {
            throw new \InvalidArgumentException("Record not found: {$type}/{$id}");
        }

        // Validate.
        $errors = $this->validator->validate($contentType, $data);
        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        // Snapshot old version before overwriting.
        if ($this->revisions !== null) {
            $author = $_SESSION['cms_admin'] ?? null;
            $this->revisions->snapshot($type, $id, $existing, $author, 'Before update');
        }

        $record = $this->store->update($contentType, $id, $data);

        // Webhook.
        if ($this->webhooks !== null) {
            $this->webhooks->fire('content.updated', [
                'type' => $type,
                'id' => $id,
                'data' => $record,
                'previous' => $existing,
            ]);
        }

        return $record;
    }

    /**
     * Delete: remove a record. Snapshots, fires webhook.
     */
    public function delete(string $type, string|int $id): bool
    {
        $contentType = $this->must($type);
        $deleteId = is_numeric($id) ? (int) $id : $id;

        // Snapshot before deleting.
        if ($this->revisions !== null) {
            $existing = $this->store->find($contentType, $deleteId);
            if ($existing !== null) {
                $author = $_SESSION['cms_admin'] ?? null;
                $this->revisions->snapshot($type, $deleteId, $existing, $author, 'Before delete');
            }
        }

        $result = $this->store->delete($contentType, $deleteId);

        // Webhook.
        if ($this->webhooks !== null) {
            $this->webhooks->fire('content.deleted', [
                'type' => $type,
                'id' => $id,
            ]);
        }

        return $result;
    }

    /**
     * Restore a content record from a revision snapshot.
     *
     * @param array<string,mixed> $snapshotData
     * @return array<string,mixed>
     */
    public function restore(string $type, string|int $id, array $snapshotData): array
    {
        $contentType = $this->must($type);
        $existing = $this->store->find($contentType, $id);

        if ($existing === null) {
            throw new \InvalidArgumentException("Record not found: {$type}/{$id}");
        }

        // Snapshot the current state before restoring.
        if ($this->revisions !== null) {
            $author = $_SESSION['cms_admin'] ?? null;
            $this->revisions->snapshot($type, $id, $existing, $author, 'Before rollback');
        }

        $record = $this->store->update($contentType, $id, $snapshotData);

        // Webhook.
        if ($this->webhooks !== null) {
            $this->webhooks->fire('content.updated', [
                'type' => $type,
                'id' => $id,
                'data' => $record,
                'reason' => 'rollback',
            ]);
        }

        return $record;
    }

    // --- Revision access ---------------------------------------------------

    public function revisions(): ?RevisionStore
    {
        return $this->revisions;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function history(string $type, string|int $id): array
    {
        if ($this->revisions === null) {
            return [];
        }

        return $this->revisions->history($type, $id);
    }

    // --- Helpers -----------------------------------------------------------

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
