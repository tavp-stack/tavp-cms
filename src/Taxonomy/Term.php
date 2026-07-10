<?php

declare(strict_types=1);

namespace Tavp\Cms\Taxonomy;

/**
 * A single taxonomy term (category or tag).
 */
class Term
{
    public function __construct(
        public readonly int|null $id,
        public readonly string $type,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description = null,
        public readonly ?int $parentId = null,
        public readonly int $sort = 0,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly array $children = [],
    ) {
    }

    /**
     * Build a Term from a database row.
     *
     * @param array<string,mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            type: (string) $row['type'],
            name: (string) $row['name'],
            slug: (string) $row['slug'],
            description: $row['description'] ?? null,
            parentId: isset($row['parent_id']) ? (int) $row['parent_id'] : null,
            sort: (int) ($row['sort'] ?? 0),
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return array_merge([
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'sort' => $this->sort,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ], $this->children !== [] ? ['children' => $this->children] : []);
    }
}
