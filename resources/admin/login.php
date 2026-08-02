<?php /** @var string|null $error @var string $brand @var string $adminPrefix */ ?>
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
<style>
body{background:#0d131f;color:#dde2f3;font-family:'Inter',sans-serif}
.captcha-dark .tavp-captcha{background:#1a202c!important;border-color:#45474c!important;box-shadow:none!important}
.captcha-dark .tavp-captcha label{color:#dde2f3!important}
.captcha-dark .tavp-captcha input[type="radio"]+label{background:#161c27!important;color:#dde2f3!important;border-color:#45474c!important}
.captcha-dark .tavp-captcha input[type="radio"]:checked+label{background:#e6c446!important;color:#3b2f00!important;border-color:#e6c446!important}
.captcha-dark .tavp-captcha input[type="number"],.captcha-dark .tavp-captcha input[type="text"]{background:#161c27!important;color:#dde2f3!important;border-color:#45474c!important}
.captcha-dark .tavp-captcha input[type="range"]{accent-color:#e6c446!important}
.captcha-dark .tavp-captcha .material-symbols-outlined{color:#e6c446!important}
.captcha-dark .tavp-captcha .text-gray-400{color:#8f9097!important}
@keyframes spin{to{transform:rotate(360deg)}}
.animate-spin-custom{animation:spin 1s linear infinite}
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-sm"
     x-data="{
       step: 'email',
       email: '',
       code: '',
       error: '',
       loading: false,
       adminPrefix: '<?= $this->e($adminPrefix) ?>',

       async sendOtp() {
         this.error = '';
         this.loading = true;
         this.step = 'loading';

         const form = document.getElementById('login-form');
         const formData = new FormData(form);

         try {
           const resp = await fetch(this.adminPrefix + '/login', {
             method: 'POST',
             headers: {
               'X-Requested-With': 'XMLHttpRequest',
               'Accept': 'application/json'
             },
             body: formData
           });
           const data = await resp.json();

           await new Promise(r => setTimeout(r, 1500));

           if (data.success) {
             this.step = 'verify';
           } else {
             this.error = data.message || 'Gagal mengirim kode.';
             this.step = 'email';
           }
         } catch (e) {
           this.error = 'Terjadi kesalahan. Coba lagi.';
           this.step = 'email';
         } finally {
           this.loading = false;
         }
       },

       async verifyOtp() {
         this.error = '';
         this.loading = true;

         try {
           const resp = await fetch(this.adminPrefix + '/verify', {
             method: 'POST',
             headers: {
               'Content-Type': 'application/x-www-form-urlencoded',
               'X-Requested-With': 'XMLHttpRequest',
               'Accept': 'application/json'
             },
             body: 'code=' + encodeURIComponent(this.code)
           });

           if (resp.redirected) {
             window.location.href = resp.url;
             return;
           }

           const data = await resp.json();
           if (data.success) {
             window.location.href = this.adminPrefix;
           } else {
             this.error = data.message || 'Kode salah.';
           }
         } catch (e) {
           this.error = 'Terjadi kesalahan. Coba lagi.';
         } finally {
           this.loading = false;
         }
       },

       backToEmail() {
         this.step = 'email';
         this.code = '';
         this.error = '';
       }
     }">

  <!-- Header -->
  <h1 class="font-headline-lg text-headline-lg text-secondary mb-1"><?= $this->e($brand) ?> <span class="text-on-surface-variant font-normal text-body-md opacity-60">admin</span></h1>

  <!-- Step 1: Email + Captcha -->
  <div x-show="step === 'email'" x-transition>
    <p class="font-body-md text-body-md text-on-surface-variant mb-8">Sign in with a one-time code sent to your e-mail.</p>
    <div x-show="error" class="mb-4 rounded border border-error bg-error/10 px-3 py-2 text-sm text-error" x-text="error"></div>
    <form id="login-form" @submit.prevent="sendOtp()" class="space-y-4">
      <div>
        <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">E-MAIL</label>
        <input name="email" type="email" required x-model="email" class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none transition-colors font-body-md" placeholder="you@example.com">
      </div>
      <div class="captcha-dark">
        <?= captcha() ?>
      </div>
      <button type="submit" :disabled="loading" class="w-full bg-secondary text-on-secondary font-label-caps text-label-caps py-3 rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all disabled:opacity-50">
        SEND CODE
      </button>
    </form>
  </div>

  <!-- Step 2: Loading -->
  <div x-show="step === 'loading'" x-cloak class="text-center py-12">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-surface-container-high mb-6">
      <span class="material-symbols-outlined text-3xl text-secondary animate-spin-custom">progress_activity</span>
    </div>
    <p class="font-body-md text-on-surface-variant">Mengirim kode verifikasi...</p>
    <p class="font-code-sm text-on-surface-variant/60 mt-2 text-sm" x-text="email"></p>
  </div>

  <!-- Step 3: Verify OTP -->
  <div x-show="step === 'verify'" x-cloak x-transition>
    <p class="font-body-md text-body-md text-on-surface-variant mb-2">Masukkan kode 6 digit yang dikirim ke</p>
    <p class="font-code-sm text-secondary mb-6" x-text="email"></p>
    <div x-show="error" class="mb-4 rounded border border-error bg-error/10 px-3 py-2 text-sm text-error" x-text="error"></div>
    <form @submit.prevent="verifyOtp()" class="space-y-4">
      <div>
        <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">KODE VERIFIKASI</label>
        <input name="code" type="text" inputmode="numeric" maxlength="6" required x-model="code" autofocus
          class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none transition-colors font-body-md text-center tracking-[0.5em] text-xl"
          placeholder="000000">
      </div>
      <button type="submit" :disabled="loading || code.length < 6" class="w-full bg-secondary text-on-secondary font-label-caps text-label-caps py-3 rounded hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all disabled:opacity-50">
        <span x-show="!loading">VERIFY</span>
        <span x-show="loading" class="inline-flex items-center gap-2">
          <span class="material-symbols-outlined text-sm animate-spin-custom">progress_activity</span>
          Memverifikasi...
        </span>
      </button>
    </form>
    <button @click="backToEmail()" class="mt-4 w-full text-center font-body-md text-on-surface-variant hover:text-secondary transition-colors text-sm">
      ← Gunakan email lain
    </button>
  </div>
</div>
</body>
</html>
