<?php /** @var array $counts @var array $__types */ ?>
<?php
/** @var array $trafficData */
/** @var array $topPages */
/** @var int $pageviewsToday */
/** @var int $pageviewsYesterday */
/** @var int $uniqueVisitors */
/** @var int $realtime */
/** @var int $fraud */
/** @var int $weekViews */
/** @var int $weekVisitors */
/** @var int $change */
?>

<!-- Header -->
<div class="flex justify-between items-center mb-8">
  <div>
    <h1 class="font-headline-xl text-headline-xl text-secondary mb-1">Dashboard</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">Ringkasan trafik situs 7 hari terakhir.</p>
  </div>
  <a href="<?= $adminPrefix ?>/analytics" class="bg-secondary text-on-secondary py-3 px-6 rounded font-label-caps text-label-caps hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all flex items-center gap-2">
    <span class="material-symbols-outlined text-[20px]">analytics</span>
    Analytics Lengkap
  </a>
</div>

<!-- Summary Cards -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">PAGEVIEWS HARI INI</p>
    <div class="flex items-end gap-3">
      <h3 class="font-headline-xl text-headline-xl"><?= (int) $pageviewsToday ?></h3>
      <?php if ($change > 0): ?>
        <span class="text-secondary font-code-sm text-code-sm mb-1">+<?= $change ?>%</span>
      <?php elseif ($change < 0): ?>
        <span class="text-error font-code-sm text-code-sm mb-1"><?= $change ?>%</span>
      <?php endif; ?>
    </div>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">PENGUNJUNG HARI INI</p>
    <h3 class="font-headline-xl text-headline-xl"><?= (int) $uniqueVisitors ?></h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">REAL-TIME</p>
    <h3 class="font-headline-xl text-headline-xl text-secondary"><?= (int) $realtime ?></h3>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">aktif sekarang</p>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">FRAUD EVENTS</p>
    <h3 class="font-headline-xl text-headline-xl text-error"><?= (int) $fraud ?></h3>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">hari ini</p>
  </div>
</section>

<!-- 7-day totals -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">TOTAL PAGEVIEWS (7 HARI)</p>
    <h3 class="font-headline-xl text-headline-xl"><?= (int) $weekViews ?></h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">TOTAL PENGUNJUNG (7 HARI)</p>
    <h3 class="font-headline-xl text-headline-xl"><?= (int) $weekVisitors ?></h3>
  </div>
</section>

  <!-- Traffic Trend + Top Pages -->
  <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-surface-container p-6 border border-outline-variant">
      <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Tren Trafik (7 hari)</h3>
      <?php if (!empty($trafficData)): ?>
        <?php
          // Prepare data for Chart.js Bar Chart
          $labels = array_column($trafficData, 'day');
          $data = array_column($trafficData, 'cnt');

          // Create BarChart instance
          $chart = new \Tavp\Blocks\Components\BarChart('Traffic 7 Hari');
          $chart->setLabels($labels);
          $chart->addDataset('Pageviews', $data, [
            'backgroundColor' => 'rgba(236, 201, 75, 0.5)',
            'borderColor' => 'rgb(236, 201, 75)',
            'borderWidth' => 1,
          ]);
          $chart->setSize(600, 300);
          echo $chart->render();
        ?>
      <?php else: ?>
        <div class="flex items-center justify-center h-48 text-on-surface-variant"><p class="font-body-md">Belum ada data trafik.</p></div>
      <?php endif; ?>
    </div>

  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Halaman Teratas</h3>
    <?php if (!empty($topPages)): ?>
      <div class="space-y-3">
        <?php foreach ($topPages as $i => $page): ?>
          <div class="flex items-center gap-3 p-3 bg-surface-container-low rounded">
            <span class="font-label-caps text-label-caps text-on-surface-variant w-6"><?= $i + 1 ?></span>
            <div class="flex-1 min-w-0"><p class="font-body-md text-body-md truncate"><?= $this->e($page['path']) ?></p></div>
            <span class="font-code-sm text-code-sm text-secondary"><?= (int) $page['views'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant text-center py-8">Belum ada data.</p>
    <?php endif; ?>
  </div>
</section>
