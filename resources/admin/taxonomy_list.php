<?php /** @var array $terms @var string $termType */ ?>
<div class="mb-8 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-bold"><?= $this->e(ucfirst($termType)) ?>s</h1>
    <p class="text-sm text-[#8f9097]">Manage taxonomy terms.</p>
  </div>
  <a href="/admin/taxonomy/<?= $this->e($termType) ?>/create" class="rounded bg-[#e6c446] text-[#3b2f00] font-bold px-4 py-2 hover:opacity-90">+ New <?= $this->e(ucfirst($termType)) ?></a>
</div>

<?php if (empty($terms)): ?>
  <p class="text-[#8f9097]">No <?= $this->e($termType) ?>s yet.</p>
<?php else: ?>
  <div class="rounded border border-[#45474c] overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-[#1a202c]">
        <tr>
          <th class="text-left px-4 py-2 font-bold">Name</th>
          <th class="text-left px-4 py-2 font-bold">Slug</th>
          <th class="text-left px-4 py-2 font-bold">Description</th>
          <th class="text-right px-4 py-2 font-bold">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($terms as $term): ?>
          <tr class="border-t border-[#45474c] hover:bg-[#1a202c]">
            <td class="px-4 py-2"><?= $this->e($term['name']) ?></td>
            <td class="px-4 py-2 text-[#8f9097] font-mono text-xs"><?= $this->e($term['slug']) ?></td>
            <td class="px-4 py-2 text-[#8f9097] text-xs"><?= $this->e(mb_substr((string) ($term['description'] ?? ''), 0, 60)) ?></td>
            <td class="px-4 py-2 text-right space-x-2">
              <a href="/admin/taxonomy/<?= $this->e($termType) ?>/<?= $this->e($term['id']) ?>/edit" class="text-xs text-[#e6c446] hover:underline">Edit</a>
              <form method="post" action="/admin/taxonomy/<?= $this->e($termType) ?>/<?= $this->e($term['id']) ?>/delete" class="inline" onsubmit="return confirm('Delete this term?')">
                <button class="text-xs text-red-400 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
