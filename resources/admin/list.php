<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $records */ ?>
<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl font-bold"><?= $this->e($type->label) ?></h1>
  <a href="/admin/c/<?= $this->e($type->name) ?>/create" class="rounded bg-[#e6c446] text-[#3b2f00] font-bold px-4 py-2 hover:opacity-90">New <?= $this->e($type->singular) ?></a>
</div>

<?php $columns = $type->browseColumns(); ?>
<div class="rounded-xl border border-[#45474c] overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-[#1a202c] text-[#8f9097] text-left">
      <tr>
        <?php foreach ($columns as $col): ?>
          <th class="px-4 py-3 font-medium"><?= $this->e(ucwords(str_replace('_', ' ', $col))) ?></th>
        <?php endforeach; ?>
        <th class="px-4 py-3 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($records)): ?>
        <tr><td colspan="<?= count($columns) + 1 ?>" class="px-4 py-8 text-center text-[#8f9097]">No <?= $this->e(strtolower($type->label)) ?> yet.</td></tr>
      <?php else: foreach ($records as $r): ?>
        <tr class="border-t border-[#45474c] hover:bg-[#1a202c]/50">
          <?php foreach ($columns as $col): ?>
            <td class="px-4 py-3"><?= $this->e(is_scalar($r[$col] ?? '') ? ($r[$col] ?? '') : '') ?></td>
          <?php endforeach; ?>
          <td class="px-4 py-3 text-right whitespace-nowrap">
            <a href="/admin/c/<?= $this->e($type->name) ?>/<?= $this->e($r['id'] ?? '') ?>/edit" class="text-[#e6c446] hover:underline">Edit</a>
            <form method="post" action="/admin/c/<?= $this->e($type->name) ?>/<?= $this->e($r['id'] ?? '') ?>/delete" class="inline ml-3" onsubmit="return confirm('Delete this item?')">
              <button class="text-[#ffb4ab] hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
