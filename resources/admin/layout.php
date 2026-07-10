<?php /** @var string $content @var \Tavp\Cms\Admin\AdminAuth $__auth @var array $__types @var string $__brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $this->e($__brand) ?> Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>body{background:#0d131f;color:#dde2f3;font-family:Inter,system-ui,sans-serif}</style>
</head>
<body class="min-h-screen">
<div class="flex min-h-screen">
  <aside class="w-64 border-r border-[#45474c] p-6 flex flex-col">
    <a href="/admin" class="text-xl font-bold text-[#e6c446] mb-8"><?= $this->e($__brand) ?> <span class="text-[#8f9097] text-sm">admin</span></a>

    <nav class="space-y-1 flex-1">
      <a href="/admin" class="block px-3 py-2 rounded hover:bg-[#1a202c]">Dashboard</a>
      <a href="/admin/search" class="block px-3 py-2 rounded hover:bg-[#1a202c]">Search</a>

      <div class="pt-4 pb-1 px-3 text-xs font-bold text-[#8f9097] uppercase tracking-wider">Content</div>
      <?php foreach ($__types as $name => $t): ?>
        <a href="/admin/c/<?= $this->e($name) ?>" class="block px-3 py-2 rounded hover:bg-[#1a202c]"><?= $this->e($t->label) ?></a>
      <?php endforeach; ?>

      <?php if (config('cms.taxonomy.enabled', true)): ?>
        <div class="pt-4 pb-1 px-3 text-xs font-bold text-[#8f9097] uppercase tracking-wider">Taxonomy</div>
        <a href="/admin/taxonomy/category" class="block px-3 py-2 rounded hover:bg-[#1a202c]">Categories</a>
        <a href="/admin/taxonomy/tag" class="block px-3 py-2 rounded hover:bg-[#1a202c]">Tags</a>
      <?php endif; ?>
    </nav>

    <div class="pt-6 border-t border-[#45474c] text-sm">
      <?php if ($__rbac !== null): ?>
        <div class="text-xs text-[#8f9097] mb-1">Role: <?= $this->e($__rbac->role((string) $__auth->user())) ?></div>
      <?php endif; ?>
      <div class="text-[#8f9097] mb-2 truncate"><?= $this->e($__auth->user()) ?></div>
      <form method="post" action="/admin/logout"><button class="text-[#ffb4ab] hover:underline">Sign out</button></form>
    </div>
  </aside>
  <main class="flex-1 p-10"><?= $content ?></main>
</div>
</body>
</html>
