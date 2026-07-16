<?php /** @var array $types @var array $counts */ ?>

<div class="flex justify-between items-center mb-8">
  <div>
    <h1 class="font-headline-xl text-headline-xl text-secondary">BREAD Manager</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-2">Browse, Read, Edit, Add, Delete — manage your content types</p>
  </div>
</div>

<!-- Content Types Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach ($types as $name => $type): ?>
    <div class="bg-surface-container border border-outline-variant p-6 hover:border-secondary transition-colors">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary text-2xl">
            <?php
            $iconMap = ['home' => 'home', 'page' => 'description', 'post' => 'article'];
            echo $iconMap[$name] ?? 'description';
            ?>
          </span>
          <div>
            <h3 class="font-headline-lg text-headline-lg"><?= $this->e($type->label) ?></h3>
            <p class="font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($type->singular) ?></p>
          </div>
        </div>
        <span class="bg-secondary text-on-secondary px-3 py-1 rounded font-label-caps text-label-caps">
          <?= (int) ($counts[$name] ?? 0) ?> records
        </span>
      </div>

      <div class="space-y-3 mb-6">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-sm text-on-surface-variant">route</span>
          <code class="font-code-sm text-code-sm text-secondary"><?= $this->e($type->route) ?></code>
        </div>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-sm text-on-surface-variant">view_list</span>
          <span class="font-code-sm text-code-sm text-on-surface-variant"><?= count($type->fields) ?> fields</span>
        </div>
      </div>

      <!-- Fields Preview -->
      <div class="mb-6">
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-3">FIELDS</p>
        <div class="space-y-2">
          <?php foreach (array_slice($type->fields, 0, 5) as $field): ?>
            <div class="flex items-center justify-between p-2 bg-surface-container-low rounded">
              <span class="font-code-sm text-code-sm"><?= $this->e($field->name) ?></span>
              <span class="font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($field->type->value) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if (count($type->fields) > 5): ?>
            <p class="font-code-sm text-code-sm text-on-surface-variant text-center">+<?= count($type->fields) - 5 ?> more fields</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-3">
        <a href="<?= $adminPrefix ?>/c/<?= $this->e($name) ?>" class="flex-1 bg-surface-container-high text-on-surface py-2 px-4 rounded font-label-caps text-label-caps text-center hover:bg-surface-container-highest transition-colors">
          Browse
        </a>
        <a href="<?= $adminPrefix ?>/c/<?= $this->e($name) ?>/create" class="flex-1 bg-secondary text-on-secondary py-2 px-4 rounded font-label-caps text-label-caps text-center hard-step-shadow hover:brightness-110 transition-all">
          Add New
        </a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- System Info -->
<div class="mt-8 bg-surface-container border border-outline-variant p-6">
  <h3 class="font-headline-lg text-headline-lg text-secondary mb-4">System Information</h3>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="flex items-center justify-between p-3 bg-surface-container-low rounded">
      <span class="font-code-sm text-code-sm text-on-surface-variant">Storage Driver</span>
      <span class="font-code-sm text-code-sm text-secondary"><?= $this->e(config('cms.storage', 'database')) ?></span>
    </div>
    <div class="flex items-center justify-between p-3 bg-surface-container-low rounded">
      <span class="font-code-sm text-code-sm text-on-surface-variant">Cache</span>
      <span class="font-code-sm text-code-sm text-secondary"><?= config('cms.cache.enabled', true) ? 'Enabled' : 'Disabled' ?></span>
    </div>
    <div class="flex items-center justify-between p-3 bg-surface-container-low rounded">
      <span class="font-code-sm text-code-sm text-on-surface-variant">Taxonomy</span>
      <span class="font-code-sm text-code-sm text-secondary"><?= config('cms.taxonomy.enabled', true) ? 'Enabled' : 'Disabled' ?></span>
    </div>
    <div class="flex items-center justify-between p-3 bg-surface-container-low rounded">
      <span class="font-code-sm text-code-sm text-on-surface-variant">Revisions</span>
      <span class="font-code-sm text-code-sm text-secondary"><?= config('cms.revisions.enabled', true) ? 'Enabled' : 'Disabled' ?></span>
    </div>
  </div>
</div>