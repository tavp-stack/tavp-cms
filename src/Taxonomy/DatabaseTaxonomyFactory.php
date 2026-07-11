<?php

declare(strict_types=1);

namespace Tavp\Cms\Taxonomy;

/**
 * Builds a TaxonomyManager wired to the database.
 *
 * Works with the raw Phalcon PDO adapter bound as the "db" service:
 *  - query($sql, $bind) returns a Result (use ->fetchAll() / ->fetch()).
 *  - execute($sql, $bind) runs writes.
 *  - lastInsertId() returns the last auto-increment id.
 */
function buildDatabaseTaxonomy(object $db): TaxonomyManager
{
    $fetchAll = static function (string $sql, array $bind) use ($db): array {
        $result = $db->query($sql, $bind);
        return $result ? $result->fetchAll(\Phalcon\Db\Enum::FETCH_ASSOC) : [];
    };

    $fetchOne = static function (string $sql, array $bind) use ($db): ?array {
        $result = $db->query($sql, $bind);
        $row = $result ? $result->fetch(\Phalcon\Db\Enum::FETCH_ASSOC) : false;
        return is_array($row) ? $row : null;
    };

    $list = static function (string $type, string $orderBy) use ($fetchAll): array {
        $allowed = ['name', 'sort', 'created_at', 'updated_at'];
        $order = in_array($orderBy, $allowed, true) ? $orderBy : 'name';
        return $fetchAll(
            'SELECT * FROM taxonomy_terms WHERE type = :type ORDER BY ' . $order,
            ['type' => $type]
        );
    };

    $create = static function (string $type, string $name, string $slug) use ($db, $fetchOne): array {
        $now = date('Y-m-d H:i:s');
        $db->execute(
            'INSERT INTO taxonomy_terms (type, name, slug, created_at, updated_at)
             VALUES (:type, :name, :slug, :created_at, :updated_at)',
            ['type' => $type, 'name' => $name, 'slug' => $slug, 'created_at' => $now, 'updated_at' => $now]
        );

        return $fetchOne(
            'SELECT * FROM taxonomy_terms WHERE slug = :slug AND type = :type LIMIT 1',
            ['slug' => $slug, 'type' => $type]
        ) ?? [];
    };

    $update = static function (int $id, array $data) use ($db, $fetchOne): array {
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

        $db->execute(
            'UPDATE taxonomy_terms SET ' . implode(', ', $sets) . ' WHERE id = :id',
            $bind
        );

        return $fetchOne('SELECT * FROM taxonomy_terms WHERE id = :id LIMIT 1', ['id' => $id]) ?? [];
    };

    $delete = static function (int $id) use ($db): bool {
        return (bool) $db->execute('DELETE FROM taxonomy_terms WHERE id = :id', ['id' => $id]);
    };

    $findById = static function (int $id) use ($fetchOne): ?array {
        return $fetchOne('SELECT * FROM taxonomy_terms WHERE id = :id LIMIT 1', ['id' => $id]);
    };

    $findBySlug = static function (string $type, string $slug) use ($fetchOne): ?array {
        return $fetchOne(
            'SELECT * FROM taxonomy_terms WHERE type = :type AND slug = :slug LIMIT 1',
            ['type' => $type, 'slug' => $slug]
        );
    };

    $attach = static function (int $contentId, int $termId, string $contentType) use ($db, $fetchOne): void {
        $term = $fetchOne('SELECT type FROM taxonomy_terms WHERE id = :id LIMIT 1', ['id' => $termId]);
        $termType = $term['type'] ?? 'tag';

        $exists = $fetchOne(
            'SELECT COUNT(*) AS cnt FROM content_taxonomy WHERE content_id = :cid AND term_id = :tid AND content_type = :ct',
            ['cid' => $contentId, 'tid' => $termId, 'ct' => $contentType]
        );
        if ((int) ($exists['cnt'] ?? 0) > 0) {
            return;
        }

        $db->execute(
            'INSERT INTO content_taxonomy (content_id, content_type, term_id, term_type)
             VALUES (:cid, :ct, :tid, :tt)',
            ['cid' => $contentId, 'ct' => $contentType, 'tid' => $termId, 'tt' => $termType]
        );
    };

    $detach = static function (int $contentId, int $termId, string $contentType) use ($db): void {
        $db->execute(
            'DELETE FROM content_taxonomy WHERE content_id = :cid AND term_id = :tid AND content_type = :ct',
            ['cid' => $contentId, 'tid' => $termId, 'ct' => $contentType]
        );
    };

    $termsFor = static function (int $contentId, string $termType) use ($fetchAll): array {
        return $fetchAll(
            'SELECT t.* FROM taxonomy_terms t
             JOIN content_taxonomy ct ON ct.term_id = t.id
             WHERE ct.content_id = :cid AND ct.term_type = :tt
             ORDER BY t.name',
            ['cid' => $contentId, 'tt' => $termType]
        );
    };

    $contentForTerm = static function (int $termId, string $contentType, string $column) use ($fetchAll): array {
        return $fetchAll(
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
