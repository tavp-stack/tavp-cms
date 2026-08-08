<?php /** @var string $identifier @var string|null $error @var string $brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Enter code — <?= $this->e($brand) ?></title>
<link rel="stylesheet" href="/assets/admin.css"/>
<link rel="stylesheet" href="/assets/fonts.css"/>
<script defer src="/js/alpine.min.js"></script>
<style>body{background:#0d131f;color:#dde2f3;font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-sm">
  <h1 class="font-headline-lg text-headline-lg text-secondary mb-1">Check your e-mail</h1>
  <p class="font-body-md text-body-md text-on-surface-variant mb-8">We sent a 6-digit code to <span class="text-on-surface"><?= $this->e($identifier) ?></span>.</p>
  <?php if ($error): ?>
    <div class="mb-4 rounded border border-error bg-error/10 px-3 py-2 text-sm text-error"><?= $this->e($error) ?></div>
  <?php endif; ?>
  <form method="post" action="<?= $adminPrefix ?>/verify" class="space-y-4">
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">CODE</label>
      <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 tracking-[0.5em] text-center text-lg focus:border-secondary outline-none font-code-sm" placeholder="000000">
    </div>
    <button class="w-full bg-secondary text-on-secondary font-label-caps text-label-caps py-3 rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">VERIFY &amp; SIGN IN</button>
  </form>
  <a href="<?= $adminPrefix ?>/login" class="block mt-4 text-center font-body-md text-body-md text-on-surface-variant hover:text-secondary transition-colors">Use a different e-mail</a>
</div>
</body>
</html>
