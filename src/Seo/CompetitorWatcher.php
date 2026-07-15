<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

/**
 * Competitor content monitoring.
 */
class CompetitorWatcher
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return [
            'status' => 'available',
            'features' => [
                'title_comparison' => 'Compare your titles with competitor pages',
                'content_length' => 'Benchmark content length against competitors',
                'keyword_gaps' => 'Identify keywords competitors target that you don\'t',
            ],
            'setup' => [
                'Add competitor URLs to config/seo.php under competitor_watcher.urls',
                'Run tavp seo:check-competitors to analyze',
            ],
        ];
    }

    public function compareContent(array $yourContent, array $competitorContent): array
    {
        $yourWordCount = str_word_count(strip_tags($yourContent['body'] ?? ''));
        $theirWordCount = str_word_count(strip_tags($competitorContent['body'] ?? ''));

        return [
            'your_word_count' => $yourWordCount,
            'competitor_word_count' => $theirWordCount,
            'difference' => $theirWordCount - $yourWordCount,
            'recommendation' => $yourWordCount < $theirWordCount
                ? "Consider adding more content. Competitor has {$theirWordCount} words vs your {$yourWordCount}."
                : 'Your content is longer than the competitor.',
        ];
    }
}
