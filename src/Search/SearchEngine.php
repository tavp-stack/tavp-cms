<?php

declare(strict_types=1);

namespace Tavp\Cms\Search;

use Tavp\Cms\Bread\BreadManager;

/**
 * Simple in-process full-text search over all registered content types.
 *
 * Scans records against a set of configured fields and scores matches.
 * Results are weighted by how many query terms match and how many fields
 * are hit. No external index required — fine for small-to-medium sites.
 *
 * For large sites, swap the scan method for an Elasticsearch/Meilisearch driver
 * by subclassing and overriding index() / search().
 */
class SearchEngine
{
    /**
     * @param string[] $searchFields field names to scan (e.g. title, body, excerpt, slug)
     */
    public function __construct(
        private readonly BreadManager $bread,
        private readonly array $searchFields = ['title', 'body', 'excerpt', 'slug'],
    ) {
    }

    /**
     * Search across all registered content types.
     *
     * @return array<int,array{content:array<string,mixed>,score:float,type:string}>
     */
    public function search(string $query, ?string $type = null, int $limit = 20): array
    {
        $terms = $this->tokenize($query);

        if ($terms === []) {
            return [];
        }

        $results = [];
        $types = $type !== null ? [$type => $this->bread->type($type)] : $this->bread->types();

        foreach ($types as $typeName => $contentType) {
            if ($contentType === null) {
                continue;
            }

            $records = $this->bread->browse($typeName);

            foreach ($records as $record) {
                $score = $this->score($record, $terms);

                if ($score > 0) {
                    $results[] = [
                        'content' => $record,
                        'score' => $score,
                        'type' => $typeName,
                    ];
                }
            }
        }

        usort($results, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $limit);
    }

    /**
     * Tokenize a search query into lowercase terms.
     *
     * @return string[]
     */
    private function tokenize(string $query): array
    {
        $query = mb_strtolower(trim($query));
        $terms = preg_split('/\s+/', $query) ?: [];

        return array_filter($terms, fn (string $t) => mb_strlen($t) >= 2);
    }

    /**
     * Score a record against search terms.
     *
     * Higher score = better match. Exact field matches score higher than
     * partial substring matches.
     */
    private function score(array $record, array $terms): float
    {
        $score = 0;

        foreach ($this->searchFields as $field) {
            $haystack = mb_strtolower((string) ($record[$field] ?? ''));

            if ($haystack === '') {
                continue;
            }

            foreach ($terms as $term) {
                if ($haystack === $term) {
                    $score += 10; // exact match
                } elseif (str_contains($haystack, $term)) {
                    $score += 3; // substring match
                }

                // Bonus: term at start of field.
                if (str_starts_with($haystack, $term)) {
                    $score += 2;
                }
            }
        }

        return $score;
    }
}
