<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Cms\Auth\RbacGuard;
use Tavp\Cms\Bread\BreadManager;
use Tavp\Core\Controllers\BaseController;
use Tavp\Core\Http\Response;

/**
 * Base for all admin controllers.
 *
 * Renders self-contained PHP templates (no theme coupling) and provides a
 * simple auth guard with RBAC-lite support. Admin UI is deliberately plain
 * and fast.
 */
abstract class AdminController extends BaseController
{
    protected AdminAuth $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AdminAuth();
    }

    protected function bread(): BreadManager
    {
        return app()->getService(BreadManager::class);
    }

    /**
     * Get the RBAC guard (if configured).
     */
    protected function rbac(): ?RbacGuard
    {
        try {
            return app()->getService(RbacGuard::class);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Check if the current admin user has a permission.
     */
    protected function can(string $permission): bool
    {
        $rbac = $this->rbac();

        if ($rbac === null || !$this->auth->check()) {
            return true; // no RBAC configured = allow all
        }

        return $rbac->can((string) $this->auth->user(), $permission);
    }

    /**
     * Redirect to login when not authenticated. Returns a Response to return
     * early from the caller, or null when the request may proceed.
     */
    protected function guard(): ?Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/admin/login');
        }

        return null;
    }

    /**
     * Render an admin template wrapped in the admin layout.
     *
     * @param array<string,mixed> $data
     */
    protected function admin(string $template, array $data = []): string
    {
        $data['__auth'] = $this->auth;
        $data['__types'] = $this->safeTypes();
        $data['__brand'] = (string) config('cms.admin.brand', 'TAVP');
        $data['__rbac'] = $this->rbac();
        $data['__errors'] = $_SESSION['cms_errors'] ?? [];
        $data['__old'] = $_SESSION['cms_old'] ?? [];
        unset($_SESSION['cms_errors'], $_SESSION['cms_old']);

        $content = $this->partial($template, $data);

        return $this->partial('layout', array_merge($data, ['content' => $content]));
    }

    /**
     * Render a bare admin template (no layout).
     *
     * @param array<string,mixed> $data
     */
    protected function partial(string $template, array $data = []): string
    {
        $file = __DIR__ . '/../../resources/admin/' . $template . '.php';

        if (!is_file($file)) {
            return "<!-- admin template not found: {$template} -->";
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $file;

        return (string) ob_get_clean();
    }

    /**
     * Store validation errors in session for the next request (flash).
     *
     * @param array<string,string[]> $errors
     */
    protected function flashErrors(array $errors): void
    {
        $_SESSION['cms_errors'] = $errors;
    }

    /**
     * Store old input in session for repopulating the form.
     *
     * @param array<string,mixed> $old
     */
    protected function flashOld(array $old): void
    {
        $_SESSION['cms_old'] = $old;
    }

    /**
     * @return array<string,\Tavp\Cms\Content\ContentType>
     */
    private function safeTypes(): array
    {
        try {
            return $this->bread()->types();
        } catch (\Throwable) {
            return [];
        }
    }

    protected function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
