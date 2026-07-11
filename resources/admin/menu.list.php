<?php /** @var array $menus */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Menus</h2>
  <a href="/admin/menus/create" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-6 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">+ NEW MENU</a>
</div>

<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high">
      <tr>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Name</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Location</th>
        <th class="px-4 py-3 text-right font-label-caps text-label-caps text-on-surface-variant">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($menus)): ?>
        <tr><td colspan="3" class="px-4 py-8 text-center text-on-surface-variant">No menus yet.</td></tr>
      <?php else: foreach ($menus as $menu): ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <td class="px-4 py-3"><?= $this->e($menu['name'] ?? '') ?></td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($menu['location'] ?? '-') ?></td>
          <td class="px-4 py-3 text-right">
            <a href="/admin/menus/<?= $this->e($menu['id']) ?>/edit" class="text-secondary font-label-caps text-label-caps hover:underline mr-3">Edit</a>
            <form method="post" action="/admin/menus/<?= $this->e($menu['id']) ?>/delete" class="inline">
              <button type="submit" class="text-error font-label-caps text-label-caps hover:underline" onclick="return confirm('Delete menu?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
