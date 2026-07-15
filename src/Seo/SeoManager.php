<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Core SEO manager — reads/writes SEO meta for content records.
 */
class SeoManager
{
    private array $config;

    public function __construct(private AdapterInterface $db, array $config = [])
    {
        $this->config = $config;
    }

    public function get(string $contentType, int $contentId): ?array
    {
        $sql = 'SELECT * FROM seo_meta WHERE content_type = ? AND content_id = ?';

        $row = $this->db->fetchOne($sql, null, [$contentType, $contentId]);

        return $row ?: null;
    }

    public function save(string $contentType, int $contentId, array $data): bool
    {
        $existing = $this->get($contentType, $contentId);

        $record = [
            'content_type' => $contentType,
            'content_id' => $contentId,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_image' => $data['og_image'] ?? null,
            'og_type' => $data['og_type'] ?? null,
            'twitter_title' => $data['twitter_title'] ?? null,
            'twitter_description' => $data['twitter_description'] ?? null,
            'twitter_image' => $data['twitter_image'] ?? null,
            'twitter_card' => $data['twitter_card'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'robots' => $data['robots'] ?? null,
            'schema_type' => $data['schema_type'] ?? null,
            'schema_data' => isset($data['schema_data']) ? json_encode($data['schema_data']) : null,
            'seo_score' => $data['seo_score'] ?? null,
            'focus_keyword' => $data['focus_keyword'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            return $this->db->update('seo_meta', $record, 'content_type = ? AND content_id = ?', [$contentType, $contentId]);
        }

        $record['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('seo_meta', $record);
    }

    public function delete(string $contentType, int $contentId): bool
    {
        return $this->db->delete('seo_meta', 'content_type = ? AND content_id = ?', [$contentType, $contentId]);
    }

    public function generateMetaTags(string $contentType, int $contentId, array $contentData = []): string
    {
        $seo = $this->get($contentType, $contentId);
        $config = $this->config;

        $title = $seo['meta_title'] ?? $contentData['title'] ?? '';
        $description = $seo['meta_description'] ?? $contentData['body'] ?? '';

        if (mb_strlen($description) > 160) {
            $description = mb_substr($description, 0, 157) . '...';
        }

        $suffix = $config['meta']['title_suffix'] ?? '';
        $separator = $config['meta']['separator'] ?? ' | ';
        $fullTitle = $suffix ? "{$title}{$separator}{$suffix}" : $title;

        $html = '';
        $html .= "<title>{$fullTitle}</title>\n";
        $html .= "<meta name=\"description\" content=\"{$description}\">\n";

        if (!empty($seo['meta_keywords'])) {
            $html .= "<meta name=\"keywords\" content=\"{$seo['meta_keywords']}\">\n";
        }

        if (!empty($seo['robots'])) {
            $html .= "<meta name=\"robots\" content=\"{$seo['robots']}\">\n";
        } else {
            $html .= "<meta name=\"robots\" content=\"index, follow\">\n";
        }

        if (!empty($seo['canonical_url'])) {
            $html .= "<link rel=\"canonical\" href=\"{$seo['canonical_url']}\">\n";
        }

        if ($config['open_graph']['enabled'] ?? true) {
            $html .= $this->generateOpenGraph($seo, $contentData, $title, $description);
        }

        if ($config['twitter']['enabled'] ?? true) {
            $html .= $this->generateTwitterCard($seo, $contentData, $title, $description);
        }

        if ($config['webmaster']['google_verification'] ?? '') {
            $html .= "<meta name=\"google-site-verification\" content=\"{$config['webmaster']['google_verification']}\">\n";
        }

        if ($config['webmaster']['bing_verification'] ?? '') {
            $html .= "<meta name=\"msvalidate.01\" content=\"{$config['webmaster']['bing_verification']}\">\n";
        }

        if ($config['analytics']['google_analytics_id'] ?? '') {
            $gaId = $config['analytics']['google_analytics_id'];
            $html .= "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$gaId}\"></script>\n";
            $html .= "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$gaId}');</script>\n";
        }

        return $html;
    }

    private function generateOpenGraph(?array $seo, array $contentData, string $title, string $description): string
    {
        $html = '';
        $html .= "<meta property=\"og:title\" content=\"" . ($seo['og_title'] ?? $title) . "\">\n";
        $html .= "<meta property=\"og:description\" content=\"" . ($seo['og_description'] ?? $description) . "\">\n";
        $html .= "<meta property=\"og:type\" content=\"" . ($seo['og_type'] ?? 'website') . "\">\n";

        if (!empty($seo['og_image'])) {
            $html .= "<meta property=\"og:image\" content=\"{$seo['og_image']}\">\n";
        }

        if (!empty($contentData['url'])) {
            $html .= "<meta property=\"og:url\" content=\"{$contentData['url']}\">\n";
        }

        return $html;
    }

    private function generateTwitterCard(?array $seo, array $contentData, string $title, string $description): string
    {
        $html = '';
        $cardType = $seo['twitter_card'] ?? 'summary_large_image';

        $html .= "<meta name=\"twitter:card\" content=\"{$cardType}\">\n";
        $html .= "<meta name=\"twitter:title\" content=\"" . ($seo['twitter_title'] ?? $title) . "\">\n";
        $html .= "<meta name=\"twitter:description\" content=\"" . ($seo['twitter_description'] ?? $description) . "\">\n";

        if (!empty($seo['twitter_image'])) {
            $html .= "<meta name=\"twitter:image\" content=\"{$seo['twitter_image']}\">\n";
        }

        return $html;
    }
}
