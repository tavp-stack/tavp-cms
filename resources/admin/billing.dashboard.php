<?php /** @var array $subscriptions @var array $stats */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Billing</h2>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-gutter">
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">ACTIVE SUBSCRIPTIONS</p>
    <h3 class="font-headline-xl text-headline-xl text-secondary"><?= (int) ($stats['active'] ?? 0) ?></h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">CANCELLED</p>
    <h3 class="font-headline-xl text-headline-xl"><?= (int) ($stats['cancelled'] ?? 0) ?></h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">TOTAL REVENUE</p>
    <h3 class="font-headline-xl text-headline-xl text-secondary">$<?= number_format((float) ($stats['total_revenue'] ?? 0), 2) ?></h3>
  </div>
</div>

<!-- Subscriptions Table -->
<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <div class="px-6 py-4 border-b border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg">Subscriptions</h3>
  </div>
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high">
      <tr>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">User</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Plan</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Status</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Gateway</th>
        <th class="px-4 py-3 text-right font-label-caps text-label-caps text-on-surface-variant">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($subscriptions)): ?>
        <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No subscriptions yet.</td></tr>
      <?php else: foreach ($subscriptions as $sub): ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <td class="px-4 py-3"><?= $this->e($sub['name'] ?? '-') ?> (<?= $this->e($sub['email'] ?? '-') ?>)</td>
          <td class="px-4 py-3 font-code-sm text-code-sm"><?= $this->e($sub['plan'] ?? '') ?></td>
          <td class="px-4 py-3">
            <span class="font-label-caps text-label-caps px-2 py-1 rounded-full <?= ($sub['status'] ?? '') === 'active' ? 'bg-secondary/20 text-secondary' : 'bg-surface-container-high text-on-surface-variant' ?>"><?= $this->e($sub['status'] ?? '') ?></span>
          </td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($sub['gateway'] ?? '') ?></td>
          <td class="px-4 py-3 text-right">
            <?php if (($sub['status'] ?? '') === 'active'): ?>
              <form method="post" action="<?= $adminPrefix ?>/billing/subscriptions/<?= $this->e($sub['id']) ?>/cancel" class="inline">
                <button type="submit" class="text-error font-label-caps text-label-caps hover:underline" onclick="return confirm('Cancel subscription?')">Cancel</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
