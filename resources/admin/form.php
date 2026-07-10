<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $record @var string $action @var string $heading */ ?>
<div class="mb-8">
  <a href="/admin/c/<?= $this->e($type->name) ?>" class="text-sm text-[#8f9097] hover:underline">&larr; Back to <?= $this->e($type->label) ?></a>
  <h1 class="text-2xl font-bold mt-2"><?= $this->e($heading) ?></h1>
  <?php if (!empty($type->name) && method_exists($this->bread()->revisions() ?? new \stdClass, 'history') && !empty($record['id'])): ?>
    <a href="/admin/c/<?= $this->e($type->name) ?>/<?= $this->e($record['id']) ?>/revisions" class="text-sm text-[#8f9097] hover:underline">View revisions</a>
  <?php endif; ?>
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

<form method="post" action="<?= $this->e($action) ?>" class="max-w-2xl space-y-6">
  <?php foreach ($type->formSchema() as $field): ?>
    <?php
      $name = $field['name'];
      // Prefer old input on validation error, then existing record, then default.
      $value = $__old[$name] ?? $record[$name] ?? $field['default'] ?? '';
      $control = $field['control'];
      $fieldErrors = $__errors[$name] ?? [];
    ?>
    <div>
      <label class="block text-sm mb-1"><?= $this->e($field['label']) ?><?= !empty($field['required']) ? ' <span class="text-[#ffb4ab]">*</span>' : '' ?></label>
      <?php if ($control === 'textarea' || $control === 'editor' || $control === 'block-editor'): ?>
        <textarea name="<?= $this->e($name) ?>" rows="<?= $control === 'textarea' ? 3 : 10 ?>" class="w-full rounded bg-[#1a202c] border <?= $fieldErrors ? 'border-red-500' : 'border-[#45474c]' ?> px-3 py-2 focus:border-[#e6c446] outline-none font-mono text-sm"><?= $this->e(is_scalar($value) ? $value : '') ?></textarea>
      <?php elseif ($control === 'select'): ?>
        <select name="<?= $this->e($name) ?>" class="w-full rounded bg-[#1a202c] border <?= $fieldErrors ? 'border-red-500' : 'border-[#45474c]' ?> px-3 py-2 focus:border-[#e6c446] outline-none">
          <?php foreach (($field['options'] ?? []) as $opt): ?>
            <option value="<?= $this->e($opt) ?>"<?= ((string) $value === (string) $opt) ? ' selected' : '' ?>><?= $this->e(ucfirst($opt)) ?></option>
          <?php endforeach; ?>
        </select>
      <?php elseif ($control === 'toggle'): ?>
        <select name="<?= $this->e($name) ?>" class="w-full rounded bg-[#1a202c] border <?= $fieldErrors ? 'border-red-500' : 'border-[#45474c]' ?> px-3 py-2 focus:border-[#e6c446] outline-none">
          <option value="1"<?= $value ? ' selected' : '' ?>>Yes</option>
          <option value="0"<?= !$value ? ' selected' : '' ?>>No</option>
        </select>
      <?php elseif ($control === 'color'): ?>
        <input name="<?= $this->e($name) ?>" type="color" value="<?= $this->e(is_scalar($value) ? $value : '#000000') ?>" class="w-16 h-10 rounded bg-[#1a202c] border <?= $fieldErrors ? 'border-red-500' : 'border-[#45474c]' ?> px-1 py-1">
      <?php elseif ($control === 'password'): ?>
        <input name="<?= $this->e($name) ?>" type="password" value="" placeholder="Leave empty to keep current" class="w-full rounded bg-[#1a202c] border <?= $fieldErrors ? 'border-red-500' : 'border-[#45474c]' ?> px-3 py-2 focus:border-[#e6c446] outline-none">
      <?php else: ?>
        <input name="<?= $this->e($name) ?>" type="<?= $control === 'datetime' ? 'datetime-local' : ($control === 'date' ? 'date' : ($control === 'number' ? 'number' : ($control === 'email' ? 'email' : ($control === 'url' ? 'url' : 'text')))) ?>" value="<?= $this->e(is_scalar($value) ? $value : '') ?>" class="w-full rounded bg-[#1a202c] border <?= $fieldErrors ? 'border-red-500' : 'border-[#45474c]' ?> px-3 py-2 focus:border-[#e6c446] outline-none">
      <?php endif; ?>
      <?php if (!empty($fieldErrors)): ?>
        <p class="text-xs text-red-400 mt-1"><?= $this->e(implode(' ', $fieldErrors)) ?></p>
      <?php endif; ?>
      <?php if (!empty($field['rules'])): ?>
        <p class="text-xs text-[#8f9097] mt-1">Rules: <?= $this->e(implode(', ', $field['rules'])) ?></p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="flex gap-3 pt-2">
    <button class="rounded bg-[#e6c446] text-[#3b2f00] font-bold px-6 py-2 hover:opacity-90">Save</button>
    <a href="/admin/c/<?= $this->e($type->name) ?>" class="rounded border border-[#45474c] px-6 py-2 hover:bg-[#1a202c]">Cancel</a>
  </div>
</form>
