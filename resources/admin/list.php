<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $records */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl"><?= $this->e($type->label) ?></h2>
  <a href="/admin/c/<?= $this->e($type->name) ?>/create" class="bg-secondary text-on-secondary py-3 px-6 rounded font-label-caps text-label-caps hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">
    + NEW <?= strtoupper($this->e($type->singular)) ?>
  </a>
</div>

<?php $columns = $type->browseColumns(); ?>
<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high border-b border-outline-variant">
      <tr>
        <?php foreach ($columns as $col): ?>
          <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant"><?= $this->e(ucwords(str_replace('_', ' ', $col))) ?></th>
        <?php endforeach; ?>
        <th class="px-4 py-3 text-right font-label-caps text-label-caps text-on-surface-variant">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($records)): ?>
        <tr><td colspan="<?= count($columns) + 1 ?>" class="px-4 py-8 text-center text-on-surface-variant font-body-md">No <?= $this->e(strtolower($type->label)) ?> yet.</td></tr>
      <?php else: foreach ($records as $r): ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <?php foreach ($columns as $col): ?>
            <td class="px-4 py-3 font-body-md"><?= $this->e(is_scalar($r[$col] ?? '') ? ($r[$col] ?? '') : '') ?></td>
          <?php endforeach; ?>
          <td class="px-4 py-3 text-right whitespace-nowrap">
            <a href="/admin/c/<?= $this->e($type->name) ?>/<?= $this->e($r['id'] ?? '') ?>/edit" class="text-secondary font-label-caps text-label-caps hover:underline mr-3">Edit</a>
            <form method="post" action="/admin/c/<?= $this->e($type->name) ?>/<?= $this->e($r['id'] ?? '') ?>/delete" class="inline" onsubmit="return confirm('Delete this item?')">
              <button class="text-error font-label-caps text-label-caps hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
