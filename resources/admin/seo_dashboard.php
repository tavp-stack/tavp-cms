<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">SEO Dashboard</h1>
    <div class="flex gap-2">
        <a href="<?= $adminPrefix ?>/seo/settings" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">Settings</a>
        <a href="<?= $adminPrefix ?>/seo/redirects" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">Redirects</a>
        <a href="<?= $adminPrefix ?>/seo/analyzer" class="px-4 py-2 bg-yellow-500 text-black rounded hover:bg-yellow-400">Analyzer</a>
        <form method="POST" action="<?= $adminPrefix ?>/seo/ping" class="inline">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">Ping Sitemap</button>
        </form>
    </div>
</div>

<?php if (!empty($stats)): ?>
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-gray-800 rounded-lg p-4">
        <div class="text-3xl font-bold text-white"><?= $stats['total_meta'] ?></div>
        <div class="text-gray-400 text-sm">SEO Meta Records</div>
    </div>
    <div class="bg-gray-800 rounded-lg p-4">
        <div class="text-3xl font-bold <?= $stats['avg_score'] >= 70 ? 'text-green-400' : ($stats['avg_score'] >= 40 ? 'text-yellow-400' : 'text-red-400') ?>"><?= $stats['avg_score'] ?>/100</div>
        <div class="text-gray-400 text-sm">Avg SEO Score</div>
    </div>
    <div class="bg-gray-800 rounded-lg p-4">
        <div class="text-3xl font-bold text-white"><?= $stats['redirects'] ?></div>
        <div class="text-gray-400 text-sm">Active Redirects</div>
    </div>
    <div class="bg-gray-800 rounded-lg p-4">
        <div class="text-3xl font-bold <?= $stats['broken_links'] > 0 ? 'text-red-400' : 'text-green-400' ?>"><?= $stats['broken_links'] ?></div>
        <div class="text-gray-400 text-sm">Broken Links</div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-2 gap-6">
    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-bold text-white mb-4">Sitemap</h2>
        <p class="text-gray-300 mb-2">XML sitemap auto-generated from published content.</p>
        <a href="/sitemap.xml" target="_blank" class="text-yellow-400 hover:underline">/sitemap.xml</a>
    </div>
    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-bold text-white mb-4">Robots.txt</h2>
        <p class="text-gray-300 mb-2">Search engine crawling rules.</p>
        <a href="/robots.txt" target="_blank" class="text-yellow-400 hover:underline">/robots.txt</a>
    </div>
</div>
