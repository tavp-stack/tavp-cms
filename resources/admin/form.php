<?php /** @var array $content @var string $action @var string $heading */ ?>
<div class="flex items-center gap-4 mb-gutter">
  <a href="<?= $adminPrefix ?>/c/<?= $this->e($content['__type'] ?? 'post') ?>" class="text-on-surface-variant hover:text-secondary transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <h2 class="font-headline-xl text-headline-xl"><?= $this->e($heading) ?></h2>
</div>

<?php if (!empty($__errors)): ?>
  <div class="mb-6 bg-error-container/20 border border-error/30 p-4 rounded max-w-2xl">
    <ul class="list-disc list-inside text-body-md text-error space-y-1">
      <?php foreach ($__errors as $errs): foreach ($errs as $err): ?>
        <li><?= $this->e($err) ?></li>
      <?php endforeach; endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form id="content-form" method="post" action="<?= $this->e($action) ?>" class="max-w-2xl space-y-6 pb-24">
  <?php foreach ($content['__fields'] ?? [] as $field): ?>
    <?php
      $key = $field['name'];
      if ($key === 'author') continue;
      $value = $__old[$key] ?? ($content[$key] ?? ($field['default'] ?? ''));
    ?>
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">
        <?= $this->e(ucwords(str_replace('_', ' ', $key))) ?>
        <?php if (!empty($field['required'])): ?><span class="text-error">*</span><?php endif; ?>
      </label>

      <?php if ($field['type'] === 'textarea'): ?>
        <textarea name="<?= $this->e($key) ?>" rows="3"
          class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors font-body-md"><?= $this->e($value) ?></textarea>

      <?php elseif ($field['type'] === 'select'): ?>
        <select name="<?= $this->e($key) ?>"
          class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors">
          <?php foreach ($field['options'] as $opt): ?>
            <option value="<?= $this->e($opt) ?>"<?= $value === $opt ? ' selected' : '' ?>><?= $this->e(ucfirst($opt)) ?></option>
          <?php endforeach; ?>
        </select>

      <?php elseif ($field['type'] === 'richtext' || $field['type'] === 'editor'): ?>
        <textarea name="<?= $this->e($key) ?>" id="editor-<?= $this->e($key) ?>" class="easymde-target"><?= $this->e($value) ?></textarea>

      <?php elseif ($field['type'] === 'slug'): ?>
        <input type="text" name="<?= $this->e($key) ?>" value="<?= $this->e($value) ?>"
          class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface font-mono text-sm focus:border-secondary outline-none">

      <?php else: ?>
        <input type="text" name="<?= $this->e($key) ?>" value="<?= $this->e($value) ?>"
          class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none">
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</form>

<!-- Floating save bar — fixed at bottom, highest z-index -->
<div class="fixed bottom-0 left-0 right-0 z-[9999] floating-save-bar border-t border-outline-variant" x-data x-bind:style="'margin-left:' + (sidebarCollapsed ? '68px' : '256px')">
  <div class="px-10 py-4 flex gap-3">
    <button type="submit" form="content-form" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE</button>
    <a href="<?= $adminPrefix ?>/c/<?= $this->e($content['__type'] ?? 'post') ?>" class="border border-outline-variant font-label-caps text-label-caps py-3 px-8 hover:bg-surface-container-high transition-colors">CANCEL</a>
  </div>
</div>

<?php
$hasEditor = false;
foreach (($content['__fields'] ?? []) as $f) {
    if ($f['type'] === 'richtext' || $f['type'] === 'editor') { $hasEditor = true; break; }
}
if ($hasEditor):
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('content-form');
  var editors = [];

  document.querySelectorAll('.easymde-target').forEach(function(ta) {
    if (ta.dataset.mdeReady) return;
    ta.dataset.mdeReady = '1';

    var mde = new EasyMDE({
      element: ta,
      spellChecker: false,
      autosave: { enabled: false },
      minHeight: '400px',
      status: false,
      toolbar: [
        'bold', 'italic', 'heading', '|',
        'quote', 'unordered-list', 'ordered-list', '|',
        'link', 'image', 'table', 'code', '|',
        'preview', 'side-by-side', 'fullscreen', '|',
        'guide'
      ]
    });

    // Ensure textarea is not disabled
    ta.removeAttribute('disabled');
    ta.removeAttribute('readonly');

    // Sync on every change
    mde.codemirror.on('change', function() {
      ta.value = mde.value();
    });

    editors.push({ mde: mde, ta: ta });
  });

  // Force sync ALL editors before form submit
  if (form) {
    form.addEventListener('submit', function(e) {
      editors.forEach(function(ed) {
        ed.ta.value = ed.mde.value();
        ed.ta.removeAttribute('disabled');
        ed.ta.removeAttribute('readonly');
      });
      console.log('Form submitting, body value length:', (form.querySelector('[name="body"]') || {}).value?.length);
    });
  }
});
</script>
<?php endif; ?>
