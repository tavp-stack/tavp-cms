<?php
/** @var array $__counts */
/** @var array $trafficData */
/** @var array $topPages */
/** @var array $devices */
/** @var array $browsers */
/** @var array $countries */
/** @var array $recentSessions */
/** @var int $pageviewsToday */
/** @var int $pageviewsYesterday */
/** @var int $uniqueVisitors */
/** @var int $realtime */
/** @var int $fraudEvents */
/** @var int $totalPageviews */
/** @var int $totalVisitors */
/** @var int $avgDaily */
/** @var int $change */
/** @var string $period */
/** @var bool $isCustom */
/** @var string|null $customFrom */
/** @var string|null $customTo */
/** @var int $periodDays */
?>

<div class="flex justify-between items-center mb-8">
  <div>
    <h1 class="font-headline-xl text-headline-xl text-secondary">Analytics</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Traffic insights and user behavior</p>
  </div>
  <div class="flex flex-col items-end gap-3">
    <div class="flex items-center gap-2">
      <a href="/admin/analytics?period=7d" class="px-4 py-2 rounded font-label-caps text-label-caps transition-colors <?= (!$isCustom && $period === '7d') ? 'bg-secondary text-on-secondary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">7 Days</a>
      <a href="/admin/analytics?period=30d" class="px-4 py-2 rounded font-label-caps text-label-caps transition-colors <?= (!$isCustom && $period === '30d') ? 'bg-secondary text-on-secondary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">30 Days</a>
      <a href="/admin/analytics?period=90d" class="px-4 py-2 rounded font-label-caps text-label-caps transition-colors <?= (!$isCustom && $period === '90d') ? 'bg-secondary text-on-secondary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">90 Days</a>
    </div>
    <form method="get" action="/admin/analytics" class="flex items-center gap-2">
      <input type="date" name="from" value="<?= $this->e($customFrom ?? '') ?>" max="<?= $today ?>"
        class="bg-surface-container border <?= $isCustom ? 'border-secondary' : 'border-outline-variant' ?> rounded px-3 py-2 font-code-sm text-code-sm text-on-surface outline-none focus:border-secondary">
      <span class="text-on-surface-variant text-sm">to</span>
      <input type="date" name="to" value="<?= $this->e($customTo ?? '') ?>" max="<?= $today ?>"
        class="bg-surface-container border <?= $isCustom ? 'border-secondary' : 'border-outline-variant' ?> rounded px-3 py-2 font-code-sm text-code-sm text-on-surface outline-none focus:border-secondary">
      <button class="px-4 py-2 rounded font-label-caps text-label-caps bg-secondary text-on-secondary hover:brightness-110 transition-all">APPLY</button>
      <?php if ($isCustom): ?>
        <a href="/admin/analytics?period=7d" class="px-3 py-2 rounded font-label-caps text-label-caps text-on-surface-variant hover:bg-surface-container-high transition-colors" title="Clear custom range">CLEAR</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- Metrics Cards -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">TODAY</p>
    <div class="flex items-end gap-3">
      <h3 class="font-headline-xl text-headline-xl"><?= (int) $pageviewsToday ?></h3>
      <?php if ($change > 0): ?>
        <span class="text-secondary font-code-sm text-code-sm mb-1">+<?= $change ?>%</span>
      <?php elseif ($change < 0): ?>
        <span class="text-error font-code-sm text-code-sm mb-1"><?= $change ?>%</span>
      <?php endif; ?>
    </div>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">pageviews</p>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">PERIOD TOTAL</p>
    <h3 class="font-headline-xl text-headline-xl"><?= (int) $totalPageviews ?></h3>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">pageviews in <?= $periodDays ?> days</p>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">UNIQUE VISITORS</p>
    <h3 class="font-headline-xl text-headline-xl"><?= (int) $totalVisitors ?></h3>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">in <?= $periodDays ?> days</p>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">REAL-TIME</p>
    <h3 class="font-headline-xl text-headline-xl text-secondary"><?= (int) $realtime ?></h3>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">active now</p>
  </div>
</section>

<!-- Traffic Chart -->
<section class="bg-surface-container p-6 border border-outline-variant mb-8">
  <div class="flex justify-between items-center mb-6">
    <h3 class="font-headline-lg text-headline-lg text-secondary">Traffic Trend</h3>
    <span class="font-code-sm text-code-sm text-on-surface-variant">Avg: <?= (int) $avgDaily ?>/day</span>
  </div>
  <?php if (!empty($trafficData)): ?>
    <?php
      // Prepare data for Chart.js
      $labels = array_column($trafficData, 'day');
      $data = array_column($trafficData, 'cnt');

      // Create LineChart instance
      $chart = new \Tavp\Blocks\Components\LineChart('Daily Traffic');
      $chart->setLabels($labels);
      $chart->addDataset('Pageviews', $data, [
        'borderColor' => '#6750A4',
        'backgroundColor' => 'rgba(103, 80, 164, 0.1)',
        'tension' => 0.4,
        'fill' => true
      ]);
      $chart->setSize(800, 300);
      echo $chart->render();
    ?>
  <?php else: ?>
    <div class="flex items-center justify-center h-48 text-on-surface-variant">
      <p class="font-body-md">No traffic data yet for this period</p>
    </div>
  <?php endif; ?>
