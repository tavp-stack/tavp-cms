<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Track outbound links and detect broken ones.
 */
class LinkChecker
{
    public function __construct(private AdapterInterface $db)
    {
    }

    public function extractLinks(string $contentType, int $contentId, string $html): void
    {
        $this->db->delete('outbound_links', 'content_type = ? AND content_id = ?', [$contentType, $contentId]);

        if (!preg_match_all('/href="(https?:\/\/[^"]+)"/i', $html, $matches)) {
            return;
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';

        foreach ($matches[1] as $url) {
            $parsed = parse_url($url);
            if (($parsed['host'] ?? '') === $host) {
                continue;
            }

            $this->db->insert('outbound_links', [
                'content_type' => $contentType,
                'content_id' => $contentId,
                'url' => $url,
                'is_broken' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function checkAll(int $limit = 50): array
    {
        $results = [];

        try {
            $rows = $this->db->fetchAll(
                'SELECT * FROM outbound_links WHERE last_checked_at IS NULL OR last_checked_at < ? ORDER BY last_checked_at ASC LIMIT ?',
                null,
                [date('Y-m-d H:i:s', strtotime('-7 days')), $limit]
            );

            foreach ($rows as $row) {
                $statusCode = $this->checkUrl($row['url']);
                $isBroken = $statusCode >= 400 || $statusCode === 0;

                $this->db->update('outbound_links', [
                    'status_code' => $statusCode,
                    'is_broken' => $isBroken ? 1 : 0,
                    'last_checked_at' => date('Y-m-d H:i:s'),
                ], 'id = ?', [$row['id']]);

                $results[] = [
                    'url' => $row['url'],
                    'status' => $statusCode,
                    'broken' => $isBroken,
                ];
            }
        } catch (\Throwable) {}

        return $results;
    }

    public function getBrokenLinks(): array
    {
        try {
            return $this->db->fetchAll('SELECT * FROM outbound_links WHERE is_broken = 1 ORDER BY last_checked_at DESC') ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function checkUrl(string $url): int
    {
        if (!function_exists('curl_init')) {
            return 0;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code;
    }
}
