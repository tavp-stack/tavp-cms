<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Cms\Bread\BreadManager;
use Tavp\Cms\Content\ContentType;
use Tavp\Core\Controllers\BaseController;
use Tavp\Core\Http\Response;
use Tavp\Tavpid\Rbac\AccessControl;

/**
 * Base for all admin controllers.
 *
 * Uses session-based auth and RBAC.
 */
abstract class AdminController extends BaseController
{
    protected ?AccessControl $rbac = null;

    protected function adminPrefix(): string
    {
        $dbPrefix = null;
        try {
            $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
            $dbPrefix = $settings?->get('admin.route_prefix');
        } catch (\Throwable) {}
        return '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');
    }

    public function __construct()
    {
        parent::__construct();
        $this->ensureSession();
        $this->initRbac();
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function initRbac(): void
    {
        try {
            $this->rbac = app()->getService('tavpid.rbac');
        } catch (\Throwable) {
            // Fallback: no RBAC configured
        }
    }

    protected function bread(): BreadManager
    {
        return app()->getService(BreadManager::class);
    }

    /**
     * Check if the current admin user has a permission.
     */
    protected function can(string $permission): bool
    {
        if ($this->rbac === null || empty($_SESSION['cms_admin'])) {
            return true; // no RBAC configured = allow all
        }

        $email = $_SESSION['cms_admin'];

        return $this->rbac->can([$this->rbac->role($email)], $permission);
    }

    /**
     * Redirect to login when not authenticated.
     */
    protected function guard(): ?Response
    {
        if (empty($_SESSION['cms_admin'])) {
            return $this->redirect($this->adminPrefix() . '/login');
        }

        return null;
    }

    /**
     * Get the current admin user email.
     */
    protected function adminUser(): ?string
    {
        return $_SESSION['cms_admin'] ?? null;
    }

    /**
     * Render an admin template wrapped in the admin layout.
     */
    protected function admin(string $template, array $data = []): string
    {
        $data['__auth_email'] = $this->adminUser();
        $data['__types'] = $this->safeTypes();
        $data['__brand'] = (string) config('cms.admin.brand', 'TAVP');
        $data['__rbac'] = $this->rbac;
        $data['__errors'] = $_SESSION['cms_errors'] ?? [];
        $data['__old'] = $_SESSION['cms_old'] ?? [];
        unset($_SESSION['cms_errors'], $_SESSION['cms_old']);

        // Admin prefix: read from DB first, fallback to config
        $dbPrefix = null;
        try {
            $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
            $dbPrefix = $settings?->get('admin.route_prefix');
        } catch (\Throwable) {}
        $data['adminPrefix'] = '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');

        $content = $this->partial($template, $data);

        return $this->partial('layout', array_merge($data, ['content' => $content]));
    }

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

    protected function flashErrors(array $errors): void
    {
        $_SESSION['cms_errors'] = $errors;
    }

    protected function flashOld(array $old): void
    {
        $_SESSION['cms_old'] = $old;
    }

    protected function flash(string $key, string $value): void
    {
        $_SESSION['cms_flash'][$key] = $value;
    }

    protected function getFlash(string $key): ?string
    {
        return $_SESSION['cms_flash'][$key] ?? null;
    }

    protected function clearFlash(): void
    {
        unset($_SESSION['cms_flash']);
    }

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

    /**
     * Collect form POST data filtered by content type fields.
     */
    protected function collect(ContentType $contentType): array
    {
        $data = [];
        foreach ($contentType->fields as $field) {
            if (isset($_POST[$field->name])) {
                $data[$field->name] = $_POST[$field->name];
            }
        }
        return $data;
    }
}
