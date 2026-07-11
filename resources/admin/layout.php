<?php /** @var string $content @var \Tavp\Cms\Admin\AdminAuth $__auth @var array $__types @var string $__brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $this->e($__brand) ?> Admin</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "background": "#051424",
        "on-background": "#d4e4fa",
        "surface": "#051424",
        "surface-container-lowest": "#010f1f",
        "surface-container-low": "#0d1c2d",
        "surface-container": "#122131",
        "surface-container-high": "#1c2b3c",
        "surface-container-highest": "#273647",
        "on-surface": "#d4e4fa",
        "on-surface-variant": "#d1c6ab",
        "primary": "#ffecb9",
        "on-primary": "#3c2f00",
        "primary-container": "#facc15",
        "on-primary-container": "#6c5700",
        "secondary": "#bec6e0",
        "on-secondary": "#283044",
        "secondary-container": "#3f465c",
        "on-secondary-container": "#adb4ce",
        "tertiary": "#e6edff",
        "on-tertiary-container": "#4f5a6e",
        "outline": "#9a9078",
        "outline-variant": "#4d4632",
        "error": "#ffb4ab",
        "on-error": "#690005"
      },
      fontFamily: {
        "sans": ["Inter"],
        "mono": ["JetBrains Mono"]
      }
    }
  }
}
</script>
<style>
  .kinetic-shadow { box-shadow: 20px 20px 40px rgba(0, 0, 0, 0.4); }
  .kinetic-hover:hover { transform: translate(-4px, -4px); box-shadow: 24px 24px 48px rgba(0, 0, 0, 0.5); }
  .glass-sidebar { background: rgba(1, 15, 31, 0.85); backdrop-filter: blur(16px); }
  .active-glow { background: linear-gradient(90deg, rgba(238, 194, 0, 0.1) 0%, transparent 100%); }
  body { background-color: #051424; color: #d4e4fa; font-family: 'Inter', sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
</head>
<body class="antialiased overflow-x-hidden">

<!-- Sidebar -->
<aside class="fixed left-0 top-0 h-full w-64 glass-sidebar border-r border-outline-variant/10 z-50 flex flex-col py-8 px-4">
  <div class="mb-10 px-4">
    <h1 class="text-xl font-bold text-primary tracking-tighter flex items-center gap-2">
      <?= $this->e($__brand) ?> <span class="text-on-surface-variant font-normal text-sm opacity-60">admin</span>
    </h1>
    <p class="text-xs text-on-surface-variant/50 mt-1 uppercase tracking-widest font-mono">Kinetic CMS v1.0</p>
  </div>

  <nav class="flex-1 space-y-1">
    <div class="px-4 py-2">
      <span class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest">General</span>
    </div>
    <a href="/admin" class="flex items-center gap-3 px-4 py-3 text-primary font-bold border-r-2 border-primary active-glow transition-all duration-150">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
      <span class="text-sm">Dashboard</span>
    </a>
    <a href="/admin/search" class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-150 group">
      <span class="material-symbols-outlined">search</span>
      <span class="text-sm">Search</span>
    </a>

    <div class="px-4 py-6 mt-4">
      <span class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest">Content</span>
    </div>
    <?php foreach ($__types as $name => $t): ?>
      <a href="/admin/c/<?= $this->e($name) ?>" class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-150 group">
        <span class="material-symbols-outlined"><?= $this->e($t->icon ?? 'description') ?></span>
        <span class="text-sm"><?= $this->e($t->label) ?></span>
      </a>
    <?php endforeach; ?>

    <?php if (config('cms.taxonomy.enabled', true)): ?>
      <div class="px-4 py-6 mt-4">
        <span class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest">Klasifikasi</span>
      </div>
      <a href="/admin/taxonomy/category" class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-150 group">
        <span class="material-symbols-outlined">account_tree</span>
        <span class="text-sm">Kategori</span>
      </a>
      <a href="/admin/taxonomy/tag" class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-150 group">
        <span class="material-symbols-outlined">sell</span>
        <span class="text-sm">Tag</span>
      </a>
    <?php endif; ?>

    <div class="px-4 py-6 mt-4">
      <span class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest">Site</span>
    </div>
    <a href="/admin/menus" class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-150 group">
      <span class="material-symbols-outlined">menu</span>
      <span class="text-sm">Menu</span>
    </a>
    <a href="/admin/media" class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-150 group">
      <span class="material-symbols-outlined">image</span>
      <span class="text-sm">Media</span>
    </a>
    <a href="/admin/settings" class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest transition-all duration-150 group">
      <span class="material-symbols-outlined">settings</span>
      <span class="text-sm">Settings</span>
    </a>
  </nav>

  <div class="mt-auto pt-8 border-t border-outline-variant/10 px-4">
    <div class="flex flex-col gap-1 mb-6">
      <?php if ($__rbac !== null): ?>
        <span class="text-xs text-on-surface-variant/60">Role: <?= $this->e($__rbac->role((string) $__auth->user())) ?></span>
      <?php endif; ?>
      <span class="text-sm text-on-surface truncate"><?= $this->e($__auth->user()) ?></span>
    </div>
    <form method="post" action="/admin/logout">
      <button class="flex items-center gap-2 text-error hover:text-error/80 font-bold transition-colors">
        <span class="material-symbols-outlined text-[20px]">logout</span>
        <span class="text-sm">Sign out</span>
      </button>
    </form>
  </div>
</aside>

<!-- Main Content -->
<main class="ml-64 min-h-screen p-10 relative">
  <!-- Subtle Background -->
  <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/5 blur-[120px] rounded-full"></div>
    <div class="absolute top-[20%] -right-[5%] w-[30%] h-[50%] bg-surface-container-highest/20 blur-[100px] rounded-full"></div>
  </div>
  <div class="max-w-[1280px] mx-auto relative z-10">
    <?= $content ?>
  </div>
</main>

</body>
</html>
