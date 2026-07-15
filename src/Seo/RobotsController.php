<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Tavp\Core\Http\Response;

/**
 * Generate dynamic robots.txt.
 */
class RobotsController
{
    public function __invoke(): Response
    {
        $config = config('seo', []);
        $baseUrl = $config['app_url'] ?? '';

        $txt = "User-agent: *\n";

        $allow = $config['robots']['allow'] ?? ['/'];
        foreach ($allow as $path) {
            $txt .= "Allow: {$path}\n";
        }

        $disallow = $config['robots']['disallow'] ?? ['/admin', '/api'];
        foreach ($disallow as $path) {
            $txt .= "Disallow: {$path}\n";
        }

        $sitemapPath = $config['robots']['sitemap_url'] ?? '/sitemap.xml';
        $txt .= "\nSitemap: {$baseUrl}{$sitemapPath}\n";

        return (new Response($txt))
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
