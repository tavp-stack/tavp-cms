<?php
/** @var array $schema @var array $values */
$flash = $_SESSION['cms_flash']['success'] ?? null;
unset($_SESSION['cms_flash']);
?>
<div class="flex justify-between items-center mb-gutter">
  <div>
    <h2 class="font-headline-xl text-headline-xl">Settings</h2>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Manage your site's information and preferences</p>
  </div>
</div>

<?php if ($flash): ?>
  <div class="mb-6 bg-secondary-container/20 border border-secondary/30 p-4 rounded max-w-3xl flex items-center gap-2">
    <span class="material-symbols-outlined text-secondary">check_circle</span>
    <span class="text-body-md"><?= $this->e($flash) ?></span>
  </div>
<?php endif; ?>

<form method="post" action="<?= $adminPrefix ?>/settings" class="space-y-6 max-w-3xl">
  <?php foreach ($schema as $group => $section): ?>
    <div class="bg-surface-container border border-outline-variant p-6">
      <h3 class="font-headline-lg text-headline-lg mb-6 flex items-center gap-2">
        <span class="material-symbols-outlined text-secondary"><?= $this->e($section['icon']) ?></span>
        <?= $this->e($section['label']) ?>
      </h3>

      <div class="space-y-4">
        <?php foreach ($section['fields'] as $field): ?>
          <?php
            $key = $field['key'];
            $name = "settings[{$group}][{$key}]";
            $value = (string) ($values["{$group}.{$key}"] ?? '');
            $type = $field['type'] ?? 'text';
          ?>
          <div>
            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2"><?= $this->e($field['label']) ?></label>
            <?php if ($type === 'textarea'): ?>
              <textarea name="<?= $this->e($name) ?>" rows="3"
                placeholder="<?= $this->e($field['placeholder'] ?? '') ?>"
                class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors font-body-md"><?= $this->e($value) ?></textarea>
            <?php else: ?>
              <input type="text" name="<?= $this->e($name) ?>" value="<?= $this->e($value) ?>"
                placeholder="<?= $this->e($field['placeholder'] ?? '') ?>"
                class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors font-body-md">
            <?php endif; ?>
            <?php if (!empty($field['help'])): ?>
              <p class="text-xs text-on-surface-variant mt-1"><?= $this->e($field['help']) ?></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <div class="flex gap-3 sticky bottom-4">
    <button type="submit" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE SETTINGS</button>
  </div>
</form>
