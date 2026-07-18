<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">SEO Analyzer</h1>
        <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">Analyze on-page SEO for a content item</p>
    </div>
    <a href="<?= $adminPrefix ?>/seo" class="font-code-sm text-code-sm text-on-surface-variant hover:text-secondary transition-colors">← Back</a>
</div>

<div class="bg-surface-container border border-outline-variant p-6 mb-6">
    <form method="GET" action="<?= $adminPrefix ?>/seo/analyzer" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Content Type</label>
            <select name="type" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-code-sm">
                <option value="post" <?= $contentType === 'post' ? 'selected' : '' ?>>Post</option>
                <option value="page" <?= $contentType === 'page' ? 'selected' : '' ?>>Page</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Content ID</label>
            <input type="number" name="id" value="<?= $contentId ?: '' ?>" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-code-sm" required>
        </div>
        <button type="submit" class="px-4 py-2 bg-secondary text-on-secondary rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all font-label-caps text-label-caps">Analyze</button>
    </form>
</div>

<?php if ($analysis): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-surface-container border border-outline-variant performance-card p-6 text-center">
        <div class="font-headline-lg text-headline-lg <?= $analysis['score'] >= 70 ? 'text-secondary' : ($analysis['score'] >= 40 ? 'text-secondary-container' : 'text-error') ?>"><?= $analysis['score'] ?>/100</div>
        <div class="font-label-caps text-label-caps text-on-surface-variant mt-1">Overall Score</div>
    </div>
    <div class="bg-surface-container border border-outline-variant performance-card p-6 text-center">
        <div class="font-headline-lg text-headline-lg <?= count($analysis['errors']) === 0 ? 'text-secondary' : 'text-error' ?>"><?= count($analysis['errors']) ?></div>
        <div class="font-label-caps text-label-caps text-on-surface-variant mt-1">Errors</div>
    </div>
    <div class="bg-surface-container border border-outline-variant performance-card p-6 text-center">
        <div class="font-headline-lg text-headline-lg <?= count($analysis['warnings']) === 0 ? 'text-secondary' : 'text-secondary-container' ?>"><?= count($analysis['warnings']) ?></div>
        <div class="font-label-caps text-label-caps text-on-surface-variant mt-1">Warnings</div>
    </div>
</div>

<?php if (!empty($analysis['errors'])): ?>
<div class="bg-error-container/20 border border-error rounded-lg p-4 mb-4">
    <h3 class="font-label-caps text-label-caps text-error mb-2">Errors</h3>
    <ul class="space-y-1">
        <?php foreach ($analysis['errors'] as $e): ?>
        <li class="text-on-error text-sm font-body-md">• <?= $this->e($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($analysis['warnings'])): ?>
<div class="bg-secondary-container/20 border border-secondary-container rounded-lg p-4 mb-4">
    <h3 class="font-label-caps text-label-caps text-on-secondary-container mb-2">Warnings</h3>
    <ul class="space-y-1">
        <?php foreach ($analysis['warnings'] as $w): ?>
        <li class="text-on-secondary-container text-sm font-body-md">• <?= $this->e($w) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($analysis['suggestions'])): ?>
<div class="bg-primary-container/20 border border-primary-container rounded-lg p-4 mb-4">
    <h3 class="font-label-caps text-label-caps text-on-primary mb-2">Suggestions</h3>
    <ul class="space-y-1">
        <?php foreach ($analysis['suggestions'] as $s): ?>
        <li class="text-on-primary text-sm font-body-md">• <?= $this->e($s) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($analysis['preview'])): ?>
<div class="bg-surface-container border border-outline-variant p-6">
    <h3 class="font-headline-lg text-headline-lg text-on-surface mb-4">Search Preview</h3>
    <?= $analysis['preview'] ?>
</div>
<?php endif; ?>
<?php endif; ?>