</section>

<!-- Top Pages + Devices -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
  <!-- Top Pages -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Top Pages</h3>
    <?php if (!empty($topPages)): ?>
      <div class="space-y-3">
        <?php foreach ($topPages as $i => $page): ?>
          <div class="flex items-center gap-3 p-3 bg-surface-container-low rounded hover:bg-surface-container-high transition-colors">
            <span class="font-label-caps text-label-caps text-on-surface-variant w-6"><?= $i + 1 ?></span>
            <div class="flex-1 min-w-0">
              <p class="font-body-md text-body-md truncate"><?= $this->e($page['path']) ?></p>
            </div>
            <span class="font-code-sm text-code-sm text-secondary"><?= (int) $page['views'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant text-center py-8">No page data yet</p>
    <?php endif; ?>
  </div>

  <!-- Devices + Browsers -->
  <div class="space-y-4">
    <div class="bg-surface-container p-6 border border-outline-variant">
      <h3 class="font-headline-lg text-headline-lg text-secondary mb-4">Devices</h3>
      <?php if (!empty($devices)): ?>
        <div class="space-y-3">
          <?php
            $totalDevices = array_sum(array_column($devices, 'cnt'));
            foreach ($devices as $device):
              $pct = $totalDevices > 0 ? round($device['cnt'] / $totalDevices * 100) : 0;
          ?>
            <div>
              <div class="flex justify-between mb-1">
                <span class="font-body-md text-on-surface"><?= $this->e(ucfirst($device['device'] ?? 'Unknown')) ?></span>
                <span class="font-code-sm text-code-sm text-secondary"><?= $pct ?>%</span>
              </div>
              <div class="w-full bg-primary-container h-3 rounded-full overflow-hidden">
                <div class="bg-secondary h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-on-surface-variant text-center py-4">No device data yet</p>
      <?php endif; ?>
    </div>

    <div class="bg-surface-container p-6 border border-outline-variant">
      <h3 class="font-headline-lg text-headline-lg text-secondary mb-4">Browsers</h3>
      <?php if (!empty($browsers)): ?>
        <div class="space-y-3">
          <?php
            $totalBrowsers = array_sum(array_column($browsers, 'cnt'));
            foreach ($browsers as $browser):
              $pct = $totalBrowsers > 0 ? round($browser['cnt'] / $totalBrowsers * 100) : 0;
          ?>
            <div>
              <div class="flex justify-between mb-1">
                <span class="font-body-md text-on-surface"><?= $this->e($browser['browser'] ?? 'Unknown') ?></span>
                <span class="font-code-sm text-code-sm text-secondary"><?= $pct ?>%</span>
              </div>
              <div class="w-full bg-primary-container h-3 rounded-full overflow-hidden">
                <div class="bg-secondary h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-on-surface-variant text-center py-4">No browser data yet</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Geographic + Sessions -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
  <!-- Geographic Data -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Top Countries</h3>
    <?php if (!empty($countries)): ?>
      <div class="space-y-3">
        <?php
          $totalCountries = array_sum(array_column($countries, 'cnt'));
          foreach ($countries as $country):
            $pct = $totalCountries > 0 ? round($country['cnt'] / $totalCountries * 100) : 0;
        ?>
          <div class="flex items-center gap-3 p-3 bg-surface-container-low rounded">
            <div class="flex-1">
              <p class="font-body-md text-on-surface"><?= $this->e($country['country'] ?? 'Unknown') ?></p>
            </div>
            <div class="w-32">
              <div class="w-full bg-primary-container h-2 rounded-full overflow-hidden">
                <div class="bg-secondary h-full rounded-full" style="width: <?= $pct ?>%"></div>
              </div>
            </div>
            <span class="font-code-sm text-code-sm text-secondary w-16 text-right"><?= (int) $country['cnt'] ?> (<?= $pct ?>%)</span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant text-center py-8">No geographic data yet</p>
    <?php endif; ?>
  </div>

  <!-- Recent Sessions -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Recent Sessions</h3>
    <?php if (!empty($recentSessions)): ?>
      <div class="space-y-3">
        <?php foreach ($recentSessions as $session): ?>
          <div class="flex items-center gap-3 p-3 bg-surface-container-low rounded">
            <span class="material-symbols-outlined text-secondary">person</span>
            <div class="flex-1 min-w-0">
              <p class="font-code-sm text-code-sm text-on-surface truncate"><?= $this->e($session['ip_address'] ?? 'Unknown') ?></p>
              <p class="font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($session['device'] ?? '') ?> · <?= $this->e($session['browser'] ?? '') ?></p>
            </div>
            <div class="text-right">
              <p class="font-code-sm text-code-sm text-secondary"><?= (int) ($session['page_views'] ?? 0) ?> pages</p>
              <p class="font-code-sm text-code-sm text-on-surface-variant text-[10px]"><?= $this->e($session['last_activity_at'] ?? '') ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant text-center py-8">No session data yet</p>
    <?php endif; ?>
  </div>
</section>