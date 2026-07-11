<?php

declare(strict_types=1);

namespace Tavp\Cms\Auth;

use Tavp\Tavpid\Auth\UserProvider;

/**
 * CMS user provider — implements tavpid's UserProvider interface.
 *
 * Uses the contents table with type='user' for flat-file storage,
 * or the users table for database storage.
 */
class CmsUserProvider implements UserProvider
{
    public function findByIdentifier(string $identifier): ?object
    {
        try {
            $db = app('db');
            $result = $db->query(
                "SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1",
                [$identifier, $identifier]
            );

            $rows = $result->fetchAll();

            return !empty($rows) ? (object) $rows[0] : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function findById(int $id): ?object
    {
        try {
            $db = app('db');
            $result = $db->query("SELECT * FROM users WHERE id = ? LIMIT 1", [$id]);
            $rows = $result->fetchAll();

            return !empty($rows) ? (object) $rows[0] : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function create(string $identifier): object
    {
        $db = app('db');
        $now = date('Y-m-d H:i:s');

        $db->insert('users', [
            'name' => $identifier,
            'email' => $identifier,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $id = $db->lastInsertId();

        return (object) ['id' => $id, 'email' => $identifier, 'name' => $identifier];
    }
}
