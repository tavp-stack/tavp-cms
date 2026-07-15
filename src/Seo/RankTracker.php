<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

/**
 * Rank tracking via Google Search Console API.
 */
class RankTracker
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return [
            'google_search_console' => [
                'configured' => !empty($this->config['google_search_console']['api_key'] ?? ''),
                'setup_url' => 'https://search.google.com/search-console',
                'instructions' => [
                    '1. Go to Google Search Console',
                    '2. Add and verify your property',
                    '3. Generate API credentials in Google Cloud Console',
                    '4. Add API key to config/seo.php',
                ],
            ],
            'features' => [
                'keyword_tracking' => 'Track keyword positions over time',
                'click_through_rates' => 'Monitor CTR for pages',
                'impression_data' => 'See how often pages appear in search',
                'position_history' => 'Historical ranking data',
            ],
        ];
    }

    public function getDashboardData(): array
    {
        return [
            'status' => 'not_configured',
            'message' => 'Connect Google Search Console API to enable rank tracking.',
            'setup_url' => 'https://search.google.com/search-console',
            'features' => [
                'Track keyword rankings over time',
                'Monitor click-through rates',
                'View impression and click data',
                'Identify top-performing pages',
            ],
        ];
    }
}
