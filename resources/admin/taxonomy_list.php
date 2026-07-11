<?php /** @var array $terms @var string $termType */ ?>
<?php $labels = ['category' => 'Kategori', 'tag' => 'Tag']; $label = $labels[$termType] ?? ucfirst($termType); ?>
<div class="mb-8 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-bold"><?= $this->e($label) ?></h1>
    <p class="text-sm text-on-surface-variant">Manage <?= $this->e(strtolower($label)) ?>.</p>
  </div>
  <a href="/admin/taxonomy/<?= $this->e($termType) ?>/create" class="bg-primary-container text-on-primary font-bold px-4 py-2 rounded hover:bg-primary transition-all duration-200 active:scale-95 flex items-center gap-2 kinetic-shadow">
    <span class="material-symbols-outlined text-[20px]">add</span>
    <span>New <?= $this->e($label) ?></span>
  </a>
</div>

<?php if (empty($terms)): ?>
  <p class="text-on-surface-variant">No <?= $this->e(strtolower($label)) ?> yet.</p>
<?php else: ?>
  <div class="rounded border border-outline-variant/20 overflow-hidden kinetic-shadow">
    <table class="w-full text-sm">
      <thead class="bg-surface-container-lowest">
        <tr>
          <th class="text-left px-4 py-3 font-bold">Name</th>
          <th class="text-left px-4 py-3 font-bold">Slug</th>
          <th class="text-left px-4 py-3 font-bold">Description</th>
          <th class="text-right px-4 py-3 font-bold">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($terms as $term): ?>
          <tr class="border-t border-outline-variant/20 hover:bg-surface-container-highest transition-colors">
            <td class="px-4 py-3"><?= $this->e($term['name']) ?></td>
            <td class="px-4 py-3 text-on-surface-variant font-mono text-xs"><?= $this->e($term['slug']) ?></td>
            <td class="px-4 py-3 text-on-surface-variant text-xs"><?= $this->e(mb_substr((string) ($term['description'] ?? ''), 0, 60)) ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="/admin/taxonomy/<?= $this->e($termType) ?>/<?= $this->e($term['id']) ?>/edit" class="text-xs text-primary-container hover:underline">Edit</a>
              <form method="post" action="/admin/taxonomy/<?= $this->e($termType) ?>/<?= $this->e($term['id']) ?>/delete" class="inline" onsubmit="return confirm('Delete this item?')">
                <button class="text-xs text-error hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
