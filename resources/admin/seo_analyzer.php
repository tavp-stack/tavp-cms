<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">SEO Analyzer</h1>
    <a href="<?= $adminPrefix ?>/seo" class="text-gray-400 hover:text-white">← Back</a>
</div>

<div class="bg-gray-800 rounded-lg p-6 mb-6">
    <form method="GET" action="<?= $adminPrefix ?>/seo/analyzer" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-gray-300 text-sm mb-1">Content Type</label>
            <select name="type" class="w-full bg-gray-700 text-white rounded px-3 py-2">
                <option value="post" <?= $contentType === 'post' ? 'selected' : '' ?>>Post</option>
                <option value="page" <?= $contentType === 'page' ? 'selected' : '' ?>>Page</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block text-gray-300 text-sm mb-1">Content ID</label>
            <input type="number" name="id" value="<?= $contentId ?: '' ?>" class="w-full bg-gray-700 text-white rounded px-3 py-2" required>
        </div>
        <button type="submit" class="px-4 py-2 bg-yellow-500 text-black rounded hover:bg-yellow-400">Analyze</button>
    </form>
</div>

<?php if ($analysis): ?>
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-gray-800 rounded-lg p-4 text-center">
        <div class="text-4xl font-bold <?= $analysis['score'] >= 70 ? 'text-green-400' : ($analysis['score'] >= 40 ? 'text-yellow-400' : 'text-red-400') ?>"><?= $analysis['score'] ?>/100</div>
        <div class="text-gray-400 text-sm">Overall Score</div>
    </div>
    <div class="bg-gray-800 rounded-lg p-4 text-center">
        <div class="text-4xl font-bold <?= count($analysis['errors']) === 0 ? 'text-green-400' : 'text-red-400' ?>"><?= count($analysis['errors']) ?></div>
        <div class="text-gray-400 text-sm">Errors</div>
    </div>
    <div class="bg-gray-800 rounded-lg p-4 text-center">
        <div class="text-4xl font-bold <?= count($analysis['warnings']) === 0 ? 'text-green-400' : 'text-yellow-400' ?>"><?= count($analysis['warnings']) ?></div>
        <div class="text-gray-400 text-sm">Warnings</div>
    </div>
</div>

<?php if (!empty($analysis['errors'])): ?>
<div class="bg-red-900/30 border border-red-700 rounded-lg p-4 mb-4">
    <h3 class="text-red-400 font-bold mb-2">Errors</h3>
    <ul class="space-y-1">
        <?php foreach ($analysis['errors'] as $e): ?>
        <li class="text-red-300 text-sm">• <?= $this->e($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($analysis['warnings'])): ?>
<div class="bg-yellow-900/30 border border-yellow-700 rounded-lg p-4 mb-4">
    <h3 class="text-yellow-400 font-bold mb-2">Warnings</h3>
    <ul class="space-y-1">
        <?php foreach ($analysis['warnings'] as $w): ?>
        <li class="text-yellow-300 text-sm">• <?= $this->e($w) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($analysis['suggestions'])): ?>
<div class="bg-blue-900/30 border border-blue-700 rounded-lg p-4 mb-4">
    <h3 class="text-blue-400 font-bold mb-2">Suggestions</h3>
    <ul class="space-y-1">
        <?php foreach ($analysis['suggestions'] as $s): ?>
        <li class="text-blue-300 text-sm">• <?= $this->e($s) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($analysis['preview'])): ?>
<div class="bg-gray-800 rounded-lg p-6">
    <h3 class="text-white font-bold mb-4">Search Preview</h3>
    <?= $analysis['preview'] ?>
</div>
<?php endif; ?>
<?php endif; ?>
