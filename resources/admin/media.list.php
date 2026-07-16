<?php /** @var array $media */ ?>
<?php $__flashSuccess = $_SESSION['cms_flash']['success'] ?? null; $__flashError = $_SESSION['cms_flash']['error'] ?? null; unset($_SESSION['cms_flash']); ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Media</h2>
</div>

<?php if ($__flashSuccess): ?>
  <div class="mb-6 bg-secondary-container/20 border border-secondary/30 p-4 rounded flex items-center gap-2">
    <span class="material-symbols-outlined text-secondary">check_circle</span>
    <span class="text-body-md"><?= $this->e($__flashSuccess) ?></span>
  </div>
<?php endif; ?>
<?php if ($__flashError): ?>
  <div class="mb-6 bg-error-container/20 border border-error/30 p-4 rounded flex items-center gap-2">
    <span class="material-symbols-outlined text-error">error</span>
    <span class="text-body-md"><?= $this->e($__flashError) ?></span>
  </div>
<?php endif; ?>

<!-- Upload Form -->
<div class="bg-surface-container border border-outline-variant p-6 mb-gutter">
  <h3 class="font-headline-lg text-headline-lg mb-4">Upload File</h3>
  <form method="post" action="<?= $adminPrefix ?>/media/upload" enctype="multipart/form-data" class="flex gap-3">
    <input type="file" name="file" required class="flex-1 bg-surface-container-low border border-outline-variant rounded px-4 py-2 text-on-surface text-sm focus:border-secondary outline-none transition-colors">
    <button type="submit" class="bg-secondary text-on-secondary font-label-caps text-label-caps py-2 px-6 hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">Upload</button>
  </form>
</div>

<!-- Media List -->
<div class="bg-surface-container border border-outline-variant overflow-hidden">
  <table class="w-full text-body-md">
    <thead class="bg-surface-container-high">
      <tr>
        <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Preview</th>
        <?php
        $mediaSortCols = [
          'name' => 'Name',
          'mime_type' => 'Type',
          'size' => 'Size',
        ];
        foreach ($mediaSortCols as $col => $colLabel):
          $isActive = ($_GET['sort'] ?? 'name') === $col;
          $nextDir = ($isActive && ($_GET['dir'] ?? 'ASC') === 'ASC') ? 'DESC' : 'ASC';
          $arrow = $isActive ? (($_GET['dir'] ?? 'ASC') === 'ASC' ? ' ↑' : ' ↓') : '';
        ?>
          <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">
            <a href="?sort=<?= $col ?>&dir=<?= $nextDir ?>" class="hover:text-secondary transition-colors"><?= $this->e($colLabel) ?><?= $arrow ?></a>
          </th>
        <?php endforeach; ?>
        <th class="px-4 py-3 text-right font-label-caps text-label-caps text-on-surface-variant">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($media)): ?>
        <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No media files yet.</td></tr>
      <?php else: foreach ($media as $file): ?>
        <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
          <td class="px-4 py-3">
            <?php if (str_starts_with((string) ($file['mime_type'] ?? ''), 'image/')): ?>
              <img src="/uploads/<?= $this->e($file['path'] ?? $file['file_name'] ?? '') ?>" alt="" class="w-10 h-10 object-cover rounded">
            <?php else: ?>
              <span class="material-symbols-outlined text-secondary">insert_drive_file</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3"><?= $this->e($file['name'] ?? '') ?></td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($file['mime_type'] ?? '') ?></td>
          <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e(round(($file['size'] ?? 0) / 1024)) ?> KB</td>
          <td class="px-4 py-3 text-right">
            <form method="post" action="<?= $adminPrefix ?>/media/<?= $this->e($file['id']) ?>/delete" class="inline">
              <button type="submit" class="text-error font-label-caps text-label-caps hover:underline" onclick="return confirm('Delete file?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
