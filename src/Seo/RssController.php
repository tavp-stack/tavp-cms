<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Tavp\Core\Http\Response;

/**
 * Generate RSS/Atom feed for published content.
 */
class RssController
{
    public function feed(): Response
    {
        $db = app('db')->getAdapter();
        $config = config('seo', []);
        $limit = $config['rss']['limit'] ?? 20;

        $baseUrl = $config['app_url'] ?? ($_SERVER['REQUEST_SCHEME'] ?? 'https') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $title = $config['rss']['title'] ?? config('app.name', 'TAVP CMS');
        $description = $config['rss']['description'] ?? '';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= "<channel>\n";
        $xml .= "  <title>{$title}</title>\n";
        $xml .= "  <link>{$baseUrl}</link>\n";
        $xml .= "  <description>{$description}</description>\n";
        $xml .= "  <language>en</language>\n";
        $xml .= '  <atom:link href="' . $baseUrl . '/feed" rel="self" type="application/rss+xml"/>' . "\n";
        $xml .= '  <lastBuildDate>' . date(DATE_RFC2822) . "</lastBuildDate>\n";

        try {
            $rows = $db->fetchAll(
                "SELECT * FROM contents WHERE status = 'published' ORDER BY published_at DESC LIMIT ?",
                null,
                [$limit]
            );

            foreach ($rows as $row) {
                $url = $baseUrl . '/' . ($row['slug'] ?? $row['id']);
                $pubDate = date(DATE_RFC2822, strtotime($row['published_at'] ?? $row['created_at']));
                $body = strip_tags($row['data'] ?? '');
                if (mb_strlen($body) > 300) {
                    $body = mb_substr($body, 0, 297) . '...';
                }

                $xml .= "  <item>\n";
                $xml .= "    <title>" . htmlspecialchars($row['title'] ?? '', ENT_XML1) . "</title>\n";
                $xml .= "    <link>{$url}</link>\n";
                $xml .= "    <guid isPermaLink=\"true\">{$url}</guid>\n";
                $xml .= "    <pubDate>{$pubDate}</pubDate>\n";
                $xml .= "    <description>" . htmlspecialchars($body, ENT_XML1) . "</description>\n";
                $xml .= "  </item>\n";
            }
        } catch (\Throwable) {
            // Empty feed
        }

        $xml .= "</channel>\n</rss>\n";

        return (new Response($xml))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
