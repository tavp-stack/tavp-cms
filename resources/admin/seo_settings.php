<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">SEO Settings</h1>
        <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">Global meta, social cards, and verification</p>
    </div>
    <a href="<?= $adminPrefix ?>/seo" class="font-code-sm text-code-sm text-on-surface-variant hover:text-secondary transition-colors">← Back</a>
</div>

<?php if (!empty($_SESSION['cms_flash']['success'])): ?>
    <div class="mb-4 rounded border border-secondary bg-secondary/10 px-3 py-2 text-sm text-secondary"><?= $this->e($_SESSION['cms_flash']['success']) ?></div>
<?php endif; ?>

<form method="POST" action="<?= $adminPrefix ?>/seo/settings" class="space-y-6">
    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Meta Tags</h2>
        <div class="space-y-4">
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Title Suffix</label>
                <input type="text" name="seo_title_suffix" value="<?= $this->e($config['meta']['title_suffix'] ?? '') ?>" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-body-md">
                <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">Appended to every page title (e.g. " | My Site")</p>
            </div>
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Default Description</label>
                <textarea name="seo_default_description" rows="2" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-body-md"><?= $this->e($config['meta']['default_description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Open Graph</h2>
        <div class="space-y-4">
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Default OG Image URL</label>
                <input type="url" name="seo_og_image" value="<?= $this->e($config['open_graph']['default_image'] ?? '') ?>" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-body-md">
            </div>
        </div>
    </div>

    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Twitter</h2>
        <div class="space-y-4">
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Twitter Handle</label>
                <input type="text" name="seo_twitter_handle" value="<?= $this->e($config['twitter']['site_handle'] ?? '') ?>" placeholder="@yoursite" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-body-md">
            </div>
        </div>
    </div>

    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Webmaster Verification</h2>
        <div class="space-y-4">
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Google Verification Code</label>
                <input type="text" name="seo_google_verification" value="<?= $this->e($config['webmaster']['google_verification'] ?? '') ?>" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-body-md">
            </div>
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Bing Verification Code</label>
                <input type="text" name="seo_bing_verification" value="<?= $this->e($config['webmaster']['bing_verification'] ?? '') ?>" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-body-md">
            </div>
        </div>
    </div>

    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Analytics</h2>
        <div class="space-y-4">
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Google Analytics ID</label>
                <input type="text" name="seo_google_analytics_id" value="<?= $this->e($config['analytics']['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-code-sm">
            </div>
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Google Tag Manager ID</label>
                <input type="text" name="seo_google_tag_manager" value="<?= $this->e($config['analytics']['google_tag_manager_id'] ?? '') ?>" placeholder="GTM-XXXXXXX" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-code-sm">
            </div>
        </div>
    </div>

    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Schema.org</h2>
        <div class="space-y-4">
            <div>
                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Organization Name</label>
                <input type="text" name="seo_schema_org" value="<?= $this->e($config['schemas']['organization']['name'] ?? '') ?>" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-body-md">
            </div>
        </div>
    </div>

    <button type="submit" class="px-6 py-3 bg-secondary text-on-secondary rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all font-label-caps text-label-caps">Save Settings</button>
</form>
