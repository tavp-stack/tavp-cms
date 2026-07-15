<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

/**
 * SEO content analyzer — scores and suggests improvements.
 */
class SeoAnalyzer
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config['analyzer'] ?? [];
    }

    public function analyze(array $content, string $contentType): array
    {
        $title = $content['title'] ?? '';
        $body = strip_tags($content['data'] ?? $content['body'] ?? '');
        $focusKeyword = $content['focus_keyword'] ?? '';

        $errors = [];
        $warnings = [];
        $suggestions = [];
        $score = 100;

        $titleLen = mb_strlen($title);
        $minTitle = $this->config['min_title_length'] ?? 30;
        $maxTitle = $this->config['max_title_length'] ?? 60;

        if ($titleLen === 0) {
            $errors[] = 'Title is missing.';
            $score -= 30;
        } elseif ($titleLen < $minTitle) {
            $warnings[] = "Title is too short ({$titleLen} chars, recommended: {$minTitle}-{$maxTitle}).";
            $score -= 10;
        } elseif ($titleLen > $maxTitle) {
            $warnings[] = "Title is too long ({$titleLen} chars, recommended: {$minTitle}-{$maxTitle}).";
            $score -= 5;
        }

        $descLen = mb_strlen($content['meta_description'] ?? '');
        $minDesc = $this->config['min_description_length'] ?? 120;
        $maxDesc = $this->config['max_description_length'] ?? 160;

        if ($descLen === 0) {
            $errors[] = 'Meta description is missing.';
            $score -= 20;
        } elseif ($descLen < $minDesc) {
            $warnings[] = "Meta description is too short ({$descLen} chars, recommended: {$minDesc}-{$maxDesc}).";
            $score -= 10;
        } elseif ($descLen > $maxDesc) {
            $warnings[] = "Meta description is too long ({$descLen} chars, recommended: {$minDesc}-{$maxDesc}).";
            $score -= 5;
        }

        if (empty($content['og_title'] ?? '')) {
            $warnings[] = 'Open Graph title is missing.';
            $score -= 5;
        }

        if (empty($content['og_image'] ?? '')) {
            $suggestions[] = 'Add an Open Graph image for better social sharing.';
            $score -= 5;
        }

        if (empty($content['canonical_url'] ?? '')) {
            $suggestions[] = 'Consider setting a canonical URL.';
            $score -= 3;
        }

        if (!empty($focusKeyword)) {
            $keywordCount = mb_substr_count(mb_strtolower($body), mb_strtolower($focusKeyword));
            $wordCount = str_word_count($body);
            $density = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;

            $minDensity = $this->config['min_keyword_density'] ?? 1.0;
            $maxDensity = $this->config['max_keyword_density'] ?? 3.0;

            if ($density < $minDensity) {
                $warnings[] = "Keyword density is too low ({$density}%, recommended: {$minDensity}-{$maxDensity}%).";
                $score -= 5;
            } elseif ($density > $maxDensity) {
                $warnings[] = "Keyword density is too high ({$density}%, recommended: {$minDensity}-{$maxDensity}%).";
                $score -= 10;
            }
        } else {
            $suggestions[] = 'Set a focus keyword for better optimization.';
            $score -= 5;
        }

        $imgCount = mb_substr_count($content['data'] ?? '', '<img');
        if ($imgCount === 0) {
            $suggestions[] = 'Add at least one image to your content.';
            $score -= 3;
        }

        if (mb_strlen($body) < 300) {
            $warnings[] = 'Content is very thin. Consider adding more content (300+ words recommended).';
            $score -= 10;
        }

        $score = max(0, min(100, $score));

        $preview = $this->generatePreview($title, $content['meta_description'] ?? '', $content['slug'] ?? '');

        return [
            'score' => $score,
            'errors' => $errors,
            'warnings' => $warnings,
            'suggestions' => $suggestions,
            'preview' => $preview,
        ];
    }

    private function generatePreview(string $title, string $description, string $slug): string
    {
        $title = $title ?: 'Page Title';
        $description = $description ?: 'Meta description will appear here...';
        $url = ($_SERVER['HTTP_HOST'] ?? 'example.com') . '/' . $slug;

        if (mb_strlen($title) > 60) {
            $title = mb_substr($title, 0, 57) . '...';
        }
        if (mb_strlen($description) > 160) {
            $description = mb_substr($description, 0, 157) . '...';
        }

        $html = '<div class="bg-white rounded p-4 max-w-xl">';
        $html .= '<div class="text-blue-700 text-lg font-medium hover:underline cursor-pointer">' . htmlspecialchars($title) . '</div>';
        $html .= '<div class="text-green-700 text-sm">' . htmlspecialchars($url) . '</div>';
        $html .= '<div class="text-gray-600 text-sm mt-1">' . htmlspecialchars($description) . '</div>';
        $html .= '</div>';

        return $html;
    }
}
