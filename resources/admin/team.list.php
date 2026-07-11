<?php
/** @var array $teams */

$db = app('db');
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);
?>

<div class="flex justify-between items-center mb-8">
  <div>
    <h1 class="font-headline-xl text-headline-xl text-secondary">Users</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Manage registered accounts</p>
  </div>
</div>

<!-- Users List -->
<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high">
      <tr>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">User</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Email</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Role</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Joined</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($users)): ?>
        <tr><td colspan="4" class="px-4 py-8 text-center text-on-surface-variant">No users yet.</td></tr>
      <?php else: foreach ($users as $user): ?>
        <?php
          $role = 'editor';
          if ($__rbac !== null) {
            $role = $__rbac->role($user['email']);
          }
        ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center">
                <span class="material-symbols-outlined text-sm text-secondary">person</span>
              </div>
              <span class="font-body-md"><?= $this->e($user['name'] ?? 'Unknown') ?></span>
            </div>
          </td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($user['email']) ?></td>
          <td class="px-4 py-3">
            <span class="font-code-sm text-code-sm px-2 py-1 rounded <?= $role === 'admin' ? 'bg-secondary/20 text-secondary' : 'bg-surface-container-high text-on-surface-variant' ?>">
              <?= $this->e($role) ?>
            </span>
          </td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($user['created_at'] ?? '') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>