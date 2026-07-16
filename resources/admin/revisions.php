<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $record @var array $revisions */ ?>
<div class="mb-8">
  <a href="<?= $adminPrefix ?>/c/<?= $this->e($type->name) ?>/<?= $this->e($record['id']) ?>/edit" class="text-sm text-[#8f9097] hover:underline">&larr; Back to editor</a>
  <h1 class="text-2xl font-bold mt-2">Revisions: <?= $this->e($record['title'] ?? $record['slug'] ?? 'Record') ?></h1>
</div>

<?php if (empty($revisions)): ?>
  <p class="text-[#8f9097]">No revisions recorded yet.</p>
<?php else: ?>
  <div class="space-y-4">
    <?php foreach ($revisions as $i => $rev): ?>
      <div class="rounded border border-[#45474c] bg-[#1a202c] p-4 flex items-start justify-between">
        <div>
          <p class="text-sm font-mono text-[#8f9097]">
            <?= $this->e($rev['created_at'] ?? 'unknown') ?>
            <?php if (!empty($rev['author'])): ?>
              <span class="text-[#e6c446]">&middot;</span> <?= $this->e($rev['author']) ?>
            <?php endif; ?>
            <?php if (!empty($rev['note'])): ?>
              <span class="text-[#8f9097]">&middot;</span> <?= $this->e($rev['note']) ?>
            <?php endif; ?>
          </p>
          <?php if ($i > 0): ?>
            <?php $ts = date('Y-m-d_H-i-s', strtotime($rev['created_at'])); ?>
            <form method="post" action="<?= $adminPrefix ?>/c/<?= $this->e($type->name) ?>/<?= $this->e($record['id']) ?>/rollback/<?= $this->e($ts) ?>" class="mt-2" onsubmit="return confirm('Restore this revision? Current changes will be overwritten.')">
              <button class="text-xs rounded bg-[#e6c446] text-[#3b2f00] px-3 py-1 font-bold hover:opacity-90">Restore this version</button>
            </form>
          <?php endif; ?>
        </div>
        <div class="text-xs text-[#8f9097] max-w-xs overflow-hidden">
          <?php
            $data = $rev['data'] ?? [];
            $preview = mb_substr((string) ($data['body'] ?? $data['excerpt'] ?? ''), 0, 120);
          ?>
          <?= $this->e($preview) ?>…
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
