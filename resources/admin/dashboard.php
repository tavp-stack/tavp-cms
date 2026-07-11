<?php /** @var array $counts @var array $__types */ ?>
<?php
$iconMap = [
    'home' => 'home',
    'page' => 'description',
    'post' => 'article',
];
?>
<!-- Metrics Overview -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-gutter">
  <?php foreach ($__types as $name => $type): ?>
    <a href="/admin/c/<?= $this->e($name) ?>" class="bg-surface-container p-6 border border-outline-variant performance-card flex justify-between items-end hover:hard-step-shadow hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all duration-150">
      <div>
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">TOTAL <?= strtoupper($this->e($type->label)) ?></p>
        <h2 class="font-headline-xl text-headline-xl"><?= (int) ($counts[$name] ?? 0) ?></h2>
      </div>
      <div class="text-right">
        <span class="material-symbols-outlined text-secondary text-2xl"><?= $this->e($iconMap[$name] ?? 'description') ?></span>
      </div>
    </a>
  <?php endforeach; ?>
</section>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
  <!-- Content Types List -->
  <div class="lg:col-span-2 space-y-gutter">
    <div class="bg-surface-container-high border border-outline-variant p-6">
      <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Content Types</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($__types as $name => $type): ?>
          <a href="/admin/c/<?= $this->e($name) ?>" class="bg-surface-container p-4 border border-outline-variant hover:border-secondary transition-colors">
            <div class="flex items-center gap-3 mb-2">
              <span class="material-symbols-outlined text-secondary"><?= $this->e($iconMap[$name] ?? 'description') ?></span>
              <span class="font-label-caps text-label-caps"><?= $this->e($type->label) ?></span>
            </div>
            <p class="font-code-sm text-code-sm text-on-surface-variant"><?= (int) ($counts[$name] ?? 0) ?> records</p>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (config('cms.taxonomy.enabled', true)): ?>
      <div class="bg-surface-container border border-outline-variant p-6">
        <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Klasifikasi</h3>
        <div class="flex gap-4">
          <a href="/admin/taxonomy/category" class="bg-surface-container-low border border-outline-variant px-6 py-3 font-label-caps text-label-caps text-on-surface-variant hover:border-secondary transition-colors">Kategori</a>
          <a href="/admin/taxonomy/tag" class="bg-surface-container-low border border-outline-variant px-6 py-3 font-label-caps text-label-caps text-on-surface-variant hover:border-secondary transition-colors">Tag</a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Quick Links -->
    <div class="bg-surface-container border border-outline-variant p-6">
      <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Site Management</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="/admin/menus" class="bg-surface-container-low border border-outline-variant p-4 hover:border-secondary transition-colors flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary">menu</span>
          <span class="font-label-caps text-label-caps">Menu</span>
        </a>
        <a href="/admin/media" class="bg-surface-container-low border border-outline-variant p-4 hover:border-secondary transition-colors flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary">image</span>
          <span class="font-label-caps text-label-caps">Media</span>
        </a>
        <a href="/admin/settings" class="bg-surface-container-low border border-outline-variant p-4 hover:border-secondary transition-colors flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary">settings</span>
          <span class="font-label-caps text-label-caps">Settings</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Right Sidebar -->
  <div class="space-y-gutter">
    <!-- API Info -->
    <?php if (config('cms.api.enabled', true)): ?>
      <div class="bg-surface-container border border-outline-variant p-6">
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">HEADLESS API</p>
        <code class="text-secondary font-code-sm text-code-sm">/<?= $this->e(config('cms.api.prefix', 'api/cms')) ?></code>
        <p class="font-code-sm text-code-sm text-on-surface-variant mt-2">REST API endpoint</p>
      </div>
    <?php endif; ?>

    <!-- Sitemap -->
    <?php if (config('cms.seo.enabled', true)): ?>
      <div class="bg-surface-container border border-outline-variant p-6">
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">SITEMAP</p>
        <a href="<?= $this->e(config('cms.seo.sitemap_path', '/sitemap.xml')) ?>" target="_blank" class="text-secondary font-code-sm text-code-sm hover:underline">/sitemap.xml</a>
      </div>
    <?php endif; ?>

    <!-- System Status -->
    <div class="bg-surface-container border border-outline-variant p-6">
      <p class="font-label-caps text-label-caps text-on-surface-variant mb-4">SYSTEM STATUS</p>
      <div class="space-y-3">
        <div class="flex justify-between items-center">
          <span class="font-code-sm text-code-sm text-on-surface-variant">Storage</span>
          <span class="font-code-sm text-code-sm text-secondary"><?= $this->e(config('cms.storage', 'database')) ?></span>
        </div>
        <div class="flex justify-between items-center">
          <span class="font-code-sm text-code-sm text-on-surface-variant">Cache</span>
          <span class="font-code-sm text-code-sm text-secondary"><?= config('cms.cache.enabled', true) ? 'ON' : 'OFF' ?></span>
        </div>
        <div class="flex justify-between items-center">
          <span class="font-code-sm text-code-sm text-on-surface-variant">PHP</span>
          <span class="font-code-sm text-code-sm text-secondary"><?= PHP_VERSION ?></span>
        </div>
      </div>
    </div>
  </div>
</div>
