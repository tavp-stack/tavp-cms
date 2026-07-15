<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

/**
 * Generate JSON-LD structured data schemas.
 */
class SchemaGenerator
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function generate(string $type, array $data): string
    {
        $schemaType = $this->config['schemas']['types'][$type] ?? 'WebPage';

        $schema = match ($schemaType) {
            'Article' => $this->article($data),
            'Product' => $this->product($data),
            'WebPage' => $this->webPage($data),
            'FAQPage' => $this->faqPage($data),
            'BreadcrumbList' => $this->breadcrumbList($data),
            default => $this->webPage($data),
        };

        if (!empty($this->config['schemas']['organization']['name'])) {
            $schema = $this->withOrganization($schema);
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    private function article(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? '',
            'description' => $data['meta_description'] ?? '',
            'image' => $data['og_image'] ?? '',
            'datePublished' => $data['published_at'] ?? date('c'),
            'dateModified' => $data['updated_at'] ?? date('c'),
            'author' => [
                '@type' => 'Person',
                'name' => $data['author'] ?? 'Admin',
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $data['url'] ?? '',
            ],
        ];
    }

    private function product(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $data['title'] ?? '',
            'description' => $data['meta_description'] ?? '',
        ];

        if (!empty($data['og_image'])) {
            $schema['image'] = $data['og_image'];
        }

        if (!empty($data['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $data['price'],
                'priceCurrency' => $data['currency'] ?? 'USD',
                'availability' => 'https://schema.org/InStock',
            ];
        }

        return $schema;
    }

    private function webPage(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $data['title'] ?? '',
            'description' => $data['meta_description'] ?? '',
            'url' => $data['url'] ?? '',
        ];
    }

    private function faqPage(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        if (!empty($data['faqs'])) {
            foreach ($data['faqs'] as $faq) {
                $schema['mainEntity'][] = [
                    '@type' => 'Question',
                    'name' => $faq['question'] ?? '',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer'] ?? '',
                    ],
                ];
            }
        }

        return $schema;
    }

    private function breadcrumbList(array $data): array
    {
        $items = [];
        $position = 1;

        if (!empty($data['breadcrumbs'])) {
            foreach ($data['breadcrumbs'] as $crumb) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $crumb['name'] ?? '',
                    'item' => $crumb['url'] ?? '',
                ];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    private function withOrganization(array $schema): array
    {
        $org = $this->config['schemas']['organization'];

        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => $org['name'],
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $org['logo'] ?? '',
            ],
        ];

        return $schema;
    }
}
