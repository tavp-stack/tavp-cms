<?php /** @var string $content @var \Tavp\Cms\Admin\AdminAuth $__auth @var array $__types @var string $__brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $this->e($__brand) ?> Admin</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "background": "#0d131f",
        "on-background": "#dde2f3",
        "surface": "#0d131f",
        "surface-container-lowest": "#080e1a",
        "surface-container-low": "#161c27",
        "surface-container": "#1a202c",
        "surface-container-high": "#242a36",
        "surface-container-highest": "#2f3542",
        "on-surface": "#dde2f3",
        "on-surface-variant": "#c5c6cd",
        "primary": "#bdc7dc",
        "on-primary": "#273141",
        "primary-container": "#2d3748",
        "secondary": "#e6c446",
        "on-secondary": "#3b2f00",
        "secondary-container": "#ac8e0a",
        "tertiary": "#bcc7dd",
        "on-tertiary-container": "#95a0b5",
        "outline": "#8f9097",
        "outline-variant": "#45474c",
        "error": "#ffb4ab"
      },
      fontFamily: {
        "headline-xl": ["Geist"], "headline-lg": ["Geist"],
        "body-md": ["Inter"], "code-sm": ["JetBrains Mono"], "label-caps": ["JetBrains Mono"]
      },
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
</style>
</head>
<body class="overflow-x-hidden" x-data="{ mobileMenu: false }">

<!-- Sidebar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container border-r border-outline-variant flex flex-col py-gutter px-component-padding-x z-50">
  <div class="mb-10">
    <h1 class="font-headline-lg text-headline-lg font-bold text-secondary tracking-tight"><?= $this->e($__brand) ?> <span class="text-on-surface-variant font-normal text-body-md opacity-60">admin</span></h1>
    <p class="font-code-sm text-code-sm text-on-surface-variant opacity-60">v1.0</p>
  </div>

  <nav class="flex-1 space-y-1">
    <a href="/admin" class="flex items-center px-4 py-3 text-secondary border-r-2 border-secondary bg-primary-container/10 font-body-md text-body-md transition-all duration-200">
      <span class="material-symbols-outlined mr-3">dashboard</span>
      Dashboard
    </a>

    <div class="pt-4 pb-1 px-4 text-on-surface-variant/40">
      <span class="font-label-caps text-label-caps uppercase tracking-widest">Content</span>
    </div>
    <?php foreach ($__types as $name => $t): ?>
      <a href="/admin/c/<?= $this->e($name) ?>" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
        <span class="material-symbols-outlined mr-3"><?= $this->e($t->icon ?? 'description') ?></span>
        <?= $this->e($t->label) ?>
      </a>
    <?php endforeach; ?>

    <?php if (config('cms.taxonomy.enabled', true)): ?>
      <div class="pt-4 pb-1 px-4 text-on-surface-variant/40">
        <span class="font-label-caps text-label-caps uppercase tracking-widest">Klasifikasi</span>
      </div>
      <a href="/admin/taxonomy/category" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
        <span class="material-symbols-outlined mr-3">category</span>
        Kategori
      </a>
      <a href="/admin/taxonomy/tag" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
        <span class="material-symbols-outlined mr-3">sell</span>
        Tag
      </a>
    <?php endif; ?>

    <div class="pt-4 pb-1 px-4 text-on-surface-variant/40">
      <span class="font-label-caps text-label-caps uppercase tracking-widest">Site</span>
    </div>
    <a href="/admin/menus" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
      <span class="material-symbols-outlined mr-3">menu</span>
      Menu
    </a>
    <a href="/admin/media" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
      <span class="material-symbols-outlined mr-3">image</span>
      Media
    </a>
    <a href="/admin/settings" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
      <span class="material-symbols-outlined mr-3">settings</span>
      Settings
    </a>
    <a href="/admin/teams" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
      <span class="material-symbols-outlined mr-3">group</span>
      Teams
    </a>
    <a href="/admin/billing" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
      <span class="material-symbols-outlined mr-3">payments</span>
      Billing
    </a>
    <a href="/admin/analytics" class="flex items-center px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors duration-200 font-body-md text-body-md">
      <span class="material-symbols-outlined mr-3">analytics</span>
      Analytics
    </a>
  </nav>

  <div class="mt-auto pt-6">
    <a href="/admin/c/home/create" class="w-full bg-secondary text-on-secondary py-3 px-4 rounded font-label-caps text-label-caps hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all text-center block">
      + NEW POST
    </a>
    <div class="mt-6 flex items-center space-x-3 px-2">
      <div class="w-8 h-8 rounded-full bg-primary-container overflow-hidden border border-outline-variant flex items-center justify-center">
        <span class="material-symbols-outlined text-sm">person</span>
      </div>
      <div class="flex-1 min-w-0">
        <?php
          $user = $__auth->user();
          $userName = is_object($user) ? ($user->email ?? $user->name ?? 'User') : (string) ($user ?? 'User');
        ?>
        <p class="font-label-caps text-label-caps truncate"><?= $this->e($userName) ?></p>
        <?php if ($__rbac !== null && is_object($user)): ?>
          <p class="font-code-sm text-code-sm text-on-surface-variant text-[10px]">Role: <?= $this->e($__rbac->role($user->email ?? '')) ?></p>
        <?php endif; ?>
      </div>
      <form method="post" action="/admin/logout">
        <button class="text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">logout</span></button>
      </form>
    </div>
  </div>
</aside>

<!-- Main Content -->
<main class="ml-64 min-h-screen bg-background">
  <div class="max-w-[1280px] mx-auto p-gutter">
    <?= $content ?>
  </div>
</main>

</body>
</html>
