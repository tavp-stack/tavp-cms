<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

/**
 * AI-assisted meta tag generation.
 */
class AiMetaGenerator
{
    public function generateMetaTags(string $title, string $body): array
    {
        $description = $this->generateDescription($body);
        $keywords = $this->extractKeywords($body);
        $ogTitle = $this->generateOgTitle($title);

        return [
            'meta_title' => $title,
            'meta_description' => $description,
            'meta_keywords' => implode(', ', $keywords),
            'og_title' => $ogTitle,
            'og_description' => $description,
            'focus_keyword' => $keywords[0] ?? '',
        ];
    }

    private function generateDescription(string $body): string
    {
        $body = strip_tags($body);
        $body = preg_replace('/\s+/', ' ', $body);
        $body = trim($body);

        if (mb_strlen($body) <= 160) {
            return $body;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $body);
        $description = '';

        foreach ($sentences as $sentence) {
            if (mb_strlen($description) + mb_strlen($sentence) > 155) {
                break;
            }
            $description .= ($description ? ' ' : '') . $sentence;
        }

        if (mb_strlen($description) > 155) {
            $description = mb_substr($description, 0, 152) . '...';
        }

        return $description;
    }

    private function extractKeywords(string $body, int $limit = 5): array
    {
        $body = strip_tags($body);
        $body = mb_strtolower($body);

        $stopWords = ['the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
            'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should',
            'may', 'might', 'must', 'shall', 'can', 'need', 'dare', 'ought', 'used',
            'to', 'of', 'in', 'for', 'on', 'with', 'at', 'by', 'from', 'as', 'into',
            'through', 'during', 'before', 'after', 'above', 'below', 'between', 'out',
            'and', 'but', 'or', 'nor', 'not', 'so', 'yet', 'both', 'either', 'neither',
            'each', 'every', 'all', 'any', 'few', 'more', 'most', 'other', 'some', 'such',
            'no', 'only', 'own', 'same', 'than', 'too', 'very', 'just', 'because', 'if',
            'when', 'where', 'how', 'what', 'which', 'who', 'whom', 'this', 'that', 'these',
            'those', 'i', 'me', 'my', 'we', 'our', 'you', 'your', 'he', 'him', 'his',
            'she', 'her', 'it', 'its', 'they', 'them', 'their', 'di', 'dan', 'yang', 'ini',
            'itu', 'untuk', 'dengan', 'pada', 'dari', 'ke', 'oleh', 'sebagai', 'jika'];

        $words = preg_split('/[^\p{L}\p{N}]+/u', $body);
        $words = array_filter($words, fn($w) => mb_strlen($w) > 2 && !in_array($w, $stopWords, true));

        $freq = array_count_values($words);
        arsort($freq);

        return array_slice(array_keys($freq), 0, $limit);
    }

    private function generateOgTitle(string $title): string
    {
        if (mb_strlen($title) <= 60) {
            return $title;
        }

        return mb_substr($title, 0, 57) . '...';
    }
}
