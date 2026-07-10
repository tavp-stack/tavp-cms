<?php /** @var string $email @var string|null $error @var string $brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Enter code — <?= $this->e($brand) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<style>body{background:#0d131f;color:#dde2f3;font-family:Inter,system-ui,sans-serif}</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-sm">
  <h1 class="text-2xl font-bold text-[#e6c446] mb-1">Check your e-mail</h1>
  <p class="text-[#8f9097] text-sm mb-8">We sent a 6-digit code to <span class="text-[#dde2f3]"><?= $this->e($email) ?></span>.</p>
  <?php if ($error): ?>
    <div class="mb-4 rounded border border-[#93000a] bg-[#93000a]/20 px-3 py-2 text-sm text-[#ffb4ab]"><?= $this->e($error) ?></div>
  <?php endif; ?>
  <form method="post" action="/admin/verify" class="space-y-4">
    <div>
      <label class="block text-sm mb-1">Code</label>
      <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus class="w-full rounded bg-[#1a202c] border border-[#45474c] px-3 py-2 tracking-[0.5em] text-center text-lg focus:border-[#e6c446] outline-none" placeholder="000000">
    </div>
    <button class="w-full rounded bg-[#e6c446] text-[#3b2f00] font-bold px-4 py-2 hover:opacity-90">Verify &amp; sign in</button>
  </form>
  <form method="post" action="/admin/login" class="mt-4 text-center">
    <button class="text-sm text-[#8f9097] hover:underline">Use a different e-mail</button>
  </form>
</div>
</body>
</html>
