<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Monitor backlinks via API integration.
 */
class BacklinkMonitor
{
    public function __construct(private AdapterInterface $db, private array $config = [])
    {
    }

    public function getStats(): array
    {
        $stats = [
            'total_outbound' => 0,
            'broken_outbound' => 0,
            'healthy_outbound' => 0,
        ];

        try {
            $row = $this->db->fetchOne('SELECT COUNT(*) as cnt FROM outbound_links');
            $stats['total_outbound'] = (int) ($row['cnt'] ?? 0);

            $row = $this->db->fetchOne('SELECT COUNT(*) as cnt FROM outbound_links WHERE is_broken = 1');
            $stats['broken_outbound'] = (int) ($row['cnt'] ?? 0);

            $stats['healthy_outbound'] = $stats['total_outbound'] - $stats['broken_outbound'];
        } catch (\Throwable) {}

        return $stats;
    }

    public function getOutboundLinks(int $limit = 100): array
    {
        try {
            return $this->db->fetchAll('SELECT * FROM outbound_links ORDER BY created_at DESC LIMIT ?', null, [$limit]) ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function getBacklinkReport(): array
    {
        $report = [
            'outbound_by_status' => [],
            'broken_links' => [],
            'recent_links' => [],
        ];

        try {
            $report['outbound_by_status'] = $this->db->fetchAll(
                'SELECT status_code, COUNT(*) as cnt FROM outbound_links GROUP BY status_code ORDER BY cnt DESC'
            ) ?: [];

            $report['broken_links'] = $this->db->fetchAll(
                'SELECT * FROM outbound_links WHERE is_broken = 1 ORDER BY last_checked_at DESC LIMIT 50'
            ) ?: [];

            $report['recent_links'] = $this->db->fetchAll(
                'SELECT * FROM outbound_links ORDER BY created_at DESC LIMIT 20'
            ) ?: [];
        } catch (\Throwable) {}

        return $report;
    }
}
