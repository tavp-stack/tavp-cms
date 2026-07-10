<?php /** @var string $termType @var array $term @var string $action @var string $heading */ ?>
<div class="mb-8">
  <a href="/admin/taxonomy/<?= $this->e($termType) ?>" class="text-sm text-[#8f9097] hover:underline">&larr; Back to <?= $this->e(ucfirst($termType)) ?>s</a>
  <h1 class="text-2xl font-bold mt-2"><?= $this->e($heading) ?></h1>
</div>

<?php if (!empty($__errors)): ?>
  <div class="mb-6 rounded border border-red-500 bg-red-900/20 p-4">
    <p class="text-sm font-bold text-red-400 mb-2">Please fix the following errors:</p>
    <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
      <?php foreach ($__errors as $field => $errs): ?>
        <?php foreach ($errs as $err): ?>
          <li><strong><?= $this->e(ucfirst($field)) ?>:</strong> <?= $this->e($err) ?></li>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="<?= $this->e($action) ?>" class="max-w-xl space-y-4">
  <div>
    <label class="block text-sm mb-1">Name <span class="text-[#ffb4ab]">*</span></label>
    <input name="name" value="<?= $this->e($__old['name'] ?? $term['name'] ?? '') ?>" class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
  </div>

  <div>
    <label class="block text-sm mb-1">Slug</label>
    <input name="slug" value="<?= $this->e($__old['slug'] ?? $term['slug'] ?? '') ?>" placeholder="Auto-generated from name" class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
  </div>

  <div>
    <label class="block text-sm mb-1">Description</label>
    <textarea name="description" rows="3" class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none"><?= $this->e($__old['description'] ?? $term['description'] ?? '') ?></textarea>
  </div>

  <div>
    <label class="block text-sm mb-1">Sort order</label>
    <input name="sort" type="number" value="<?= $this->e($__old['sort'] ?? $term['sort'] ?? 0) ?>" class="w-32 rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
  </div>

  <div>
    <label class="block text-sm mb-1">Parent ID</label>
    <input name="parent_id" type="number" value="<?= $this->e($__old['parent_id'] ?? $term['parent_id'] ?? 0) ?>" class="w-32 rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
  </div>

  <div class="flex gap-3 pt-2">
    <button class="rounded bg-[#e6c446] text-[#3b2f00] font-bold px-6 py-2 hover:opacity-90">Save</button>
    <a href="/admin/taxonomy/<?= $this->e($termType) ?>" class="rounded border border-[#45474c] px-6 py-2 hover:bg-[#1a202c]">Cancel</a>
  </div>
</form>
