<?php
/** @var array $users @var string $currentEmail */
$flashSuccess = $_SESSION['cms_flash']['success'] ?? null;
$flashError = $_SESSION['cms_flash']['error'] ?? null;
unset($_SESSION['cms_flash']);
?>

<div class="flex justify-between items-center mb-8">
  <div>
    <h1 class="font-headline-xl text-headline-xl text-secondary">Users</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Manage who can sign in and what they can do</p>
  </div>
  <a href="/admin/users/create" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-3 px-6 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">+ NEW USER</a>
</div>

<?php if ($flashSuccess): ?>
  <div class="mb-6 bg-secondary/10 border border-secondary/30 text-secondary p-4 rounded"><?= $this->e($flashSuccess) ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
  <div class="mb-6 bg-error-container/20 border border-error/30 text-error p-4 rounded"><?= $this->e($flashError) ?></div>
<?php endif; ?>

<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high">
      <tr>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">User</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Email</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Role</th>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Joined</th>
        <th class="px-4 py-3 text-right font-label-caps text-label-caps text-on-surface-variant">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($users)): ?>
        <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No users yet.</td></tr>
      <?php else: foreach ($users as $user): ?>
        <?php
          $role = $user['role'] ?? 'editor';
          if (empty($user['role']) && $__rbac !== null) {
            $role = $__rbac->role($user['email']);
          }
          $isSelf = strtolower((string) $user['email']) === $currentEmail;
        ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center">
                <span class="material-symbols-outlined text-sm text-secondary">person</span>
              </div>
              <span class="font-body-md"><?= $this->e($user['name'] ?? 'Unknown') ?><?= $isSelf ? ' <span class="text-xs text-on-surface-variant">(you)</span>' : '' ?></span>
            </div>
          </td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($user['email']) ?></td>
          <td class="px-4 py-3">
            <span class="font-code-sm text-code-sm px-2 py-1 rounded <?= $role === 'admin' ? 'bg-secondary/20 text-secondary' : 'bg-surface-container-high text-on-surface-variant' ?>">
              <?= $this->e($role) ?>
            </span>
          </td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($user['created_at'] ?? '') ?></td>
          <td class="px-4 py-3 text-right whitespace-nowrap">
            <a href="/admin/users/<?= $this->e($user['id']) ?>/edit" class="text-secondary font-label-caps text-label-caps hover:underline">Edit</a>
            <?php if (!$isSelf): ?>
              <form method="post" action="/admin/users/<?= $this->e($user['id']) ?>/delete" class="inline ml-3" onsubmit="return confirm('Delete this user?');">
                <button type="submit" class="text-error font-label-caps text-label-caps hover:underline">Delete</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
