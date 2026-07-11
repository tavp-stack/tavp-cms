<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Settings admin — manage useful site-level settings grouped by section.
 */
class SettingsController extends AdminController
{
    /**
     * Curated set of site settings, grouped by section. Each field is stored
     * as "{group}.{key}" in the settings table.
     *
     * @return array<string,array{label:string,icon:string,fields:array<int,array{key:string,label:string,type:string,placeholder?:string,help?:string,options?:array<string,string>}>}>
     */
    private function schema(): array
    {
        return [
            'general' => [
                'label' => 'General',
                'icon' => 'tune',
                'fields' => [
                    ['key' => 'site_name', 'label' => 'Site Name', 'type' => 'text', 'placeholder' => 'TAVP', 'help' => 'Shown in the browser tab and page titles.'],
                    ['key' => 'tagline', 'label' => 'Tagline', 'type' => 'text', 'placeholder' => 'A short catchy phrase'],
                    ['key' => 'description', 'label' => 'Site Description', 'type' => 'textarea', 'help' => 'Used for SEO and social previews.'],
                    ['key' => 'timezone', 'label' => 'Timezone', 'type' => 'text', 'placeholder' => 'Asia/Jakarta'],
                ],
            ],
            'contact' => [
                'label' => 'Contact',
                'icon' => 'mail',
                'fields' => [
                    ['key' => 'email', 'label' => 'Contact Email', 'type' => 'text', 'placeholder' => 'hello@tavp.web.id'],
                    ['key' => 'phone', 'label' => 'Phone', 'type' => 'text', 'placeholder' => '+62 ...'],
                    ['key' => 'address', 'label' => 'Address', 'type' => 'textarea'],
                ],
            ],
            'social' => [
                'label' => 'Social Links',
                'icon' => 'share',
                'fields' => [
                    ['key' => 'twitter', 'label' => 'Twitter / X URL', 'type' => 'text', 'placeholder' => 'https://x.com/...'],
                    ['key' => 'github', 'label' => 'GitHub URL', 'type' => 'text', 'placeholder' => 'https://github.com/...'],
                    ['key' => 'linkedin', 'label' => 'LinkedIn URL', 'type' => 'text', 'placeholder' => 'https://linkedin.com/...'],
                    ['key' => 'instagram', 'label' => 'Instagram URL', 'type' => 'text', 'placeholder' => 'https://instagram.com/...'],
                ],
            ],
            'seo' => [
                'label' => 'SEO & Analytics',
                'icon' => 'search',
                'fields' => [
                    ['key' => 'meta_keywords', 'label' => 'Meta Keywords', 'type' => 'text', 'help' => 'Comma-separated keywords.'],
                    ['key' => 'google_analytics_id', 'label' => 'Google Analytics ID', 'type' => 'text', 'placeholder' => 'G-XXXXXXX'],
                ],
            ],
            'footer' => [
                'label' => 'Footer',
                'icon' => 'call_to_action',
                'fields' => [
                    ['key' => 'copyright', 'label' => 'Copyright Text', 'type' => 'text', 'placeholder' => '© 2026 TAVP. All rights reserved.'],
                    ['key' => 'footer_note', 'label' => 'Footer Note', 'type' => 'textarea'],
                ],
            ],
        ];
    }

    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $values = $this->getSettingsService()->all();

        return $this->admin('settings.list', [
            'schema' => $this->schema(),
            'values' => $values,
        ]);
    }

    public function update(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $settings = $this->getSettingsService();
        $posted = (array) $this->request->input('settings', []);

        foreach ($this->schema() as $group => $section) {
            $groupData = (array) ($posted[$group] ?? []);
            foreach ($section['fields'] as $field) {
                $key = $field['key'];
                $value = (string) ($groupData[$key] ?? '');
                $settings->set("{$group}.{$key}", $value);
            }
        }

        $settings->forget();
        $this->flash('success', 'Settings saved.');

        return $this->redirect('/admin/settings');
    }

    private function getSettingsService(): \Tavp\Cms\Settings\Settings
    {
        return app()->getService(\Tavp\Cms\Settings\Settings::class);
    }
}
