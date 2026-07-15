<?php

declare(strict_types=1);

namespace Tavp\Cms\Seo;

use Tavp\Cms\Admin\AdminController;
use Tavp\Core\Http\Response;

/**
 * Admin controller for SEO settings, redirects, and analyzer.
 */
class SeoAdminController extends AdminController
{
    public function index(): Response
    {
        if ($this->guard()) {
            return $this->redirect('/admin/login');
        }

        $seoConfig = config('seo', []);
        $redirects = $this->getRedirects();
        $seoStats = $this->getSeoStats();

        return new Response($this->admin('seo_dashboard', [
            'config' => $seoConfig,
            'redirects' => $redirects,
            'stats' => $seoStats,
        ]));
    }

    public function settings(): Response
    {
        if ($this->guard()) {
            return $this->redirect('/admin/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveSettings($_POST);
            $this->flash('success', 'SEO settings saved.');
            return $this->redirect('/admin/seo');
        }

        $seoConfig = config('seo', []);

        return new Response($this->admin('seo_settings', [
            'config' => $seoConfig,
        ]));
    }

    public function redirects(): Response
    {
        if ($this->guard()) {
            return $this->redirect('/admin/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createRedirect($_POST);
            $this->flash('success', 'Redirect created.');
            return $this->redirect('/admin/seo/redirects');
        }

        $redirects = $this->getRedirects();

        return new Response($this->admin('seo_redirects', [
            'redirects' => $redirects,
        ]));
    }

    public function deleteRedirect(): Response
    {
        if ($this->guard()) {
            return $this->redirect('/admin/login');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->db()->delete('redirects', 'id = ?', [$id]);
            $this->flash('success', 'Redirect deleted.');
        }

        return $this->redirect('/admin/seo/redirects');
    }

    public function analyzer(): Response
    {
        if ($this->guard()) {
            return $this->redirect('/admin/login');
        }

        $contentType = $_GET['type'] ?? 'post';
        $contentId = (int) ($_GET['id'] ?? 0);

        $content = null;
        $analysis = null;

        if ($contentId > 0) {
            $content = $this->getContent($contentType, $contentId);
            $analyzer = new SeoAnalyzer(config('seo', []));
            $analysis = $analyzer->analyze($content, $contentType);
        }

        return new Response($this->admin('seo_analyzer', [
            'contentType' => $contentType,
            'contentId' => $contentId,
            'content' => $content,
            'analysis' => $analysis,
        ]));
    }

    public function ping(): Response
    {
        if ($this->guard()) {
            return $this->redirect('/admin/login');
        }

        $db = app('db')->getAdapter();
        $generator = new SitemapGenerator($db, config('seo', []));
        $results = $generator->pingSearchEngines();

        $this->flash('success', 'Sitemap pinged: ' . implode(', ', array_keys($results)));

        return $this->redirect('/admin/seo');
    }

    private function db(): \Phalcon\Db\Adapter\AdapterInterface
    {
        return app('db')->getAdapter();
    }

    private function getRedirects(): array
    {
        try {
            return $this->db()->fetchAll('SELECT * FROM redirects ORDER BY created_at DESC LIMIT 100') ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function getSeoStats(): array
    {
        $stats = ['total_meta' => 0, 'avg_score' => 0, 'redirects' => 0, 'broken_links' => 0];

        try {
            $row = $this->db()->fetchOne('SELECT COUNT(*) as cnt, COALESCE(AVG(seo_score), 0) as avg FROM seo_meta');
            $stats['total_meta'] = (int) ($row['cnt'] ?? 0);
            $stats['avg_score'] = (int) ($row['avg'] ?? 0);

            $row = $this->db()->fetchOne('SELECT COUNT(*) as cnt FROM redirects WHERE is_active = 1');
            $stats['redirects'] = (int) ($row['cnt'] ?? 0);

            $row = $this->db()->fetchOne('SELECT COUNT(*) as cnt FROM outbound_links WHERE is_broken = 1');
            $stats['broken_links'] = (int) ($row['cnt'] ?? 0);
        } catch (\Throwable) {}

        return $stats;
    }

    private function getContent(string $type, int $id): ?array
    {
        try {
            return $this->db()->fetchOne('SELECT * FROM contents WHERE type = ? AND id = ?', null, [$type, $id]) ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function createRedirect(array $post): void
    {
        $this->db()->insert('redirects', [
            'from_url' => $post['from_url'] ?? '/',
            'to_url' => $post['to_url'] ?? '/',
            'status_code' => (int) ($post['status_code'] ?? 301),
            'is_active' => 1,
            'is_regex' => !empty($post['is_regex']),
            'hits' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function saveSettings(array $post): void
    {
        $settings = app()->getService('settings');
        if ($settings === null) {
            return;
        }

        $fields = [
            'seo_title_suffix', 'seo_default_description', 'seo_og_image',
            'seo_twitter_handle', 'seo_google_verification', 'seo_bing_verification',
            'seo_google_analytics_id', 'seo_google_tag_manager',
            'seo_robots_txt', 'seo_schema_org',
        ];

        foreach ($fields as $field) {
            if (isset($post[$field])) {
                $settings->set($field, $post[$field]);
            }
        }
    }
}
