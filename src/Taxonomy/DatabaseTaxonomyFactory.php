<?php

declare(strict_types=1);

namespace Tavp\Cms\Taxonomy;

/**
 * Builds a TaxonomyManager wired to the database.
 *
 * Call buildDatabaseTaxonomy() in your CmsServiceProvider and pass app('db').
 */
function buildDatabaseTaxonomy(object $db): TaxonomyManager
{
    $list = static function (string $type, string $orderBy) use ($db): array {
        $sql = 'SELECT * FROM taxonomy_terms WHERE type = :type ORDER BY ' . $orderBy;
        return $db->query($sql, ['type' => $type]);
    };

    $create = static function (string $type, string $name, string $slug) use ($db): array {
        $now = date('Y-m-d H:i:s');
        $db->insert('taxonomy_terms', [
            'type' => $type,
            'name' => $name,
            'slug' => $slug,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $db->query(
            'SELECT * FROM taxonomy_terms WHERE slug = :slug AND type = :type LIMIT 1',
            ['slug' => $slug, 'type' => $type]
        )[0] ?? [];
    };

    $update = static function (int $id, array $data) use ($db): array {
        $sets = [];
        $bind = ['id' => $id];

        foreach (['name', 'slug', 'description', 'parent_id', 'sort'] as $key) {
            if (array_key_exists($key, $data)) {
                $sets[] = "{$key} = :{$key}";
                $bind[$key] = $data[$key];
            }
        }

        $sets[] = 'updated_at = :updated_at';
        $bind['updated_at'] = date('Y-m-d H:i:s');

        if ($sets !== []) {
            $db->update('taxonomy_terms', array_fill(0, count($sets), 1), array_combine($sets, array_values($bind)));
        }

        return $db->query('SELECT * FROM taxonomy_terms WHERE id = :id LIMIT 1', ['id' => $id])[0] ?? [];
    };

    $delete = static function (int $id) use ($db): bool {
        return $db->delete('taxonomy_terms', ['id' => $id]);
    };

    $findById = static function (int $id) use ($db): ?array {
        $rows = $db->query('SELECT * FROM taxonomy_terms WHERE id = :id LIMIT 1', ['id' => $id]);
        return $rows[0] ?? null;
    };

    $findBySlug = static function (string $type, string $slug) use ($db): ?array {
        $rows = $db->query(
            'SELECT * FROM taxonomy_terms WHERE type = :type AND slug = :slug LIMIT 1',
            ['type' => $type, 'slug' => $slug]
        );
        return $rows[0] ?? null;
    };

    $attach = static function (int $contentId, int $termId, string $contentType) use ($db): void {
        $termType = ($db->query(
            'SELECT type FROM taxonomy_terms WHERE id = :id LIMIT 1',
            ['id' => $termId]
        )[0] ?? ['type' => 'tag'])['type'];

        $exists = $db->query(
            'SELECT COUNT(*) AS cnt FROM content_taxonomy WHERE content_id = :cid AND term_id = :tid AND content_type = :ct',
            ['cid' => $contentId, 'tid' => $termId, 'ct' => $contentType]
        );
        if (($exists[0]['cnt'] ?? 0) > 0) {
            return;
        }

        $db->insert('content_taxonomy', [
            'content_id' => $contentId,
            'content_type' => $contentType,
            'term_id' => $termId,
            'term_type' => $termType,
        ]);
    };

    $detach = static function (int $contentId, int $termId, string $contentType) use ($db): void {
        $db->delete('content_taxonomy', [
            'content_id' => $contentId,
            'term_id' => $termId,
            'content_type' => $contentType,
        ]);
    };

    $termsFor = static function (int $contentId, string $termType) use ($db): array {
        return $db->query(
            'SELECT t.* FROM taxonomy_terms t
             JOIN content_taxonomy ct ON ct.term_id = t.id
             WHERE ct.content_id = :cid AND ct.term_type = :tt
             ORDER BY t.name',
            ['cid' => $contentId, 'tt' => $termType]
        );
    };

    $contentForTerm = static function (int $termId, string $contentType, string $column) use ($db): array {
        return $db->query(
            'SELECT content_id FROM content_taxonomy WHERE term_id = :tid AND content_type = :ct',
            ['tid' => $termId, 'ct' => $contentType]
        );
    };

    return new TaxonomyManager(
        listTerms: $list,
        createTerm: $create,
        updateTerm: $update,
        deleteTerm: $delete,
        findTermById: $findById,
        findTermBySlug: $findBySlug,
        attachContent: $attach,
        detachContent: $detach,
        getTermsForContent: $termsFor,
        getContentForTerm: $contentForTerm,
    );
}
