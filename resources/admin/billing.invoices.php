<?php /** @var array $invoices */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Invoices</h2>
  <a href="<?= $adminPrefix ?>/billing" class="text-secondary font-label-caps text-label-caps hover:underline">&larr; Back to Billing</a>
</div>

<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high">
      <tr>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">User</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Amount</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Status</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Gateway</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($invoices)): ?>
        <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No invoices yet.</td></tr>
      <?php else: foreach ($invoices as $inv): ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <td class="px-4 py-3"><?= $this->e($inv['name'] ?? '-') ?> (<?= $this->e($inv['email'] ?? '-') ?>)</td>
          <td class="px-4 py-3 text-secondary font-code-sm text-code-sm">$<?= $this->e($inv['amount'] ?? '0') ?> <?= $this->e($inv['currency'] ?? '') ?></td>
          <td class="px-4 py-3">
            <span class="font-label-caps text-label-caps px-2 py-1 rounded-full <?= ($inv['status'] ?? '') === 'paid' ? 'bg-secondary/20 text-secondary' : 'bg-yellow-900/30 text-yellow-400' ?>"><?= $this->e($inv['status'] ?? '') ?></span>
          </td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($inv['gateway'] ?? '') ?></td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($inv['created_at'] ?? '') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
