<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Generate and manage sitemaps — XML sitemap, ping search engines.
 */
class SitemapGenerator
{
    private array $config;

    public function __construct(private AdapterInterface $db, array $config = [])
    {
        $this->config = $config;
    }

    public function generate(): string
    {
        $baseUrl = $this->config['app_url'] ?? ($_SERVER['REQUEST_SCHEME'] ?? 'https') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $maxUrls = $this->config['sitemap']['max_urls'] ?? 50000;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $count = 0;

        $contentTypes = $this->getContentTypes();

        foreach ($contentTypes as $type) {
            $rows = $this->getPublishedContent($type['slug'], $maxUrls - $count);

            foreach ($rows as $row) {
                $url = $baseUrl . '/' . ($row['slug'] ?? $row['id']);
                $xml .= "  <url>\n";
                $xml .= "    <loc>{$url}</loc>\n";
                $xml .= '    <lastmod>' . date('Y-m-d', strtotime($row['updated_at'] ?? $row['created_at'])) . "</lastmod>\n";
                $xml .= '    <changefreq>' . ($type['changefreq'] ?? 'weekly') . "</changefreq>\n";
                $xml .= '    <priority>' . ($type['priority'] ?? '0.8') . "</priority>\n";
                $xml .= "  </url>\n";

                $count++;

                if ($count >= $maxUrls) {
                    break 2;
                }
            }
        }

        $xml .= "</urlset>\n";

        return $xml;
    }

    public function pingSearchEngines(): array
    {
        $baseUrl = $this->config['app_url'] ?? '';
        $sitemapUrl = $baseUrl . ($this->config['sitemap']['path'] ?? '/sitemap.xml');
        $results = [];

        if ($this->config['sitemap']['ping_google'] ?? true) {
            $results['google'] = $this->ping("https://www.google.com/ping?sitemap={$sitemapUrl}");
        }

        if ($this->config['sitemap']['ping_bing'] ?? true) {
            $results['bing'] = $this->ping("https://www.bing.com/indexnow?url={$sitemapUrl}");
        }

        return $results;
    }

    private function ping(string $url): bool
    {
        if (!function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    private function getContentTypes(): array
    {
        try {
            $sql = "SELECT * FROM content_types WHERE is_active = 1";
            return $this->db->fetchAll($sql) ?: [];
        } catch (\Throwable $e) {
            return [
                ['slug' => 'page', 'changefreq' => 'monthly', 'priority' => '1.0'],
                ['slug' => 'post', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ];
        }
    }

    private function getPublishedContent(string $type, int $limit): array
    {
        try {
            $sql = "SELECT * FROM contents WHERE type = ? AND status = 'published' ORDER BY updated_at DESC LIMIT ?";
            return $this->db->fetchAll($sql, null, [$type, $limit]) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
