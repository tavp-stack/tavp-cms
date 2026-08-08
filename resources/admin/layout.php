<?php /** @var string $content @var \Tavp\Cms\Admin\AdminAuth $__auth @var array $__types @var string $__brand */
// Read admin prefix from database first, fallback to config
$dbPrefix = null;
try {
    $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
    $dbPrefix = $settings?->get('admin.route_prefix');
} catch (\Throwable) {}
$adminPrefix = '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $this->e($__brand) ?> Admin</title>
<link rel="stylesheet" href="/assets/admin.css"/>
<link rel="stylesheet" href="/assets/fonts.css"/>
<link rel="stylesheet" href="/css/prism-tomorrow.min.css"/>
<link rel="stylesheet" href="/css/easymde.min.css"/>
<script defer src="/js/alpine.min.js"></script>
<script defer src="/js/easymde.min.js"></script>
<script defer src="/js/prism.min.js"></script>
<script defer src="/js/prism-php.min.js"></script>
<script defer src="/js/prism-javascript.min.js"></script>
<script defer src="/js/prism-css.min.js"></script>
<script defer src="/js/prism-bash.min.js"></script>
<script defer src="/js/prism-json.min.js"></script>
<script defer src="/js/prism-markdown.min.js"></script>
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
    <?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; ?>
    <a href="<?= $adminPrefix ?>" class="flex items-center gap-3 px-3 py-2.5 rounded transition-all duration-200 <?= $currentPath === $adminPrefix ? 'text-secondary bg-primary-container/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' ?>" :class="sidebarCollapsed ? 'justify-center' : ''">
      <span class="material-symbols-outlined text-xl">dashboard</span>
      <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap">Dashboard</span>
    </a>
    <a href="<?= $adminPrefix ?>/home" class="flex items-center gap-3 px-3 py-2.5 rounded transition-all duration-200 <?= str_starts_with($currentPath, $adminPrefix . '/home') ? 'text-secondary bg-primary-container/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' ?>" :class="sidebarCollapsed ? 'justify-center' : ''">
      <span class="material-symbols-outlined text-xl">home</span>
      <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap">Home</span>
    </a>

    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-on-surface-variant/40">
      <span class="font-label-caps text-label-caps uppercase tracking-widest">Content</span>
    </div>
    <?php
    // Resolve the edit URL of a single-record page type (home, contact, ...).
    $singleEdit = function (string $type) use ($adminPrefix): string {
        try {
            $recs = app()->getService(\Tavp\Cms\Bread\BreadManager::class)->browse($type);
            if (!empty($recs[0]['id'])) {
                return $adminPrefix . '/c/' . $type . '/' . $recs[0]['id'] . '/edit';
            }
        } catch (\Throwable $e) {}
        return $adminPrefix . '/c/' . $type . '/create';
    };
    $contentMenus = [
        ['href' => $singleEdit('home'), 'match' => $adminPrefix . '/c/home', 'icon' => 'cottage', 'label' => 'Landing Page'],
        ['href' => $adminPrefix . '/c/page', 'match' => $adminPrefix . '/c/page', 'icon' => 'description', 'label' => 'Pages'],
        ['href' => $adminPrefix . '/c/post', 'match' => $adminPrefix . '/c/post', 'icon' => 'article', 'label' => 'Blog'],
        ['href' => $singleEdit('contact'), 'match' => $adminPrefix . '/c/contact', 'icon' => 'mail', 'label' => 'Contact'],
        ['href' => $singleEdit('get_started'), 'match' => $adminPrefix . '/c/get_started', 'icon' => 'rocket_launch', 'label' => 'Get Started'],
        ['href' => $singleEdit('performance'), 'match' => $adminPrefix . '/c/performance', 'icon' => 'speed', 'label' => 'Performance'],
        ['href' => $singleEdit('documentation'), 'match' => $adminPrefix . '/c/documentation', 'icon' => 'menu_book', 'label' => 'Documentation'],
        ['href' => $adminPrefix . '/taxonomy/category', 'match' => $adminPrefix . '/taxonomy/category', 'icon' => 'category', 'label' => 'Categories'],
        ['href' => $adminPrefix . '/taxonomy/tag', 'match' => $adminPrefix . '/taxonomy/tag', 'icon' => 'sell', 'label' => 'Tags'],
    ];
    foreach ($contentMenus as $m):
      $active = str_starts_with($currentPath, $m['match']);
    ?>
      <a href="<?= $this->e($m['href']) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors duration-200 <?= $active ? 'text-secondary bg-primary-container/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' ?>" :class="sidebarCollapsed ? 'justify-center' : ''">
        <span class="material-symbols-outlined text-xl"><?= $this->e($m['icon']) ?></span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap"><?= $this->e($m['label']) ?></span>
      </a>
    <?php endforeach; ?>

    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-on-surface-variant/40">
      <span class="font-label-caps text-label-caps uppercase tracking-widest">Site</span>
    </div>
    <?php
    $siteMenus = [
        ['href' => $adminPrefix . '/menus', 'icon' => 'menu', 'label' => 'Menus', 'desc' => 'Navigation menus'],
        ['href' => $adminPrefix . '/media', 'icon' => 'image', 'label' => 'Media', 'desc' => 'Upload files'],
        ['href' => $adminPrefix . '/settings', 'icon' => 'settings', 'label' => 'Settings', 'desc' => 'Site configuration'],
        ['href' => $adminPrefix . '/users', 'icon' => 'group', 'label' => 'Users', 'desc' => 'Manage accounts'],
        ['href' => $adminPrefix . '/messages', 'icon' => 'mail', 'label' => 'Messages', 'desc' => 'Contact form messages'],
    ];
    ?>
    <?php foreach ($siteMenus as $m): $active = str_starts_with($currentPath, $m['href']); ?>
      <a href="<?= $this->e($m['href']) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors duration-200 <?= $active ? 'text-secondary bg-primary-container/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' ?>" :class="sidebarCollapsed ? 'justify-center' : ''" title="<?= $this->e($m['desc']) ?>">
        <span class="material-symbols-outlined text-xl"><?= $this->e($m['icon']) ?></span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap"><?= $this->e($m['label']) ?></span>
      </a>
    <?php endforeach; ?>

    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-on-surface-variant/40">
      <span class="font-label-caps text-label-caps uppercase tracking-widest">SEO & Analytics</span>
    </div>
    <?php
    $seoMenus = [
        ['href' => $adminPrefix . '/seo', 'icon' => 'search', 'label' => 'SEO', 'desc' => 'Search engine optimization'],
        ['href' => $adminPrefix . '/analytics', 'icon' => 'analytics', 'label' => 'Analytics', 'desc' => 'Traffic insights'],
        ['href' => $adminPrefix . '/settings', 'icon' => 'settings', 'label' => 'Security & Footer', 'desc' => 'Captcha type, admin path, footer settings'],
    ];
    ?>
    <?php foreach ($seoMenus as $m): $active = str_starts_with($currentPath, $m['href']); ?>
      <a href="<?= $this->e($m['href']) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors duration-200 <?= $active ? 'text-secondary bg-primary-container/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' ?>" :class="sidebarCollapsed ? 'justify-center' : ''" title="<?= $this->e($m['desc']) ?>">
        <span class="material-symbols-outlined text-xl"><?= $this->e($m['icon']) ?></span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap"><?= $this->e($m['label']) ?></span>
      </a>
    <?php endforeach; ?>

    <?php
    // Check if user is admin to show BREAD manager
    $isAdmin = false;
    if ($__rbac !== null && !empty($__auth_email)) {
      $userRole = $__rbac->role($__auth_email);
      $isAdmin = ($userRole === 'admin');
    }
    ?>
    <?php if ($isAdmin): ?>
      <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-on-surface-variant/40">
        <span class="font-label-caps text-label-caps uppercase tracking-widest">Advanced</span>
      </div>
      <a href="<?= $adminPrefix ?>/bread" class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded transition-colors duration-200" :class="sidebarCollapsed ? 'justify-center' : ''">
        <span class="material-symbols-outlined text-xl">construction</span>
        <span x-show="!sidebarCollapsed" x-transition class="font-body-md text-body-md whitespace-nowrap">BREAD Manager</span>
      </a>
    <?php endif; ?>
  </nav>

  <!-- Bottom section -->
  <div class="px-3 pb-4 pt-2 border-t border-outline-variant">
    <a href="<?= $adminPrefix ?>/c/post/create" class="w-full bg-secondary text-on-secondary py-2.5 px-3 rounded font-label-caps text-label-caps hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all text-center block mb-3" :class="sidebarCollapsed ? 'px-0 text-xs' : ''">
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
      <form method="post" action="<?= $adminPrefix ?>/logout" class="shrink-0">
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
