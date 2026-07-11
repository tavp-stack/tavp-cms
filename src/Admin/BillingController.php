<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Billing dashboard — subscriptions, invoices, plans.
 */
class BillingController extends AdminController
{
    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $subscriptions = $this->db()->query(
            'SELECT s.*, u.name, u.email FROM subscriptions s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC',
            []
        );

        $stats = [
            'active' => $this->countByStatus('subscriptions', 'active'),
            'cancelled' => $this->countByStatus('subscriptions', 'cancelled'),
            'total_revenue' => $this->sumRevenue(),
        ];

        return $this->admin('billing.dashboard', [
            'subscriptions' => $subscriptions,
            'stats' => $stats,
        ]);
    }

    public function invoices(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $invoices = $this->db()->query(
            'SELECT i.*, u.name, u.email FROM invoices i LEFT JOIN users u ON i.user_id = u.id ORDER BY i.created_at DESC',
            []
        );

        return $this->admin('billing.invoices', [
            'invoices' => $invoices,
        ]);
    }

    public function cancelSubscription(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->db()->update('subscriptions', [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        $this->flash('success', 'Subscription cancelled.');

        return $this->redirect('/admin/billing');
    }

    private function countByStatus(string $table, string $status): int
    {
        $result = $this->db()->query("SELECT COUNT(*) as cnt FROM {$table} WHERE status = ?", [$status]);
        $rows = $result->fetchAll();
        return (int) ($rows[0]['cnt'] ?? 0);
    }

    private function sumRevenue(): float
    {
        $result = $this->db()->query("SELECT COALESCE(SUM(amount), 0) as total FROM invoices WHERE status = 'paid'", []);
        $rows = $result->fetchAll();
        return (float) ($rows[0]['total'] ?? 0);
    }

    private function db()
    {
        return app('db');
    }
}
