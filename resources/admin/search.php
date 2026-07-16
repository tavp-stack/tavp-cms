<?php /** @var string $query @var array $results */ ?>
<div class="mb-8">
  <h1 class="text-2xl font-bold">Search</h1>
</div>

<form method="get" action="<?= $adminPrefix ?>/search" class="mb-6">
  <div class="flex gap-2 max-w-xl">
    <input name="q" value="<?= $this->e($query) ?>" placeholder="Search all content…" class="flex-1 rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
    <button class="rounded bg-[#e6c446] text-[#3b2f00] font-bold px-4 py-2 hover:opacity-90">Search</button>
  </div>
</form>

<?php if ($query !== '' && empty($results)): ?>
  <p class="text-[#8f9097]">No results found for "<?= $this->e($query) ?>".</p>
<?php endif; ?>

<?php if (!empty($results)): ?>
  <p class="text-sm text-[#8f9097] mb-4"><?= count($results) ?> result(s) found.</p>
  <div class="space-y-3">
    <?php foreach ($results as $hit): ?>
      <?php $record = $hit['content']; $type = $hit['type']; ?>
      <a href="<?= $adminPrefix ?>/c/<?= $this->e($type) ?>/<?= $this->e($record['id']) ?>/edit" class="block rounded border border-[#45474c] bg-[#1a202c] p-4 hover:border-[#e6c446] transition-colors">
        <div class="flex items-center gap-2 mb-1">
          <span class="text-xs rounded bg-[#e6c446]/20 text-[#e6c446] px-2 py-0.5 font-bold"><?= $this->e(ucfirst($type)) ?></span>
          <span class="text-sm font-bold"><?= $this->e($record['title'] ?? $record['slug'] ?? 'Untitled') ?></span>
        </div>
        <?php if (!empty($record['excerpt'])): ?>
          <p class="text-xs text-[#8f9097] line-clamp-2"><?= $this->e(mb_substr($record['excerpt'], 0, 200)) ?></p>
        <?php endif; ?>
        <p class="text-xs text-[#8f9097] mt-1">Score: <?= $this->e($hit['score']) ?></p>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
