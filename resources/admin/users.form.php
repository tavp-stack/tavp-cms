<?php /** @var array $user @var array $roles @var string $action @var string $heading @var bool $isEdit */ ?>
<div class="flex items-center gap-4 mb-gutter">
  <a href="/admin/users" class="text-on-surface-variant hover:text-secondary transition-colors">
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
      <input type="email" value="<?= $this->e($user['email'] ?? '') ?>" disabled
        class="w-full bg-surface-container-high border border-outline-variant rounded px-4 py-3 text-on-surface-variant font-code-sm text-code-sm cursor-not-allowed">
      <p class="text-xs text-on-surface-variant mt-1">Email cannot be changed after creation.</p>
    <?php else: ?>
      <input type="email" name="email" required value="<?= $this->e($__old['email'] ?? '') ?>" placeholder="user@example.com"
        class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-code-sm text-code-sm">
    <?php endif; ?>
  </div>

  <div>
    <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Role <span class="text-error">*</span></label>
    <?php $currentRole = $__old['role'] ?? $user['role'] ?? 'editor'; ?>
    <select name="role" class="w-full bg-surface-container border border-outline-variant rounded px-4 py-3 focus:border-secondary outline-none font-body-md">
      <?php foreach ($roles as $r): ?>
        <option value="<?= $this->e($r) ?>"<?= $currentRole === $r ? ' selected' : '' ?>><?= $this->e(ucfirst($r)) ?></option>
      <?php endforeach; ?>
    </select>
    <p class="text-xs text-on-surface-variant mt-1">Admins can manage everything; editors manage content, media and taxonomy.</p>
  </div>

  <div class="bg-surface-container-low border border-outline-variant rounded p-4 flex items-start gap-3">
    <span class="material-symbols-outlined text-secondary text-xl">info</span>
    <p class="text-sm text-on-surface-variant">This user signs in with a one-time code sent to their e-mail. No password is needed.</p>
  </div>

  <div class="flex gap-4 pt-2">
    <button class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE</button>
    <a href="/admin/users" class="border border-outline-variant font-label-caps text-label-caps py-3 px-8 hover:bg-surface-container-high transition-colors">CANCEL</a>
  </div>
</form>
