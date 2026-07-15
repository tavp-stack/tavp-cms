<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Manage redirects — CRUD, match incoming URLs, track hits.
 */
class RedirectManager
{
    public function __construct(private AdapterInterface $db, private array $config = [])
    {
    }

    public function match(string $requestUri): ?array
    {
        $ignoreCase = $this->config['redirects']['ignore_case'] ?? true;
        $ignoreTrailing = $this->config['redirects']['ignore_trailing_slash'] ?? true;

        $normalizedUri = $requestUri;
        if ($ignoreTrailing && $normalizedUri !== '/') {
            $normalizedUri = rtrim($normalizedUri, '/');
        }

        $redirects = $this->getActiveRedirects();

        foreach ($redirects as $redirect) {
            $fromUrl = $redirect['from_url'];

            if ($ignoreTrailing && $fromUrl !== '/') {
                $fromUrl = rtrim($fromUrl, '/');
            }

            if (!empty($redirect['is_regex'])) {
                $pattern = $ignoreCase ? '#' . $fromUrl . '#i' : '#' . $fromUrl . '#';
                if (preg_match($pattern, $normalizedUri)) {
                    $toUrl = preg_replace($pattern, $redirect['to_url'], $normalizedUri);
                    $this->recordHit($redirect['id']);
                    return ['to' => $toUrl, 'status' => (int) $redirect['status_code']];
                }
            } else {
                $compareFrom = $ignoreCase ? mb_strtolower($fromUrl) : $fromUrl;
                $compareUri = $ignoreCase ? mb_strtolower($normalizedUri) : $normalizedUri;

                if ($compareFrom === $compareUri) {
                    $this->recordHit($redirect['id']);
                    return ['to' => $redirect['to_url'], 'status' => (int) $redirect['status_code']];
                }
            }
        }

        return null;
    }

    public function create(string $from, string $to, int $status = 301, bool $isRegex = false): bool
    {
        return $this->db->insert('redirects', [
            'from_url' => $from,
            'to_url' => $to,
            'status_code' => $status,
            'is_active' => 1,
            'is_regex' => $isRegex ? 1 : 0,
            'hits' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->db->delete('redirects', 'id = ?', [$id]);
    }

    public function all(): array
    {
        try {
            return $this->db->fetchAll('SELECT * FROM redirects ORDER BY created_at DESC') ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function getActiveRedirects(): array
    {
        try {
            return $this->db->fetchAll('SELECT * FROM redirects WHERE is_active = 1 ORDER BY id ASC') ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function recordHit(int $id): void
    {
        try {
            $this->db->execute(
                'UPDATE redirects SET hits = hits + 1, last_hit_at = ? WHERE id = ?',
                [date('Y-m-d H:i:s'), $id]
            );
        } catch (\Throwable) {}
    }
}
