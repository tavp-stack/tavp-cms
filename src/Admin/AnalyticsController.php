<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Analytics dashboard controller.
 */
class AnalyticsController extends AdminController
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
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $monthAgo = date('Y-m-d', strtotime('-30 days'));

        // Period selector
        $period = $_GET['period'] ?? '7d';
        $periodDays = match($period) {
            '30d' => 30,
            '90d' => 90,
            default => 7,
        };
        $periodStart = date('Y-m-d', strtotime("-{$periodDays} days"));

        // Check for custom date range
        $customFrom = $_GET['from'] ?? null;
        $customTo = $_GET['to'] ?? null;
        $isCustom = false;
        if ($customFrom && $customTo) {
            $isCustom = true;
            $periodStart = min($customFrom, $customTo);
            $periodEnd = max($customFrom, $customTo);
            $periodDays = max(1, (int) ((strtotime($periodEnd) - strtotime($periodStart)) / 86400) + 1);
        }

        try {
            // Today's stats
            $pageviewsToday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $pageviewsYesterday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$yesterday])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $uniqueVisitors = $db->query("SELECT COUNT(DISTINCT session_id) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $realtime = $db->query("SELECT COUNT(*) as cnt FROM analytics_sessions WHERE last_activity_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $fraudEvents = $db->query("SELECT COUNT(*) as cnt FROM analytics_fraud_events WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;

            $range = [$periodStart, $periodEnd ?? $today];
            // Period totals
            $totalPageviews = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ?", $range)->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $totalVisitors = $db->query("SELECT COUNT(DISTINCT session_id) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ?", $range)->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
            $avgDaily = $periodDays > 0 ? round($totalPageviews / $periodDays) : 0;

            // Traffic chart data
            $trafficData = $db->query("SELECT DATE(created_at) as day, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY day ASC", $range)->fetchAll(\PDO::FETCH_ASSOC);

            // Top pages
            $topPages = $db->query("SELECT path, COUNT(*) as views FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY path ORDER BY views DESC LIMIT 10", $range)->fetchAll(\PDO::FETCH_ASSOC);

            // Device breakdown
            $devices = $db->query("SELECT device, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY device ORDER BY cnt DESC", $range)->fetchAll(\PDO::FETCH_ASSOC);

            // Browser breakdown
            $browsers = $db->query("SELECT browser, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY browser ORDER BY cnt DESC", $range)->fetchAll(\PDO::FETCH_ASSOC);

            // Geographic data
            $countries = $db->query("SELECT country, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? AND country IS NOT NULL AND country != '' GROUP BY country ORDER BY cnt DESC LIMIT 10", $range)->fetchAll(\PDO::FETCH_ASSOC);

            // Recent sessions
            $recentSessions = $db->query("SELECT * FROM analytics_sessions ORDER BY last_activity_at DESC LIMIT 10")->fetchAll(\PDO::FETCH_ASSOC);

            // Calculate percentage change
            $change = $pageviewsYesterday > 0 ? round(($pageviewsToday - $pageviewsYesterday) / $pageviewsYesterday * 100) : 0;

        } catch (\Throwable $e) {
            $pageviewsToday = 0; $pageviewsYesterday = 0; $uniqueVisitors = 0; $realtime = 0; $fraudEvents = 0;
            $totalPageviews = 0; $totalVisitors = 0; $avgDaily = 0;
            $trafficData = []; $topPages = []; $devices = []; $browsers = []; $countries = []; $recentSessions = [];
            $change = 0;
        }

        return $this->admin('analytics', [
            'period' => $period,
            'periodDays' => $periodDays,
            'isCustom' => $isCustom,
            'customFrom' => $customFrom,
            'customTo' => $customTo,
            'pageviewsToday' => $pageviewsToday,
            'pageviewsYesterday' => $pageviewsYesterday,
            'uniqueVisitors' => $uniqueVisitors,
            'realtime' => $realtime,
            'fraudEvents' => $fraudEvents,
            'totalPageviews' => $totalPageviews,
            'totalVisitors' => $totalVisitors,
            'avgDaily' => $avgDaily,
            'change' => $change,
            'trafficData' => $trafficData,
            'topPages' => $topPages,
            'devices' => $devices,
            'browsers' => $browsers,
            'countries' => $countries,
            'recentSessions' => $recentSessions,
        ]);
    }
}
