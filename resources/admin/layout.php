<?php /** @var string $content @var \Tavp\Cms\Admin\AdminAuth $__auth @var array $__types @var string $__brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $this->e($__brand) ?> Admin</title>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "background": "#0d131f", "on-background": "#dde2f3",
        "surface": "#0d131f", "surface-container-lowest": "#080e1a",
        "surface-container-low": "#161c27", "surface-container": "#1a202c",
        "surface-container-high": "#242a36", "surface-container-highest": "#2f3542",
        "on-surface": "#dde2f3", "on-surface-variant": "#c5c6cd",
        "primary": "#bdc7dc", "on-primary": "#273141",
        "primary-container": "#2d3748", "secondary": "#e6c446",
        "on-secondary": "#3b2f00", "secondary-container": "#ac8e0a",
        "tertiary": "#bcc7dd", "on-tertiary-container": "#95a0b5",
        "outline": "#8f9097", "outline-variant": "#45474c", "error": "#ffb4ab"
      },
      fontFamily: { "headline-xl": ["Geist"], "headline-lg": ["Geist"], "body-md": ["Inter"], "code-sm": ["JetBrains Mono"], "label-caps": ["JetBrains Mono"] },
      fontSize: {
        "headline-xl": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
        "code-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}]
      }
    }
  }
}
</script>
<style>
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  .hard-step-shadow { box-shadow: 2px 2px 0px 0px #000000; }
  .performance-card { border-top: 2px solid #e6c446; }
  body { background-color: #0d131f; color: #dde2f3; font-family: 'Inter', sans-serif; }
  ::-webkit-scrollbar { width: 6px; }
  ::-webkit-scrollbar-track { background: #1a202c; }
  ::-webkit-scrollbar-thumb { background: #4a5568; border-radius: 3px; }
  ::-webkit-scrollbar-thumb:hover { background: #e6c446; }
  .sidebar-collapsed .nav-label { display: none; }
  .sidebar-collapsed .nav-section { display: none; }
  .sidebar-collapsed .sidebar-header-text { display: none; }
  .sidebar-collapsed .sidebar-bottom-text { display: none; }
  .sidebar-collapsed { width: 64px; }
  .sidebar-collapsed + main { margin-left: 64px; }
</style>
</head>
<body class="overflow-x-hidden" x-data="{ sidebarCollapsed: false }">

<!-- Sidebar -->
<aside class="h-screen fixed left-0 top-0 bg-surface-container border-r border-outline-variant flex flex-col z-50 overflow-hidden transition-all duration-300"
       :class="sidebarCollapsed ? 'w-[68px]' : 'w-64'">

  <!-- Header -->
  <div class="px-4 pt-6 pb-4 flex items-center justify-between">
    <div class="flex items-center gap-2 min-w-0">
      <div class="w-8 h-8 bg-secondary rounded flex items-center justify-center shrink-0">
        <span class="font-headline-lg text-on-secondary text-sm font-bold">T</span>
      </div>
      <div x-show="!sidebarCollapsed" x-transition class="min-w-0">
        <h1 class="font-headline-lg text-headline-lg font-bold text-secondary tracking-tight truncate"><?= $this->e($__brand) ?></h1>
        <p class="font-code-sm text-code-sm text-on-surface-variant opacity-60">admin v1.0</p>
      </div>
    </div>
    <button @click="sidebarCollapsed = !sidebarCollapsed" class="text-on-surface-variant hover:text-secondary transition-colors shrink-0" :title="sidebarCollapsed ? 'Expand' : 'Minimize'">
      <span class="material-symbols-outlined text-xl" x-text="sidebarCollapsed ? 'chevron_right' : 'chevron_left'"></span>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto px-3 space-y-1 pb-4">
    <a href="/admin" class="flex items-center gap-3 px-3 py-2.5 text-secondary bg-primary-container/10 rounded transition-all duration-200" :class="sidebarCollapsed ? 'justify-center' : ''">
      <span class="material-symbols-outlined text-xl">dashboard</span>
      <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap">Dashboard</span>
    </a>

    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-on-surface-variant/40">
      <span class="font-label-caps text-label-caps uppercase tracking-widest">Content</span>
    </div>
    <?php foreach ($__types as $name => $t): ?>
      <a href="/admin/c/<?= $this->e($name) ?>" class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded transition-colors duration-200" :class="sidebarCollapsed ? 'justify-center' : ''">
        <span class="material-symbols-outlined text-xl">description</span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap"><?= $this->e($t->label) ?></span>
      </a>
    <?php endforeach; ?>

    <?php if (config('cms.taxonomy.enabled', true)): ?>
      <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-on-surface-variant/40">
        <span class="font-label-caps text-label-caps uppercase tracking-widest">Klasifikasi</span>
      </div>
      <a href="/admin/taxonomy/category" class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded transition-colors duration-200" :class="sidebarCollapsed ? 'justify-center' : ''">
        <span class="material-symbols-outlined text-xl">category</span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap">Kategori</span>
      </a>
      <a href="/admin/taxonomy/tag" class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded transition-colors duration-200" :class="sidebarCollapsed ? 'justify-center' : ''">
        <span class="material-symbols-outlined text-xl">sell</span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap">Tag</span>
      </a>
    <?php endif; ?>

    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-on-surface-variant/40">
      <span class="font-label-caps text-label-caps uppercase tracking-widest">Site</span>
    </div>
    <?php
    $siteMenus = [
        ['href' => '/admin/menus', 'icon' => 'menu', 'label' => 'Menu'],
        ['href' => '/admin/media', 'icon' => 'image', 'label' => 'Media'],
        ['href' => '/admin/settings', 'icon' => 'settings', 'label' => 'Settings'],
        ['href' => '/admin/teams', 'icon' => 'group', 'label' => 'Teams'],
        ['href' => '/admin/analytics', 'icon' => 'analytics', 'label' => 'Analytics'],
    ];
    ?>
    <?php foreach ($siteMenus as $m): ?>
      <a href="<?= $this->e($m['href']) ?>" class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded transition-colors duration-200" :class="sidebarCollapsed ? 'justify-center' : ''">
        <span class="material-symbols-outlined text-xl"><?= $this->e($m['icon']) ?></span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap"><?= $this->e($m['label']) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- Bottom section -->
  <div class="px-3 pb-4 pt-2 border-t border-outline-variant">
    <a href="/admin/c/home/create" class="w-full bg-secondary text-on-secondary py-2.5 px-3 rounded font-label-caps text-label-caps hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all text-center block mb-3" :class="sidebarCollapsed ? 'px-0 text-xs' : ''">
      <span x-show="!sidebarCollapsed" x-transition>+ NEW POST</span>
      <span x-show="sidebarCollapsed" x-transition class="material-symbols-outlined text-xl">add</span>
    </a>
    <div class="flex items-center gap-2 px-2" :class="sidebarCollapsed ? 'justify-center' : ''">
      <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center shrink-0">
        <span class="material-symbols-outlined text-sm">person</span>
      </div>
      <div x-show="!sidebarCollapsed" x-transition class="flex-1 min-w-0">
        <p class="font-label-caps text-label-caps truncate"><?= $this->e($__auth_email ?? 'User') ?></p>
        <?php if ($__rbac !== null && !empty($__auth_email)): ?>
          <p class="font-code-sm text-code-sm text-on-surface-variant text-[10px]">Role: <?= $this->e($__rbac->role($__auth_email)) ?></p>
        <?php endif; ?>
      </div>
      <form method="post" action="/admin/logout" class="shrink-0">
        <button class="text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">logout</span></button>
      </form>
    </div>
  </div>
</aside>

<!-- Main Content -->
<main class="min-h-screen bg-background transition-all duration-300" :class="sidebarCollapsed ? 'ml-[68px]' : 'ml-64'">
  <div class="max-w-[1280px] mx-auto px-10 py-8">
    <?= $content ?>
  </div>
</main>

</body>
</html>
