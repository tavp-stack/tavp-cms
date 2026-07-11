<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $records */ ?>
<header class="flex justify-between items-end mb-8">
  <div>
    <h2 class="text-2xl font-bold text-on-surface mb-2"><?= $this->e($type->label) ?></h2>
    <p class="text-sm text-on-surface-variant">Manage <?= $this->e(strtolower($type->label)) ?>.</p>
  </div>
  <a href="/admin/c/<?= $this->e($type->name) ?>/create" class="bg-primary-container text-on-primary font-bold px-6 py-3 rounded hover:bg-primary transition-all duration-200 active:scale-95 flex items-center gap-2 kinetic-shadow">
    <span class="material-symbols-outlined text-[20px]">add</span>
    <span>New <?= $this->e($type->singular) ?></span>
  </a>
</header>

<?php $columns = $type->browseColumns(); ?>
<div class="rounded border border-outline-variant/20 overflow-hidden kinetic-shadow">
  <table class="w-full text-sm">
    <thead class="bg-surface-container-lowest">
      <tr>
        <?php foreach ($columns as $col): ?>
          <th class="px-4 py-3 text-left font-bold"><?= $this->e(ucwords(str_replace('_', ' ', $col))) ?></th>
        <?php endforeach; ?>
        <th class="px-4 py-3 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($records)): ?>
        <tr><td colspan="<?= count($columns) + 1 ?>" class="px-4 py-8 text-center text-on-surface-variant">No <?= $this->e(strtolower($type->label)) ?> yet.</td></tr>
      <?php else: foreach ($records as $r): ?>
        <tr class="border-t border-outline-variant/20 hover:bg-surface-container-highest transition-colors">
          <?php foreach ($columns as $col): ?>
            <td class="px-4 py-3"><?= $this->e(is_scalar($r[$col] ?? '') ? ($r[$col] ?? '') : '') ?></td>
          <?php endforeach; ?>
          <td class="px-4 py-3 text-right whitespace-nowrap">
            <a href="/admin/c/<?= $this->e($type->name) ?>/<?= $this->e($r['id'] ?? '') ?>/edit" class="text-primary-container hover:underline">Edit</a>
            <form method="post" action="/admin/c/<?= $this->e($type->name) ?>/<?= $this->e($r['id'] ?? '') ?>/delete" class="inline ml-3" onsubmit="return confirm('Delete this item?')">
              <button class="text-error hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
