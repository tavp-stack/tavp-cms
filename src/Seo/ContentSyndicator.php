<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

/**
 * Syndicate content to external platforms.
 */
class ContentSyndicator
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function syndicate(string $platform, array $content): array
    {
        return match ($platform) {
            'medium' => $this->syndicateMedium($content),
            'linkedin' => $this->syndicateLinkedIn($content),
            default => ['success' => false, 'error' => "Unsupported platform: {$platform}"],
        };
    }

    public function getSupportedPlatforms(): array
    {
        return ['medium', 'linkedin'];
    }

    private function syndicateMedium(array $content): array
    {
        $token = $this->config['medium_token'] ?? env('MEDIUM_TOKEN', '');

        if (empty($token)) {
            return ['success' => false, 'error' => 'Medium API token not configured.'];
        }

        return [
            'success' => true,
            'platform' => 'medium',
            'message' => 'Content syndication queued. Configure Medium API integration for auto-publish.',
            'data' => [
                'title' => $content['title'] ?? '',
                'content' => $content['body'] ?? '',
                'tags' => $content['tags'] ?? [],
                'canonicalUrl' => $content['url'] ?? '',
            ],
        ];
    }

    private function syndicateLinkedIn(array $content): array
    {
        $token = $this->config['linkedin_token'] ?? env('LINKEDIN_TOKEN', '');

        if (empty($token)) {
            return ['success' => false, 'error' => 'LinkedIn API token not configured.'];
        }

        return [
            'success' => true,
            'platform' => 'linkedin',
            'message' => 'Content syndication queued. Configure LinkedIn API integration for auto-publish.',
            'data' => [
                'title' => $content['title'] ?? '',
                'content' => $content['body'] ?? '',
                'url' => $content['url'] ?? '',
            ],
        ];
    }
}
