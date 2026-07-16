<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">SEO Settings</h1>
    <a href="<?= $adminPrefix ?>/seo" class="text-gray-400 hover:text-white">← Back</a>
</div>

<form method="POST" action="<?= $adminPrefix ?>/seo/settings" class="space-y-6">
    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-bold text-white mb-4">Meta Tags</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-1">Title Suffix</label>
                <input type="text" name="seo_title_suffix" value="<?= $this->e($config['meta']['title_suffix'] ?? '') ?>" class="w-full bg-gray-700 text-white rounded px-3 py-2">
                <p class="text-gray-500 text-xs mt-1">Appended to every page title (e.g. " | My Site")</p>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Default Description</label>
                <textarea name="seo_default_description" rows="2" class="w-full bg-gray-700 text-white rounded px-3 py-2"><?= $this->e($config['meta']['default_description'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Default OG Image URL</label>
                <input type="url" name="seo_og_image" value="<?= $this->e($config['open_graph']['default_image'] ?? '') ?>" class="w-full bg-gray-700 text-white rounded px-3 py-2">
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-bold text-white mb-4">Twitter</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-1">Twitter Handle</label>
                <input type="text" name="seo_twitter_handle" value="<?= $this->e($config['twitter']['site_handle'] ?? '') ?>" placeholder="@yoursite" class="w-full bg-gray-700 text-white rounded px-3 py-2">
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-bold text-white mb-4">Webmaster Verification</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-1">Google Verification Code</label>
                <input type="text" name="seo_google_verification" value="<?= $this->e($config['webmaster']['google_verification'] ?? '') ?>" class="w-full bg-gray-700 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Bing Verification Code</label>
                <input type="text" name="seo_bing_verification" value="<?= $this->e($config['webmaster']['bing_verification'] ?? '') ?>" class="w-full bg-gray-700 text-white rounded px-3 py-2">
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-bold text-white mb-4">Analytics</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-1">Google Analytics ID</label>
                <input type="text" name="seo_google_analytics_id" value="<?= $this->e($config['analytics']['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX" class="w-full bg-gray-700 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Google Tag Manager ID</label>
                <input type="text" name="seo_google_tag_manager" value="<?= $this->e($config['analytics']['google_tag_manager_id'] ?? '') ?>" placeholder="GTM-XXXXXXX" class="w-full bg-gray-700 text-white rounded px-3 py-2">
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-bold text-white mb-4">Schema.org</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-1">Organization Name</label>
                <input type="text" name="seo_schema_org" value="<?= $this->e($config['schemas']['organization']['name'] ?? '') ?>" class="w-full bg-gray-700 text-white rounded px-3 py-2">
            </div>
        </div>
    </div>

    <button type="submit" class="px-6 py-3 bg-yellow-500 text-black font-bold rounded hover:bg-yellow-400">Save Settings</button>
</form>
