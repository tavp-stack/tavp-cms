<?php

declare(strict_types=1);

namespace Tavp\Cms\Webhooks;

/**
 * Fires HTTP POST to registered webhook URLs on CMS events.
 */
class WebhookManager
{
    public function __construct(
        private readonly \Closure $loader,
        private readonly int $timeout = 5,
    ) {
    }

    /**
     * Fire an event to all matching webhooks.
     *
     * @param array<string,mixed> $payload
     * @return array<int,array{webhook_id:int,status:int|null}> delivery results
     */
    public function fire(string $event, array $payload): array
    {
        $webhooks = ($this->loader)($event);
        $results = [];

        foreach ($webhooks as $wh) {
            $status = $this->deliver($wh, $event, $payload);
            $results[] = ['webhook_id' => (int) $wh['id'], 'status' => $status];
        }

        return $results;
    }

    /**
     * Deliver a single webhook.
     */
    private function deliver(array $webhook, string $event, array $payload): ?int
    {
        $body = json_encode([
            'event' => $event,
            'timestamp' => date('c'),
            'data' => $payload,
        ]);

        $headers = [
            'Content-Type: application/json',
            'X-CMS-Event: ' . $event,
        ];

        if (!empty($webhook['secret'])) {
            $headers[] = 'X-CMS-Signature: ' . hash_hmac('sha256', $body, $webhook['secret']);
        }

        $ch = curl_init((string) $webhook['url']);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return $error ? null : (int) $status;
    }
}
