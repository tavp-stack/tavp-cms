<?php /** @var array $menu @var array $items @var string $action @var string $heading */ ?>
<div class="flex items-center gap-4 mb-gutter">
  <a href="<?= $adminPrefix ?>/menus" class="text-on-surface-variant hover:text-secondary transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <h2 class="font-headline-xl text-headline-xl"><?= $this->e($heading) ?></h2>
</div>

<form method="post" action="<?= $this->e($action) ?>" class="space-y-6 max-w-2xl">
  <!-- Menu Settings -->
  <div class="bg-surface-container border border-outline-variant p-6">
    <h3 class="font-headline-lg text-headline-lg mb-4">Menu Settings</h3>
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">MENU NAME</label>
      <input type="text" name="name" value="<?= $this->e($menu['name'] ?? '') ?>" required
        class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors">
    </div>
  </div>

  <!-- Menu Items -->
  <?php if (!empty($items)): ?>
    <div class="bg-surface-container border border-outline-variant p-6">
      <h3 class="font-headline-lg text-headline-lg mb-4">Menu Items</h3>
      <div class="space-y-3">
        <?php foreach ($items as $item): ?>
          <div class="flex items-center gap-3 bg-surface-container-low border border-outline-variant rounded p-3">
            <span class="font-body-md text-on-surface flex-1"><?= $this->e($item['label'] ?? '') ?></span>
            <span class="font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($item['url'] ?? '') ?></span>
            <form method="post" action="<?= $adminPrefix ?>/menus/<?= $this->e($menu['id'] ?? '0') ?>/items/<?= $this->e($item['id']) ?>/delete" class="inline">
              <button type="submit" class="text-error font-label-caps text-label-caps hover:underline">Remove</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Add Item -->
  <div class="bg-surface-container border border-outline-variant p-6">
    <h3 class="font-headline-lg text-headline-lg mb-4">Add Item</h3>
    <div class="flex gap-3">
      <input type="text" name="label" placeholder="Label" required
        class="flex-1 bg-surface-container-low border border-outline-variant rounded px-4 py-2 text-on-surface text-sm focus:border-secondary outline-none transition-colors">
      <input type="text" name="url" placeholder="/path" required
        class="flex-1 bg-surface-container-low border border-outline-variant rounded px-4 py-2 text-on-surface text-sm focus:border-secondary outline-none transition-colors">
      <button type="submit" formaction="<?= $adminPrefix ?>/menus/<?= $this->e($menu['id'] ?? '0') ?>/items" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-2 px-4 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">Add</button>
    </div>
  </div>

  <div class="flex gap-3">
    <button type="submit" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE MENU</button>
    <a href="<?= $adminPrefix ?>/menus" class="border border-outline-variant font-label-caps text-label-caps py-3 px-8 hover:bg-surface-container-high transition-colors">CANCEL</a>
  </div>
</form>
