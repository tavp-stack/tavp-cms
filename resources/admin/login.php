<?php /** @var string|null $error @var string $brand */ ?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in — <?= $this->e($brand) ?></title>
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
      fontFamily: { "headline-lg": ["Geist"], "body-md": ["Inter"], "code-sm": ["JetBrains Mono"], "label-caps": ["JetBrains Mono"] },
      fontSize: {
        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}]
      }
    }
  }
}
</script>
<style>body{background:#0d131f;color:#dde2f3;font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-sm">
  <h1 class="font-headline-lg text-headline-lg text-secondary mb-1"><?= $this->e($brand) ?> <span class="text-on-surface-variant font-normal text-body-md opacity-60">admin</span></h1>
  <p class="font-body-md text-body-md text-on-surface-variant mb-8">Sign in with a one-time code sent to your e-mail.</p>
  <?php if ($error): ?>
    <div class="mb-4 rounded border border-error bg-error/10 px-3 py-2 text-sm text-error"><?= $this->e($error) ?></div>
  <?php endif; ?>
  <form method="post" action="<?= $adminPrefix ?>/login" class="space-y-4">
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">E-MAIL</label>
      <input name="email" type="email" required autofocus class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none transition-colors font-body-md" placeholder="you@example.com">
    </div>
    <button class="w-full bg-secondary text-on-secondary font-label-caps text-label-caps py-3 rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SEND CODE</button>
  </form>
</div>
</body>
</html>
