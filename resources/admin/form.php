<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $record @var string $action @var string $heading */ ?>
<div class="flex items-center gap-4 mb-gutter">
  <a href="/admin/c/<?= $this->e($type->name) ?>" class="text-on-surface-variant hover:text-secondary transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <h2 class="font-headline-xl text-headline-xl"><?= $this->e($heading) ?></h2>
</div>

<?php if (!empty($__errors)): ?>
  <div class="mb-6 bg-error-container/20 border border-error/30 p-4 rounded">
    <p class="font-label-caps text-label-caps text-error mb-2">ERRORS</p>
    <ul class="list-disc list-inside text-body-md text-error space-y-1">
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
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2"><?= $this->e($field['label']) ?><?= !empty($field['required']) ? ' <span class="text-error">*</span>' : '' ?></label>
      <?php if ($control === 'textarea' || $control === 'editor' || $control === 'block-editor'): ?>
        <?php $isRich = ($control === 'editor' || $control === 'block-editor'); ?>
        <textarea name="<?= $this->e($name) ?>"<?= $isRich ? ' data-md-editor="1"' : '' ?> rows="<?= $control === 'textarea' ? 3 : 10 ?>" class="w-full bg-surface-container border <?= $fieldErrors ? 'border-error' : 'border-outline-variant' ?> rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm<?= $isRich ? ' hidden' : '' ?>"><?= $this->e(is_scalar($value) ? $value : '') ?></textarea>
      <?php elseif ($control === 'select'): ?>
        <select name="<?= $this->e($name) ?>" class="w-full bg-surface-container border <?= $fieldErrors ? 'border-error' : 'border-outline-variant' ?> rounded px-4 py-3 focus:border-secondary outline-none font-body-md">
          <?php foreach (($field['options'] ?? []) as $opt): ?>
            <option value="<?= $this->e($opt) ?>"<?= ((string) $value === (string) $opt) ? ' selected' : '' ?>><?= $this->e(ucfirst($opt)) ?></option>
          <?php endforeach; ?>
        </select>
      <?php else: ?>
        <input name="<?= $this->e($name) ?>" type="<?= $control === 'datetime' ? 'datetime-local' : ($control === 'date' ? 'date' : ($control === 'number' ? 'number' : 'text')) ?>" value="<?= $this->e(is_scalar($value) ? $value : '') ?>" class="w-full bg-surface-container border <?= $fieldErrors ? 'border-error' : 'border-outline-variant' ?> rounded px-4 py-3 focus:border-secondary outline-none font-body-md">
      <?php endif; ?>
      <?php if (!empty($fieldErrors)): ?>
        <p class="text-xs text-error mt-1"><?= $this->e(implode(' ', $fieldErrors)) ?></p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="flex gap-4 pt-4">
    <button class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE</button>
    <a href="/admin/c/<?= $this->e($type->name) ?>" class="border border-outline-variant font-label-caps text-label-caps py-3 px-8 hover:bg-surface-container-high transition-colors">CANCEL</a>
  </div>
</form>

<?php $__richEditors = array_filter($type->formSchema(), fn ($f) => in_array($f['control'], ['editor', 'block-editor'], true)); ?>
<?php if (!empty($__richEditors)): ?>
  <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
  <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/theme/toastui-editor-dark.min.css">
  <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('textarea[data-md-editor]').forEach(function (textarea) {
        var holder = document.createElement('div');
        textarea.parentNode.insertBefore(holder, textarea.nextSibling);

        var editor = new toastui.Editor({
          el: holder,
          height: '480px',
          theme: 'dark',
          initialEditType: 'wysiwyg',
          previewStyle: 'tab',
          usageStatistics: false,
          initialValue: textarea.value,
          toolbarItems: [
            ['heading', 'bold', 'italic', 'strike'],
            ['hr', 'quote'],
            ['ul', 'ol', 'task'],
            ['table', 'link'],
            ['code', 'codeblock'],
            ['scrollSync'],
          ],
        });

        var form = textarea.closest('form');
        if (form) {
          form.addEventListener('submit', function () {
            textarea.value = editor.getMarkdown();
          });
        }
      });
    });
  </script>
<?php endif; ?>
