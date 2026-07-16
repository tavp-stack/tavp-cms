<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

class DashboardController extends AdminController
{
    protected function adminPrefix(): string
    {
        $dbPrefix = null;
        try {
            $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
            $dbPrefix = $settings?->get('admin.route_prefix');
        } catch (\Throwable) {}
        return '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');
    }

    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $db = app('db');
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $weekStart = date('Y-m-d', strtotime('-7 days'));

        try {
            $pageviewsToday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $pageviewsYesterday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$yesterday])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $uniqueVisitors = $db->query("SELECT COUNT(DISTINCT session_id) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $realtime = $db->query("SELECT COUNT(*) as cnt FROM analytics_sessions WHERE last_activity_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $fraud = $db->query("SELECT COUNT(*) as cnt FROM analytics_fraud_events WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $weekViews = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) >= ?", [$weekStart])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $weekVisitors = $db->query("SELECT COUNT(DISTINCT session_id) as cnt FROM analytics_page_visits WHERE DATE(created_at) >= ?", [$weekStart])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $trafficData = $db->query("SELECT DATE(created_at) as day, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) >= ? GROUP BY DATE(created_at) ORDER BY day ASC", [$weekStart])->fetchAll(\PDO::FETCH_ASSOC);
            $topPages = $db->query("SELECT path, COUNT(*) as views FROM analytics_page_visits WHERE DATE(created_at) >= ? GROUP BY path ORDER BY views DESC LIMIT 5", [$weekStart])->fetchAll(\PDO::FETCH_ASSOC);
            $change = $pageviewsYesterday > 0 ? round(($pageviewsToday - $pageviewsYesterday) / $pageviewsYesterday * 100) : 0;
        } catch (\Throwable $e) {
            $pageviewsToday = 0; $pageviewsYesterday = 0; $uniqueVisitors = 0; $realtime = 0; $fraud = 0;
            $weekViews = 0; $weekVisitors = 0; $trafficData = []; $topPages = []; $change = 0;
        }

        return $this->admin('dashboard', [
            'counts' => $this->counts(),
            'pageviewsToday' => $pageviewsToday,
            'pageviewsYesterday' => $pageviewsYesterday,
            'uniqueVisitors' => $uniqueVisitors,
            'realtime' => $realtime,
            'fraud' => $fraud,
            'weekViews' => $weekViews,
            'weekVisitors' => $weekVisitors,
            'trafficData' => $trafficData,
            'topPages' => $topPages,
            'change' => $change,
        ]);
    }

    public function home(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        return $this->admin('home', ['counts' => $this->counts()]);
    }

    /**
     * @return array<string,int>
     */
    private function counts(): array
    {
        $counts = [];
        foreach ($this->bread()->types() as $name => $type) {
            $counts[$name] = count($this->bread()->browse($name));
        }

        return $counts;
    }
}
