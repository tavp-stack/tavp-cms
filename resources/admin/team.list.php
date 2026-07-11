<?php /** @var array $teams */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Teams</h2>
  <a href="/admin/teams/create" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-6 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">+ NEW TEAM</a>
</div>

<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high">
      <tr>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Name</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Owner ID</th>
        <th class="px-4 py-3 text-right font-label-caps text-label-caps text-on-surface-variant">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($teams)): ?>
        <tr><td colspan="3" class="px-4 py-8 text-center text-on-surface-variant">No teams yet.</td></tr>
      <?php else: foreach ($teams as $team): ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <td class="px-4 py-3"><?= $this->e($team['name'] ?? '') ?></td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($team['owner_id'] ?? '') ?></td>
          <td class="px-4 py-3 text-right">
            <a href="/admin/teams/<?= $this->e($team['id']) ?>/edit" class="text-secondary font-label-caps text-label-caps hover:underline mr-3">Edit</a>
            <form method="post" action="/admin/teams/<?= $this->e($team['id']) ?>/delete" class="inline">
              <button type="submit" class="text-error font-label-caps text-label-caps hover:underline" onclick="return confirm('Delete team?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
