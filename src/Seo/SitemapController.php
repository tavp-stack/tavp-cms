<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Tavp\Cms\Bread\BreadManager;
use Tavp\Core\Http\Response;

/**
 * Generates a sitemap.xml from published content.
 */
class SitemapController
{
    public function __construct(
        private readonly BreadManager $bread,
    ) {
    }

    public function __invoke(): Response
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');

        $baseUrl = (string) config('app.url', (string) env('APP_URL', 'http://localhost'));

        foreach ($this->bread->types() as $type => $contentType) {
            $route = $contentType->route;

            if ($route === '') {
                continue;
            }

            $records = $this->bread->browse($type, ['status' => 'published']);

            foreach ($records as $record) {
                $url = $this->buildUrl($baseUrl, $route, $record);
                $node = $xml->addChild('url');
                $node->addChild('loc', $url);
                $node->addChild('lastmod', date('c', strtotime((string) ($record['updated_at'] ?? date('c')))));
                $node->addChild('changefreq', $type === 'post' ? 'weekly' : 'monthly');
                $node->addChild('priority', $type === 'page' ? '1.0' : '0.8');
            }
        }

        $body = $xml->asXML();

        return response($body)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function buildUrl(string $baseUrl, string $route, array $record): string
    {
        $path = str_replace('{slug}', (string) ($record['slug'] ?? ''), $route);

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
