<?php /** @var array $counts @var array $__types */ ?>
<h1 class="text-2xl font-bold mb-8">Dashboard</h1>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach ($__types as $name => $type): ?>
    <a href="/admin/c/<?= $this->e($name) ?>" class="block rounded-xl border border-[#45474c] bg-[#1a202c] p-6 hover:border-[#e6c446] transition-colors">
      <div class="text-3xl font-bold text-[#e6c446]"><?= (int) ($counts[$name] ?? 0) ?></div>
      <div class="mt-1 text-[#dde2f3]"><?= $this->e($type->label) ?></div>
      <div class="mt-4 text-sm text-[#8f9097]">Manage <?= $this->e(strtolower($type->label)) ?> &rarr;</div>
    </a>
  <?php endforeach; ?>
</div>
