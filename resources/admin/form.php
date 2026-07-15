<?php /** @var array $content @var string $action @var string $heading */ ?>
<div class="flex items-center gap-4 mb-gutter">
  <a href="/admin/c/<?= $this->e($content['__type'] ?? 'post') ?>" class="text-on-surface-variant hover:text-secondary transition-colors">
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

<form method="post" action="<?= $this->e($action) ?>" class="max-w-2xl space-y-6">
  <?php foreach ($content['__fields'] ?? [] as $field): ?>
    <?php
      $key = $field['name'];
      // Skip author — it's auto-filled from logged-in user
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
        <div id="editor-wrap-<?= $this->e($key) ?>"></div>
        <textarea name="<?= $this->e($key) ?>" id="editor-<?= $this->e($key) ?>" class="hidden"><?= $this->e($value) ?></textarea>

      <?php elseif ($field['type'] === 'slug'): ?>
        <input type="text" name="<?= $this->e($key) ?>" value="<?= $this->e($value) ?>"
          class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface font-mono text-sm focus:border-secondary outline-none">

      <?php else: ?>
        <input type="text" name="<?= $this->e($key) ?>" value="<?= $this->e($value) ?>"
          class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none">
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="flex gap-3 pt-2">
    <button class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE</button>
    <a href="/admin/c/<?= $this->e($content['__type'] ?? 'post') ?>" class="border border-outline-variant font-label-caps text-label-caps py-3 px-8 hover:bg-surface-container-high transition-colors">CANCEL</a>
  </div>
</form>

<?php
$hasEditor = false;
foreach (($content['__fields'] ?? []) as $f) {
    if ($f['type'] === 'richtext' || $f['type'] === 'editor') { $hasEditor = true; break; }
}
if ($hasEditor):
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  <?php foreach (($content['__fields'] ?? []) as $field): ?>
    <?php if ($field['type'] === 'richtext' || $field['type'] === 'editor'): ?>
    (function() {
      const wrap = document.getElementById('editor-wrap-<?= $this->e($field['name']) ?>');
      const ta = document.getElementById('editor-<?= $this->e($field['name']) ?>');
      if (!wrap || !ta || ta.dataset.tuiReady) return;
      ta.dataset.tuiReady = '1';

      // Check if Toast UI Editor is loaded
      if (typeof toastui === 'undefined' || !toastui.Editor) {
        console.error('Toast UI Editor not loaded');
        wrap.innerHTML = '<p class="text-red-500">Error: Toast UI Editor failed to load. Check console for details.</p>';
        return;
      }

      try {
        new toastui.Editor({
          el: wrap,
          height: '500px',
          initialEditType: 'wysiwyg',
          initialValue: ta.value || '',
          events: {
            change: function() { ta.value = this.getMarkdown(); }
          },
          hooks: {
            addImageBlobHook: function(blob, callback) {
              const formData = new FormData();
              formData.append('file', blob);
              fetch('/admin/media/api/upload', {
                method: 'POST',
                body: formData
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  callback(data.url, data.name);
                } else {
                  alert('Upload failed: ' + (data.message || 'Unknown error'));
                }
              })
              .catch(err => {
                alert('Upload error: ' + err.message);
              });
              return false;
            }
          }
        });
        console.log('Toast UI Editor initialized for <?= $this->e($field['name']) ?>');
      } catch (err) {
        console.error('Toast UI Editor initialization error:', err);
        wrap.innerHTML = '<p class="text-red-500">Error initializing editor: ' + err.message + '</p>';
      }
    })();
    <?php endif; ?>
  <?php endforeach; ?>
});
</script>
<?php endif; ?>
