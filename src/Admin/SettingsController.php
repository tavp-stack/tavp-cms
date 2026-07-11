<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Settings admin — manage site settings (key/value pairs).
 */
class SettingsController extends AdminController
{
    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $settings = $this->getSettings();

        return $this->admin('settings.list', [
            'settings' => $settings,
        ]);
    }

    public function update(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $group = (string) $this->request->input('group', 'general');
        $data = $this->request->input('settings', []);

        $settings = $this->getSettingsService();

        foreach ($data as $key => $value) {
            $settings->set("{$group}.{$key}", $value);
        }

        $this->flash('success', 'Settings updated.');

        return $this->redirect('/admin/settings');
    }

    private function getSettings(): array
    {
        $settings = $this->getSettingsService();
        return $settings->all();
    }

    private function getSettingsService(): \Tavp\Cms\Settings\Settings
    {
        return app()->getService(\Tavp\Cms\Settings\Settings::class);
    }
}
