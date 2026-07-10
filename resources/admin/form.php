<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $record @var string $action @var string $heading */ ?>
<div class="mb-8">
  <a href="/admin/c/<?= $this->e($type->name) ?>" class="text-sm text-[#8f9097] hover:underline">&larr; Back to <?= $this->e($type->label) ?></a>
  <h1 class="text-2xl font-bold mt-2"><?= $this->e($heading) ?></h1>
</div>

<form method="post" action="<?= $this->e($action) ?>" class="max-w-2xl space-y-6">
  <?php foreach ($type->formSchema() as $field): ?>
    <?php $name = $field['name']; $value = $record[$name] ?? $field['default'] ?? ''; $control = $field['control']; ?>
    <div>
      <label class="block text-sm mb-1"><?= $this->e($field['label']) ?><?= !empty($field['required']) ? ' <span class="text-[#ffb4ab]">*</span>' : '' ?></label>
      <?php if ($control === 'textarea' || $control === 'editor' || $control === 'block-editor'): ?>
        <textarea name="<?= $this->e($name) ?>" rows="<?= $control === 'textarea' ? 3 : 10 ?>" class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none font-mono text-sm"><?= $this->e(is_scalar($value) ? $value : '') ?></textarea>
      <?php elseif ($control === 'select'): ?>
        <select name="<?= $this->e($name) ?>" class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
          <?php foreach (($field['options'] ?? []) as $opt): ?>
            <option value="<?= $this->e($opt) ?>"<?= ((string) $value === (string) $opt) ? ' selected' : '' ?>><?= $this->e(ucfirst($opt)) ?></option>
          <?php endforeach; ?>
        </select>
      <?php elseif ($control === 'toggle'): ?>
        <select name="<?= $this->e($name) ?>" class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
          <option value="1"<?= $value ? ' selected' : '' ?>>Yes</option>
          <option value="0"<?= !$value ? ' selected' : '' ?>>No</option>
        </select>
      <?php else: ?>
        <input name="<?= $this->e($name) ?>" type="<?= $control === 'datetime' ? 'datetime-local' : ($control === 'date' ? 'date' : ($control === 'number' ? 'number' : 'text')) ?>" value="<?= $this->e(is_scalar($value) ? $value : '') ?>" class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 focus:border-[#e6c446] outline-none">
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="flex gap-3 pt-2">
    <button class="rounded bg-[#e6c446] text-[#3b2f00] font-bold px-6 py-2 hover:opacity-90">Save</button>
    <a href="/admin/c/<?= $this->e($type->name) ?>" class="rounded border border-[#45474c] px-6 py-2 hover:bg-[#1a202c]">Cancel</a>
  </div>
</form>
