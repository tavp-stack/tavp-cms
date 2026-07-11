<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $record @var string $action @var string $heading */ ?>
<header class="mb-8">
  <a href="/admin/c/<?= $this->e($type->name) ?>" class="text-sm text-on-surface-variant hover:underline">&larr; Back to <?= $this->e($type->label) ?></a>
  <h2 class="text-2xl font-bold text-on-surface mt-2"><?= $this->e($heading) ?></h2>
</header>

<?php if (!empty($__errors)): ?>
  <div class="mb-6 rounded border border-error bg-error/10 p-4">
    <p class="text-sm font-bold text-error mb-2">Please fix the following errors:</p>
    <ul class="list-disc list-inside text-sm text-error space-y-1">
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
      $value = $__old[$name] ?? $record[$name] ?? $field['default'] ?? '';
      $control = $field['control'];
      $fieldErrors = $__errors[$name] ?? [];
    ?>
    <div>
      <label class="block text-sm mb-1"><?= $this->e($field['label']) ?><?= !empty($field['required']) ? ' <span class="text-error">*</span>' : '' ?></label>
      <?php if ($control === 'textarea' || $control === 'editor' || $control === 'block-editor'): ?>
        <textarea name="<?= $this->e($name) ?>" rows="<?= $control === 'textarea' ? 3 : 10 ?>" class="w-full rounded bg-surface-container border <?= $fieldErrors ? 'border-error' : 'border-outline-variant/30' ?> px-3 py-2 focus:border-primary-container outline-none transition-colors font-mono text-sm"><?= $this->e(is_scalar($value) ? $value : '') ?></textarea>
      <?php elseif ($control === 'select'): ?>
        <select name="<?= $this->e($name) ?>" class="w-full rounded bg-surface-container border <?= $fieldErrors ? 'border-error' : 'border-outline-variant/30' ?> px-3 py-2 focus:border-primary-container outline-none transition-colors">
          <?php foreach (($field['options'] ?? []) as $opt): ?>
            <option value="<?= $this->e($opt) ?>"<?= ((string) $value === (string) $opt) ? ' selected' : '' ?>><?= $this->e(ucfirst($opt)) ?></option>
          <?php endforeach; ?>
        </select>
      <?php elseif ($control === 'toggle'): ?>
        <select name="<?= $this->e($name) ?>" class="w-full rounded bg-surface-container border <?= $fieldErrors ? 'border-error' : 'border-outline-variant/30' ?> px-3 py-2 focus:border-primary-container outline-none transition-colors">
          <option value="1"<?= $value ? ' selected' : '' ?>>Yes</option>
          <option value="0"<?= !$value ? ' selected' : '' ?>>No</option>
        </select>
      <?php else: ?>
        <input name="<?= $this->e($name) ?>" type="<?= $control === 'datetime' ? 'datetime-local' : ($control === 'date' ? 'date' : ($control === 'number' ? 'number' : 'text')) ?>" value="<?= $this->e(is_scalar($value) ? $value : '') ?>" class="w-full rounded bg-surface-container border <?= $fieldErrors ? 'border-error' : 'border-outline-variant/30' ?> px-3 py-2 focus:border-primary-container outline-none transition-colors">
      <?php endif; ?>
      <?php if (!empty($fieldErrors)): ?>
        <p class="text-xs text-error mt-1"><?= $this->e(implode(' ', $fieldErrors)) ?></p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="flex gap-3 pt-2">
    <button class="bg-primary-container text-on-primary font-bold px-6 py-3 rounded hover:bg-primary transition-all duration-200 active:scale-95 kinetic-shadow">Save</button>
    <a href="/admin/c/<?= $this->e($type->name) ?>" class="border border-outline-variant/30 px-6 py-3 rounded hover:bg-surface-container-highest transition-colors">Cancel</a>
  </div>
</form>
