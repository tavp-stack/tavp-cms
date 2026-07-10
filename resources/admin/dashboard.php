<?php /** @var array $counts @var array $__types */ ?>
<h1 class="text-2xl font-bold mb-8">Dashboard</h1>

<?php if (config('cms.seo.enabled', true)): ?>
  <div class="mb-6">
    <a href="<?= $this->e(config('cms.seo.sitemap_path', '/sitemap.xml')) ?>" target="_blank" class="text-sm text-[#e6c446] hover:underline">View Sitemap &rarr;</a>
  </div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
  <?php foreach ($__types as $name => $type): ?>
    <a href="/admin/c/<?= $this->e($name) ?>" class="block rounded-xl border border-[#45474c] bg-[#1a202c] p-6 hover:border-[#e6c446] transition-colors">
      <div class="text-3xl font-bold text-[#e6c446]"><?= (int) ($counts[$name] ?? 0) ?></div>
      <div class="mt-1 text-[#dde2f3]"><?= $this->e($type->label) ?></div>
      <div class="mt-4 text-sm text-[#8f9097]">Manage <?= $this->e(strtolower($type->label)) ?> &rarr;</div>
    </a>
  <?php endforeach; ?>
</div>

<?php if (config('cms.taxonomy.enabled', true)): ?>
  <h2 class="text-lg font-bold mb-4">Taxonomy</h2>
  <div class="flex gap-4 mb-8">
    <a href="/admin/taxonomy/category" class="rounded border border-[#45474c] px-4 py-2 hover:bg-[#1a202c]">Categories</a>
    <a href="/admin/taxonomy/tag" class="rounded border border-[#45474c] px-4 py-2 hover:bg-[#1a202c]">Tags</a>
  </div>
<?php endif; ?>

<?php if (config('cms.api.enabled', true)): ?>
  <h2 class="text-lg font-bold mb-4">Headless API</h2>
  <div class="rounded border border-[#45474c] bg-[#1a202c] p-4 mb-8">
    <p class="text-sm text-[#8f9097] mb-2">REST API endpoint:</p>
    <code class="text-[#e6c446] text-sm">/<?= $this->e(config('cms.api.prefix', 'api/cms')) ?></code>
    <p class="text-xs text-[#8f9097] mt-2">See README for authentication and usage details.</p>
  </div>
<?php endif; ?>
