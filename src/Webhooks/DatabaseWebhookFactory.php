<?php

declare(strict_types=1);

namespace Tavp\Cms\Webhooks;

/**
 * Builds a WebhookManager wired to the database.
 */
function buildDatabaseWebhooks(object $db, int $timeout = 5): WebhookManager
{
    $loader = static function (string $event) use ($db): array {
        return $db->query(
            "SELECT * FROM webhooks WHERE active = 1 AND (events LIKE :e1 OR events LIKE :e2 OR events LIKE :e3 OR events = :e4)",
            [
                'e1' => $event . ',%',
                'e2' => '%,' . $event . ',%',
                'e3' => '%,' . $event,
                'e4' => '*',
            ]
        );
    };

    return new WebhookManager(loader: $loader, timeout: $timeout);
}
