<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Admin controller for contact-form messages (inbox style).
 */
class MessagesController extends AdminController
{
    public function index(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $filter = $_GET['filter'] ?? 'all';
        $allowed = ['all', 'unread', 'read', 'archived'];
        if (!in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $messages = $this->getMessages($filter);
        $counts = $this->getCounts();
        $selected = null;

        $selectedId = (int) ($_GET['id'] ?? 0);
        if ($selectedId > 0) {
            foreach ($messages as $m) {
                if ((int) $m['id'] === $selectedId) {
                    $selected = $m;
                    break;
                }
            }
            // Mark as read on open
            if ($selected !== null && $selected['status'] === 'unread') {
                $this->setStatus($selectedId, 'read');
                $selected['status'] = 'read';
                $counts['unread'] = max(0, $counts['unread'] - 1);
            }
        }

        return new Response($this->admin('messages', [
            'messages' => $messages,
            'counts' => $counts,
            'filter' => $filter,
            'selected' => $selected,
        ]));
    }

    public function markRead(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->setStatus($id, 'read');
            $this->flash('success', 'Marked as read.');
        }
        return $this->redirect($this->adminPrefix() . '/messages?id=' . $id);
    }

    public function archive(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->setStatus($id, 'archived');
            $this->flash('success', 'Message archived.');
        }
        return $this->redirect($this->adminPrefix() . '/messages');
    }

    public function destroy(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->setStatus($id, 'deleted');
            $this->flash('success', 'Message deleted.');
        }
        return $this->redirect($this->adminPrefix() . '/messages');
    }

    private function db(): \Phalcon\Db\Adapter\AdapterInterface
    {
        return app('db')->getAdapter();
    }

    private function getMessages(string $filter): array
    {
        try {
            $where = match ($filter) {
                'unread' => "WHERE status = 'unread'",
                'read' => "WHERE status = 'read'",
                'archived' => "WHERE status = 'archived'",
                default => "WHERE status != 'deleted'",
            };
            $rows = $this->db()->fetchAll(
                "SELECT * FROM messages {$where} ORDER BY created_at DESC LIMIT 200"
            );
            return $rows ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function getCounts(): array
    {
        try {
            $total = $this->db()->fetchOne("SELECT COUNT(*) c FROM messages WHERE status != 'deleted'");
            $unread = $this->db()->fetchOne("SELECT COUNT(*) c FROM messages WHERE status = 'unread'");
            $read = $this->db()->fetchOne("SELECT COUNT(*) c FROM messages WHERE status = 'read'");
            $archived = $this->db()->fetchOne("SELECT COUNT(*) c FROM messages WHERE status = 'archived'");
            return [
                'all' => (int) ($total['c'] ?? 0),
                'unread' => (int) ($unread['c'] ?? 0),
                'read' => (int) ($read['c'] ?? 0),
                'archived' => (int) ($archived['c'] ?? 0),
            ];
        } catch (\Throwable) {
            return ['all' => 0, 'unread' => 0, 'read' => 0, 'archived' => 0];
        }
    }

    private function setStatus(int $id, string $status): void
    {
        try {
            $this->db()->update('messages', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
        } catch (\Throwable) {
        }
    }
}
