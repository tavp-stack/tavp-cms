<?php

declare(strict_types=1);

namespace Tavp\Cms\Taxonomy;

use Closure;
use Tavp\Cms\Content\ContentType;

/**
 * Taxonomy manager — CRUD for terms + attach/detach from content.
 *
 * Storage-agnostic: accepts closures for DB operations so it works with both
 * the database driver (Phalcon adapter) and the flat-file driver.
 */
class TaxonomyManager
{
    /**
     * @param Closure(string, string): array<int,array<string,mixed>> $listTerms
     * @param Closure(string, string, array{type?:string, name?:string, slug?:string, description?:string, parent_id?:int, sort?:int}): array<string,mixed> $createTerm
     * @param Closure(int, array{name?:string, slug?:string, description?:string, parent_id?:int, sort?:int}): array<string,mixed> $updateTerm
     * @param Closure(int): bool $deleteTerm
     * @param Closure(int, string): ?array<string,mixed> $findTermById
     * @param Closure(string, string): ?array<string,mixed> $findTermBySlug
     * @param Closure(int, int, string): void $attachContent
     * @param Closure(int, int, string): void $detachContent
     * @param Closure(int, string): array<int,array<string,mixed>> $getTermsForContent
     * @param Closure(string, string, string): array<int,array<string,mixed>> $getContentForTerm
     */
    public function __construct(
        private readonly Closure $listTerms,
        private readonly Closure $createTerm,
        private readonly Closure $updateTerm,
        private readonly Closure $deleteTerm,
        private readonly Closure $findTermById,
        private readonly Closure $findTermBySlug,
        private readonly Closure $attachContent,
        private readonly Closure $detachContent,
        private readonly Closure $getTermsForContent,
        private readonly Closure $getContentForTerm,
    ) {
    }

    // --- Term CRUD -----------------------------------------------------------

    /**
     * List all terms of a given type (category|tag).
     *
     * @return Term[]
     */
    public function all(string $type): array
    {
        $rows = ($this->listTerms)($type, 'name');
        return array_map(fn (array $r) => Term::fromRow($r), $rows);
    }

    /**
     * @return array<string,mixed>
     */
    public function find(int $id): ?array
    {
        return ($this->findTermById)($id);
    }

    public function findBySlug(string $type, string $slug): ?Term
    {
        $row = ($this->findTermBySlug)($type, $slug);

        return $row ? Term::fromRow($row) : null;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        $type = (string) ($data['type'] ?? 'tag');
        $data['slug'] = $this->slugify((string) ($data['name'] ?? uniqid('t')));

        return ($this->createTerm)($type, (string) ($data['name'] ?? ''), (string) $data['slug']);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(int $id, array $data): array
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = $this->slugify((string) $data['name']);
        }

        return ($this->updateTerm)($id, $data);
    }

    public function delete(int $id): bool
    {
        return ($this->deleteTerm)($id);
    }

    // --- Content ↔ Term relations --------------------------------------------

    /**
     * Attach a term to a content record.
     */
    public function attach(int $contentId, int $termId, string $contentType): void
    {
        ($this->attachContent)($contentId, $termId, $contentType);
    }

    /**
     * Detach a term from a content record.
     */
    public function detach(int $contentId, int $termId, string $contentType): void
    {
        ($this->detachContent)($contentId, $termId, $contentType);
    }

    /**
     * Get all terms of a given type attached to a content record.
     *
     * @return Term[]
     */
    public function termsFor(int $contentId, string $termType): array
    {
        $rows = ($this->getTermsForContent)($contentId, $termType);
        return array_map(fn (array $r) => Term::fromRow($r), $rows);
    }

    /**
     * Get all content IDs of a given content type that have a specific term.
     *
     * @return int[]
     */
    public function contentIdsWithTerm(int $termId, string $contentType): array
    {
        $rows = ($this->getContentForTerm)($termId, $contentType, 'content_id');
        return array_map(fn (array $r) => (int) $r['content_id'], $rows);
    }

    /**
     * Build the nested category tree.
     *
     * @param Term[] $terms
     * @return Term[]
     */
    public function tree(array $terms): array
    {
        $byParent = [];

        foreach ($terms as $term) {
            $parentId = $term->parentId ?? 0;
            $byParent[$parentId][] = $term;
        }

        return $this->buildBranch($byParent, 0);
    }

    /**
     * Synchronize term relations for a content record.
     * Accepts an array of term IDs; removes any not in the list, adds missing.
     *
     * @param int[] $termIds
     */
    public function sync(int $contentId, string $contentType, string $termType, array $termIds): void
    {
        $existing = $this->termsFor($contentId, $termType);
        $existingIds = array_map(fn (Term $t) => (int) $t->id, $existing);

        $toAdd = array_diff($termIds, $existingIds);
        $toRemove = array_diff($existingIds, $termIds);

        foreach ($toRemove as $id) {
            $this->detach($contentId, $id, $contentType);
        }

        foreach ($toAdd as $id) {
            $this->attach($contentId, $id, $contentType);
        }
    }

    // --- Helpers -------------------------------------------------------------

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

        return trim($value, '-') ?: uniqid('t');
    }

    /**
     * @param array<int|string, Term[]> $byParent
     * @return Term[]
     */
    private function buildBranch(array $byParent, int $parentId): array
    {
        $branch = [];

        foreach ($byParent[$parentId] ?? [] as $term) {
            $children = $this->buildBranch($byParent, (int) $term->id);
            $branch[] = new Term(
                id: $term->id,
                type: $term->type,
                name: $term->name,
                slug: $term->slug,
                description: $term->description,
                parentId: $term->parentId,
                sort: $term->sort,
                createdAt: $term->createdAt,
                updatedAt: $term->updatedAt,
                children: $children,
            );
        }

        usort($branch, fn (Term $a, Term $b) => $a->sort <=> $b->sort);

        return $branch;
    }
}
