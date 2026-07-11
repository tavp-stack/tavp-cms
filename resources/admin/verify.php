<?php /** @var string $email @var string|null $error @var string $brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Enter code — <?= $this->e($brand) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style>body{background:#051424;color:#d4e4fa;font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-sm">
  <h1 class="text-2xl font-bold text-primary mb-1">Check your e-mail</h1>
  <p class="text-on-surface-variant text-sm mb-8">We sent a 6-digit code to <span class="text-on-surface"><?= $this->e($email) ?></span>.</p>
  <?php if ($error): ?>
    <div class="mb-4 rounded border border-error-container bg-error-container/20 px-3 py-2 text-sm text-error"><?= $this->e($error) ?></div>
  <?php endif; ?>
  <form method="post" action="/admin/verify" class="space-y-4">
    <div>
      <label class="block text-sm mb-1">Code</label>
      <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus class="w-full rounded bg-surface-container border border-outline-variant/30 px-3 py-2 tracking-[0.5em] text-center text-lg focus:border-primary-container outline-none transition-colors" placeholder="000000">
    </div>
    <button class="w-full rounded bg-primary-container text-on-primary font-bold px-4 py-3 hover:bg-primary transition-all duration-200 active:scale-95">Verify &amp; sign in</button>
  </form>
  <form method="post" action="/admin/login" class="mt-4 text-center">
    <button class="text-sm text-on-surface-variant hover:underline">Use a different e-mail</button>
  </form>
</div>
</body>
</html>
