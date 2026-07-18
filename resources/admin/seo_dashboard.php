<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">SEO Dashboard</h1>
        <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">Search engine optimization overview</p>
    </div>
    <div class="flex gap-2">
        <a href="<?= $adminPrefix ?>/seo/settings" class="px-4 py-2 bg-surface-container-high text-on-surface rounded border border-outline-variant hover:border-secondary transition-colors font-label-caps text-label-caps">Settings</a>
        <a href="<?= $adminPrefix ?>/seo/redirects" class="px-4 py-2 bg-surface-container-high text-on-surface rounded border border-outline-variant hover:border-secondary transition-colors font-label-caps text-label-caps">Redirects</a>
        <a href="<?= $adminPrefix ?>/seo/analyzer" class="px-4 py-2 bg-secondary text-on-secondary rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all font-label-caps text-label-caps">Analyzer</a>
        <form method="POST" action="<?= $adminPrefix ?>/seo/ping" class="inline">
            <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all font-label-caps text-label-caps">Ping Sitemap</button>
        </form>
    </div>
</div>

<?php if (!empty($stats)): ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface-container p-6 border border-outline-variant performance-card">
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">SEO META RECORDS</p>
        <div class="font-headline-lg text-headline-lg text-on-surface"><?= $stats['total_meta'] ?></div>
    </div>
    <div class="bg-surface-container p-6 border border-outline-variant performance-card">
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">AVG SEO SCORE</p>
        <div class="font-headline-lg text-headline-lg <?= $stats['avg_score'] >= 70 ? 'text-secondary' : ($stats['avg_score'] >= 40 ? 'text-secondary-container' : 'text-error') ?>"><?= $stats['avg_score'] ?>/100</div>
    </div>
    <div class="bg-surface-container p-6 border border-outline-variant performance-card">
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">ACTIVE REDIRECTS</p>
        <div class="font-headline-lg text-headline-lg text-on-surface"><?= $stats['redirects'] ?></div>
    </div>
    <div class="bg-surface-container p-6 border border-outline-variant performance-card">
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">BROKEN LINKS</p>
        <div class="font-headline-lg text-headline-lg <?= $stats['broken_links'] > 0 ? 'text-error' : 'text-secondary' ?>"><?= $stats['broken_links'] ?></div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Sitemap</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mb-2">XML sitemap auto-generated from published content.</p>
        <a href="/sitemap.xml" target="_blank" class="font-code-sm text-code-sm text-secondary hover:underline"><?= $adminPrefix ?>/sitemap.xml</a>
    </div>
    <div class="bg-surface-container border border-outline-variant p-6">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Robots.txt</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mb-2">Search engine crawling rules.</p>
        <a href="/robots.txt" target="_blank" class="font-code-sm text-code-sm text-secondary hover:underline">/robots.txt</a>
    </div>
</div>
