<?php /** @var array $settings */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Settings</h2>
</div>

<form method="post" action="/admin/settings" class="space-y-6 max-w-2xl">
  <div class="bg-surface-container border border-outline-variant p-6">
    <h3 class="font-headline-lg text-headline-lg mb-4">Site Settings</h3>

    <?php if (empty($settings)): ?>
      <p class="text-on-surface-variant">No settings configured.</p>
    <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($settings as $setting): ?>
          <div>
            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2"><?= $this->e(ucwords(str_replace('_', ' ', $setting['key'] ?? ''))) ?></label>
            <?php if (($setting['type'] ?? 'text') === 'textarea'): ?>
              <textarea name="<?= $this->e($setting['key'] ?? '') ?>" rows="3"
                class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors font-body-md"><?= $this->e($setting['value'] ?? '') ?></textarea>
            <?php elseif (($setting['type'] ?? 'text') === 'select'): ?>
              <select name="<?= $this->e($setting['key'] ?? '') ?>"
                class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors">
                <option value="1"<?= ($setting['value'] ?? '') === '1' ? ' selected' : '' ?>>Active</option>
                <option value="0"<?= ($setting['value'] ?? '') === '0' ? ' selected' : '' ?>>Inactive</option>
              </select>
            <?php else: ?>
              <input type="text" name="<?= $this->e($setting['key'] ?? '') ?>" value="<?= $this->e($setting['value'] ?? '') ?>"
                class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors">
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="flex gap-3">
    <button type="submit" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE SETTINGS</button>
  </div>
</form>
