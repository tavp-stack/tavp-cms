<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">Redirects</h1>
        <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">Manage 301/302 URL redirects</p>
    </div>
    <a href="<?= $adminPrefix ?>/seo" class="font-code-sm text-code-sm text-on-surface-variant hover:text-secondary transition-colors">← Back</a>
</div>

<?php if (!empty($_SESSION['cms_flash']['success'])): ?>
    <div class="mb-4 rounded border border-secondary bg-secondary/10 px-3 py-2 text-sm text-secondary"><?= $this->e($_SESSION['cms_flash']['success']) ?></div>
<?php endif; ?>

<div class="bg-surface-container border border-outline-variant p-6 mb-6">
    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Add Redirect</h2>
    <form method="POST" action="<?= $adminPrefix ?>/seo/redirects" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">From URL</label>
            <input type="text" name="from_url" placeholder="/old-page" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-code-sm" required>
        </div>
        <div class="flex-1">
            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">To URL</label>
            <input type="text" name="to_url" placeholder="/new-page" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-code-sm" required>
        </div>
        <div class="w-40">
            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-1">Status</label>
            <select name="status_code" class="w-full bg-surface-container-lowest text-on-surface border border-outline-variant rounded px-3 py-2 focus:border-secondary outline-none transition-colors font-code-sm">
                <option value="301">301 (Permanent)</option>
                <option value="302">302 (Temporary)</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-secondary text-on-secondary rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all font-label-caps text-label-caps">Add</button>
    </form>
</div>

<div class="bg-surface-container border border-outline-variant overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-outline-variant">
                <th class="text-left font-label-caps text-label-caps text-on-surface-variant px-4 py-3">From</th>
                <th class="text-left font-label-caps text-label-caps text-on-surface-variant px-4 py-3">To</th>
                <th class="text-left font-label-caps text-label-caps text-on-surface-variant px-4 py-3">Status</th>
                <th class="text-left font-label-caps text-label-caps text-on-surface-variant px-4 py-3">Hits</th>
                <th class="text-left font-label-caps text-label-caps text-on-surface-variant px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($redirects)): ?>
            <tr><td colspan="5" class="text-on-surface-variant px-4 py-4 text-center font-body-md">No redirects found.</td></tr>
            <?php else: ?>
            <?php foreach ($redirects as $r): ?>
            <tr class="border-b border-outline-variant">
                <td class="text-on-surface px-4 py-3 font-code-sm"><?= $this->e($r['from_url']) ?></td>
                <td class="text-on-surface px-4 py-3 font-code-sm"><?= $this->e($r['to_url']) ?></td>
                <td class="px-4 py-3"><span class="px-2 py-1 font-label-caps text-label-caps rounded-full <?= $r['status_code'] == 301 ? 'bg-secondary-container text-on-secondary-container' : 'bg-primary-container text-on-primary' ?>"><?= $r['status_code'] ?></span></td>
                <td class="text-on-surface-variant px-4 py-3 font-code-sm"><?= $r['hits'] ?? 0 ?></td>
                <td class="px-4 py-3">
                    <form method="POST" action="<?= $adminPrefix ?>/seo/redirects/delete" class="inline" onsubmit="return confirm('Delete this redirect?')">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <button type="submit" class="text-error hover:brightness-110 font-label-caps text-label-caps">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
