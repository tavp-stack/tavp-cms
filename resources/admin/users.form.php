<?php /** @var array $user @var array $roles @var string $action @var string $heading @var bool $isEdit */ ?>
<div class="flex items-center gap-4 mb-gutter">
  <a href="<?= $adminPrefix ?>/users" class="text-on-surface-variant hover:text-secondary transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <h2 class="font-headline-xl text-headline-xl"><?= $this->e($heading) ?></h2>
</div>

<?php if (!empty($__errors)): ?>
  <div class="mb-6 bg-error-container/20 border border-error/30 p-4 rounded max-w-2xl">
    <ul class="list-disc list-inside text-body-md text-error space-y-1">
      <?php foreach ($__errors as $errs): foreach ($errs as $err): ?>
        <li><?= $this->e($err) ?></li>
      <?php endforeach; endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="<?= $this->e($action) ?>" class="max-w-2xl space-y-6">
  <div>
    <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Name</label>
    <input type="text" name="name" value="<?= $this->e($__old['name'] ?? $user['name'] ?? '') ?>" placeholder="Full name"
      class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-body-md">
  </div>

  <div>
    <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Email <span class="text-error">*</span></label>
    <?php if ($isEdit): ?>
      <input type="email" name="email" value="<?= $this->e($__old['email'] ?? $user['email'] ?? '') ?>" <?= ($isAdmin ?? false) ? '' : 'readonly' ?>
        class="w-full bg-surface-container-high border border-outline-variant rounded px-4 py-3 text-on-surface-variant font-code-sm text-code-sm <?= ($isAdmin ?? false) ? '' : 'cursor-not-allowed' ?>">
      <?php if (!($isAdmin ?? false)): ?>
        <p class="text-xs text-on-surface-variant mt-1">Only admins can change the email address.</p>
      <?php endif; ?>
    <?php else: ?>
      <input type="email" name="email" required value="<?= $this->e($__old['email'] ?? '') ?>" placeholder="user@example.com"
        class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm">
    <?php endif; ?>
  </div>

  <div>
    <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Role <span class="text-error">*</span></label>
    <?php $currentRole = $__old['role'] ?? $user['role'] ?? 'editor'; ?>
    <div x-data="{ open: false, q: '', roles: <?= htmlspecialchars(json_encode($roles), ENT_QUOTES) ?>, selected: '<?= $currentRole ?>' }" class="relative">
      <input type="hidden" name="role" :value="selected">
      <button type="button" @click="open = !open" @click.outside="open = false" class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 text-left font-body-md focus:border-secondary outline-none flex items-center justify-between">
        <span x-text="selected ? selected.charAt(0).toUpperCase() + selected.slice(1) : 'Select role...'"></span>
        <span class="material-symbols-outlined text-sm text-on-surface-variant">expand_more</span>
      </button>
      <div x-show="open" x-cloak class="absolute z-10 mt-1 w-full bg-surface-container border border-outline-variant rounded shadow-lg max-h-48 overflow-y-auto">
        <div class="p-2">
          <input type="text" x-model="q" placeholder="Search role..." class="w-full bg-surface-container-low border border-outline-variant rounded px-3 py-2 text-sm outline-none focus:border-secondary">
        </div>
        <template x-for="r in roles.filter(r => r.toLowerCase().includes(q.toLowerCase()))" :key="r">
          <div @click="selected = r; open = false" class="px-4 py-2 hover:bg-surface-container-high cursor-pointer text-sm" :class="selected === r ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface'" x-text="r.charAt(0).toUpperCase() + r.slice(1)"></div>
        </template>
      </div>
    </div>
    <p class="text-xs text-on-surface-variant mt-1">Admins can manage everything; editors manage content, media and taxonomy.</p>
  </div>

  <div>
    <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Bio</label>
    <textarea name="bio" rows="3" placeholder="Short bio about this user..."
      class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-body-md"><?= $this->e($__old['bio'] ?? $user['bio'] ?? '') ?></textarea>
  </div>

  <div class="bg-surface-container border border-outline-variant rounded-xl p-6 space-y-4">
    <h3 class="font-headline-lg text-headline-lg text-on-surface flex items-center gap-2">
      <span class="material-symbols-outlined text-secondary">share</span>
      Social Media
    </h3>
    <p class="text-sm text-on-surface-variant">Social links yang akan ditampilkan di profil publik.</p>

    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">GitHub</label>
      <input type="text" name="social_github" value="<?= $this->e($__old['social_github'] ?? $user['social_github'] ?? '') ?>" placeholder="https://github.com/username"
        class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm">
    </div>
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Twitter / X</label>
      <input type="text" name="social_twitter" value="<?= $this->e($__old['social_twitter'] ?? $user['social_twitter'] ?? '') ?>" placeholder="https://twitter.com/username"
        class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm">
    </div>
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">LinkedIn</label>
      <input type="text" name="social_linkedin" value="<?= $this->e($__old['social_linkedin'] ?? $user['social_linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/username"
        class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm">
    </div>
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Instagram</label>
      <input type="text" name="social_instagram" value="<?= $this->e($__old['social_instagram'] ?? $user['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/username"
        class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm">
    </div>
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Website</label>
      <input type="text" name="social_website" value="<?= $this->e($__old['social_website'] ?? $user['social_website'] ?? '') ?>" placeholder="https://yoursite.com"
        class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm">
    </div>
  </div>

  <div class="bg-surface-container-low border border-outline-variant rounded p-4 flex items-start gap-3">
    <span class="material-symbols-outlined text-secondary text-xl">info</span>
    <p class="text-sm text-on-surface-variant">This user signs in with a one-time code sent to their e-mail. No password is needed.</p>
  </div>

  <div class="flex gap-4 pt-2 sticky bottom-4 bg-background/80 backdrop-blur-md py-4 border-t border-outline-variant -mx-10 px-10">
    <button class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE</button>
    <a href="<?= $adminPrefix ?>/users" class="border border-outline-variant font-label-caps text-label-caps py-3 px-8 hover:bg-surface-container-high transition-colors">CANCEL</a>
  </div>
</form>
