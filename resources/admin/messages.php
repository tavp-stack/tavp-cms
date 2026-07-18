<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">Messages</h1>
        <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">Contact form inbox</p>
    </div>
    <div class="flex gap-2">
        <a href="<?= $adminPrefix ?>/messages?filter=all" class="px-3 py-2 rounded border font-label-caps text-label-caps <?= $filter === 'all' ? 'bg-secondary text-on-secondary border-secondary' : 'bg-surface-container-high text-on-surface-variant border-outline-variant hover:border-secondary transition-colors' ?>">All (<?= $counts['all'] ?>)</a>
        <a href="<?= $adminPrefix ?>/messages?filter=unread" class="px-3 py-2 rounded border font-label-caps text-label-caps <?= $filter === 'unread' ? 'bg-secondary text-on-secondary border-secondary' : 'bg-surface-container-high text-on-surface-variant border-outline-variant hover:border-secondary transition-colors' ?>">Unread (<?= $counts['unread'] ?>)</a>
        <a href="<?= $adminPrefix ?>/messages?filter=read" class="px-3 py-2 rounded border font-label-caps text-label-caps <?= $filter === 'read' ? 'bg-secondary text-on-secondary border-secondary' : 'bg-surface-container-high text-on-surface-variant border-outline-variant hover:border-secondary transition-colors' ?>">Read (<?= $counts['read'] ?>)</a>
        <a href="<?= $adminPrefix ?>/messages?filter=archived" class="px-3 py-2 rounded border font-label-caps text-label-caps <?= $filter === 'archived' ? 'bg-secondary text-on-secondary border-secondary' : 'bg-surface-container-high text-on-surface-variant border-outline-variant hover:border-secondary transition-colors' ?>">Archived (<?= $counts['archived'] ?>)</a>
    </div>
</div>

<?php if (!empty($_SESSION['cms_flash']['success'])): ?>
    <div class="mb-4 rounded border border-secondary bg-secondary/10 px-3 py-2 text-sm text-secondary"><?= $this->e($_SESSION['cms_flash']['success']) ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: message list -->
    <div class="lg:col-span-1 bg-surface-container border border-outline-variant rounded overflow-hidden max-h-[70vh] overflow-y-auto">
        <?php if (empty($messages)): ?>
            <p class="text-on-surface-variant px-4 py-6 text-center font-body-md">No messages.</p>
        <?php else: ?>
            <?php foreach ($messages as $m): ?>
                <a href="<?= $adminPrefix ?>/messages?id=<?= $m['id'] ?>&filter=<?= $filter ?>" class="block px-4 py-3 border-b border-outline-variant <?= $selected !== null && (int) $selected['id'] === (int) $m['id'] ? 'bg-primary-container/30' : 'hover:bg-surface-container-high transition-colors' ?>">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-body-md text-on-surface truncate"><?= $this->e($m['name']) ?></span>
                        <?php if ($m['status'] === 'unread'): ?><span class="w-2 h-2 rounded-full bg-secondary flex-shrink-0"></span><?php endif; ?>
                    </div>
                    <p class="font-code-sm text-code-sm text-on-surface-variant truncate"><?= $this->e($m['subject'] ?: '(no subject)') ?></p>
                    <p class="font-code-sm text-code-sm text-on-surface-variant/60 truncate"><?= $this->e(date('Y-m-d H:i', strtotime($m['created_at']))) ?></p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Right: message preview -->
    <div class="lg:col-span-2 bg-surface-container border border-outline-variant rounded p-6">
        <?php if ($selected === null): ?>
            <p class="text-on-surface-variant font-body-md">Select a message to view.</p>
        <?php else: ?>
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface"><?= $this->e($selected['subject'] ?: '(no subject)') ?></h2>
                    <p class="font-code-sm text-code-sm text-on-surface-variant">From: <?= $this->e($selected['name']) ?> &lt;<?= $this->e($selected['email']) ?>&gt;</p>
                    <p class="font-code-sm text-code-sm text-on-surface-variant"><?= $this->e(date('Y-m-d H:i', strtotime($selected['created_at']))) ?></p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <?php if ($selected['status'] !== 'read'): ?>
                    <form method="POST" action="<?= $adminPrefix ?>/messages/read" class="inline">
                        <input type="hidden" name="id" value="<?= $selected['id'] ?>">
                        <button type="submit" class="px-3 py-2 bg-surface-container-high text-on-surface rounded border border-outline-variant hover:border-secondary transition-colors font-label-caps text-label-caps">Mark Read</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="<?= $adminPrefix ?>/messages/archive" class="inline">
                        <input type="hidden" name="id" value="<?= $selected['id'] ?>">
                        <button type="submit" class="px-3 py-2 bg-surface-container-high text-on-surface rounded border border-outline-variant hover:border-secondary transition-colors font-label-caps text-label-caps">Archive</button>
                    </form>
                    <form method="POST" action="<?= $adminPrefix ?>/messages/delete" class="inline" onsubmit="return confirm('Delete this message?')">
                        <input type="hidden" name="id" value="<?= $selected['id'] ?>">
                        <button type="submit" class="px-3 py-2 bg-error-container text-on-error rounded border border-error hover:brightness-110 transition-colors font-label-caps text-label-caps">Delete</button>
                    </form>
                </div>
            </div>
            <div class="border-t border-outline-variant pt-4">
                <p class="font-body-md text-on-surface whitespace-pre-wrap"><?= $this->e($selected['body']) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
