<?php

declare(strict_types=1);

namespace Tavp\Cms\Publishing;

use Tavp\Cms\Bread\BreadManager;

/**
 * Publishes content whose published_at date has arrived.
 *
 * Run periodically via cron: tavp cms:publish
 */
class PublishScheduler
{
    public function __construct(
        private readonly BreadManager $bread,
    ) {
    }

    /**
     * Find and publish all content due for publication.
     *
     * @return array<int,array{type:string,id:string,title:string}>
     */
    public function publishDue(): array
    {
        $published = [];

        foreach ($this->bread->types() as $type => $contentType) {
            $records = $this->bread->browse($type, ['status' => 'draft']);

            foreach ($records as $record) {
                $publishedAt = $record['published_at'] ?? null;

                if ($publishedAt !== null && $publishedAt !== '' && strtotime((string) $publishedAt) <= time()) {
                    $this->bread->edit($type, $record['id'], ['status' => 'published']);
                    $published[] = [
                        'type' => $type,
                        'id' => (string) $record['id'],
                        'title' => (string) ($record['title'] ?? 'Untitled'),
                    ];
                }
            }

            // Also check 'scheduled' status.
            $scheduled = $this->bread->browse($type, ['status' => 'scheduled']);

            foreach ($scheduled as $record) {
                $publishedAt = $record['published_at'] ?? null;

                if ($publishedAt !== null && $publishedAt !== '' && strtotime((string) $publishedAt) <= time()) {
                    $this->bread->edit($type, $record['id'], ['status' => 'published']);
                    $published[] = [
                        'type' => $type,
                        'id' => (string) $record['id'],
                        'title' => (string) ($record['title'] ?? 'Untitled'),
                    ];
                }
            }
        }

        return $published;
    }
}
