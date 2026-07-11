<?php /** @var array $team @var array $members @var string $action @var string $heading */ ?>
<div class="flex items-center gap-4 mb-gutter">
  <a href="/admin/teams" class="text-on-surface-variant hover:text-secondary transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <h2 class="font-headline-xl text-headline-xl"><?= $this->e($heading) ?></h2>
</div>

<form method="post" action="<?= $this->e($action) ?>" class="space-y-6 max-w-2xl">
  <!-- Team Settings -->
  <div class="bg-surface-container border border-outline-variant p-6">
    <h3 class="font-headline-lg text-headline-lg mb-4">Team Settings</h3>
    <div>
      <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">TEAM NAME</label>
      <input type="text" name="name" value="<?= $this->e($team['name'] ?? '') ?>" required
        class="w-full bg-surface-container-low border border-outline-variant rounded px-4 py-3 text-on-surface focus:border-secondary outline-none transition-colors">
    </div>
  </div>

  <!-- Members -->
  <?php if (!empty($members)): ?>
    <div class="bg-surface-container border border-outline-variant p-6">
      <h3 class="font-headline-lg text-headline-lg mb-4">Members</h3>
      <table class="w-full text-body-md">
        <thead class="bg-surface-container-high">
          <tr>
            <th class="px-4 py-2 text-left font-label-caps text-label-caps text-on-surface-variant">Name</th>
            <th class="px-4 py-2 text-left font-label-caps text-label-caps text-on-surface-variant">Email</th>
            <th class="px-4 py-2 text-left font-label-caps text-label-caps text-on-surface-variant">Role</th>
            <th class="px-4 py-2 text-right font-label-caps text-label-caps text-on-surface-variant"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $m): ?>
            <tr class="border-t border-outline-variant">
              <td class="px-4 py-2"><?= $this->e($m['name'] ?? 'Unknown') ?></td>
              <td class="px-4 py-2 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($m['email'] ?? '-') ?></td>
              <td class="px-4 py-2 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($m['role'] ?? '') ?></td>
              <td class="px-4 py-2 text-right">
                <form method="post" action="/admin/teams/<?= $this->e($team['id']) ?>/members/<?= $this->e($m['id']) ?>/remove" class="inline">
                  <button type="submit" class="text-error font-label-caps text-label-caps hover:underline">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- Add Member -->
  <div class="bg-surface-container border border-outline-variant p-6">
    <h3 class="font-headline-lg text-headline-lg mb-4">Add Member</h3>
    <div class="flex gap-3">
      <input type="email" name="email" placeholder="user@example.com" required
        class="flex-1 bg-surface-container-low border border-outline-variant rounded px-4 py-2 text-on-surface text-sm focus:border-secondary outline-none transition-colors">
      <select name="role" class="bg-surface-container-low border border-outline-variant rounded px-4 py-2 text-on-surface text-sm focus:border-secondary outline-none transition-colors">
        <option value="member">Member</option>
        <option value="admin">Admin</option>
        <option value="owner">Owner</option>
      </select>
      <button type="submit" formaction="/admin/teams/<?= $this->e($team['id'] ?? '0') ?>/members" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-2 px-4 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">Add</button>
    </div>
  </div>

  <div class="flex gap-3">
    <button type="submit" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-8 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">SAVE TEAM</button>
    <a href="/admin/teams" class="border border-outline-variant font-label-caps text-label-caps py-3 px-8 hover:bg-surface-container-high transition-colors">CANCEL</a>
  </div>
</form>
