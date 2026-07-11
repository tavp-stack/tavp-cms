<?php /** @var array $counts @var array $__types */ ?>

<!-- Header -->
<header class="flex justify-between items-end mb-8">
  <div>
    <h2 class="text-2xl font-bold text-on-surface mb-2">Dashboard</h2>
    <?php if (config('cms.seo.enabled', true)): ?>
      <a class="text-sm text-primary-container hover:text-primary flex items-center gap-1 group transition-colors" href="<?= $this->e(config('cms.seo.sitemap_path', '/sitemap.xml')) ?>" target="_blank">
        View Sitemap
        <span class="material-symbols-outlined text-[14px] transition-transform group-hover:translate-x-1">arrow_forward</span>
      </a>
    <?php endif; ?>
  </div>
  <a href="/admin/c/home/create" class="bg-primary-container text-on-primary font-bold px-6 py-3 rounded hover:bg-primary transition-all duration-200 active:scale-95 flex items-center gap-2 kinetic-shadow">
    <span class="material-symbols-outlined text-[20px]">add</span>
    <span>New Entry</span>
  </a>
</header>

<!-- Content Type Cards -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
  <?php foreach ($__types as $name => $type): ?>
    <a href="/admin/c/<?= $this->e($name) ?>" class="bg-surface-container-lowest kinetic-shadow kinetic-hover transition-all duration-300 p-8 flex flex-col group">
      <span class="text-4xl font-bold text-primary-fixed mb-2"><?= (int) ($counts[$name] ?? 0) ?></span>
      <h3 class="text-lg font-semibold text-on-surface mb-6"><?= $this->e($type->label) ?></h3>
      <div class="mt-auto">
        <span class="text-on-surface-variant group-hover:text-primary transition-colors flex items-center gap-1">
          Manage <?= $this->e(strtolower($type->label)) ?>
          <span class="material-symbols-outlined text-[18px] opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">trending_flat</span>
        </span>
      </div>
    </a>
  <?php endforeach; ?>
</section>

<!-- Klasifikasi Section -->
<?php if (config('cms.taxonomy.enabled', true)): ?>
  <section class="mb-10">
    <h3 class="text-lg font-semibold text-on-surface mb-6 flex items-center gap-2">
      Klasifikasi
      <span class="w-12 h-[1px] bg-outline-variant/30"></span>
    </h3>
    <div class="flex flex-wrap gap-4">
      <a href="/admin/taxonomy/category" class="bg-surface-container-low border border-outline-variant/20 px-8 py-3 text-sm font-mono text-on-surface-variant hover:border-primary-container/50 hover:text-on-surface transition-all duration-200 uppercase tracking-widest kinetic-shadow">
        Kategori
      </a>
      <a href="/admin/taxonomy/tag" class="bg-surface-container-low border border-outline-variant/20 px-8 py-3 text-sm font-mono text-on-surface-variant hover:border-primary-container/50 hover:text-on-surface transition-all duration-200 uppercase tracking-widest kinetic-shadow">
        Tag
      </a>
    </div>
  </section>
<?php endif; ?>

<!-- Quick Links -->
<section class="mb-10">
  <h3 class="text-lg font-semibold text-on-surface mb-6 flex items-center gap-2">
    Site Management
    <span class="w-12 h-[1px] bg-outline-variant/30"></span>
  </h3>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="/admin/menus" class="bg-surface-container-low border border-outline-variant/20 px-6 py-4 hover:border-primary-container/50 transition-all duration-200 flex items-center gap-3 kinetic-shadow">
      <span class="material-symbols-outlined text-primary-container">menu</span>
      <span class="text-sm font-medium">Menu</span>
    </a>
    <a href="/admin/media" class="bg-surface-container-low border border-outline-variant/20 px-6 py-4 hover:border-primary-container/50 transition-all duration-200 flex items-center gap-3 kinetic-shadow">
      <span class="material-symbols-outlined text-primary-container">image</span>
      <span class="text-sm font-medium">Media</span>
    </a>
    <a href="/admin/settings" class="bg-surface-container-low border border-outline-variant/20 px-6 py-4 hover:border-primary-container/50 transition-all duration-200 flex items-center gap-3 kinetic-shadow">
      <span class="material-symbols-outlined text-primary-container">settings</span>
      <span class="text-sm font-medium">Settings</span>
    </a>
  </div>
</section>

<!-- API Info -->
<?php if (config('cms.api.enabled', true)): ?>
  <section>
    <h3 class="text-lg font-semibold text-on-surface mb-6 flex items-center gap-2">
      Headless API
      <span class="w-12 h-[1px] bg-outline-variant/30"></span>
    </h3>
    <div class="bg-surface-container-lowest kinetic-shadow p-6 border border-outline-variant/20">
      <p class="text-sm text-on-surface-variant mb-2">REST API endpoint:</p>
      <code class="text-primary-container text-sm font-mono">/<?= $this->e(config('cms.api.prefix', 'api/cms')) ?></code>
      <p class="text-xs text-on-surface-variant mt-2">See documentation for authentication and usage details.</p>
    </div>
  </section>
<?php endif; ?>
