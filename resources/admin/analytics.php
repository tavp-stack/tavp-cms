<?php
/** @var array $__counts */

$db = app('db');
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$weekAgo = date('Y-m-d', strtotime('-7 days'));
$monthAgo = date('Y-m-d', strtotime('-30 days'));

// Period selector — supports preset (7d/30d/90d) or a custom from/to range.
$period = $_GET['period'] ?? '7d';
$customFrom = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from'] : null;
$customTo = isset($_GET['to']) && $_GET['to'] !== '' ? $_GET['to'] : null;

$validDate = static fn ($d) => $d !== null && \DateTime::createFromFormat('Y-m-d', $d) !== false;

if ($validDate($customFrom) && $validDate($customTo)) {
    $isCustom = true;
    $period = 'custom';
    $periodStart = min($customFrom, $customTo);
    $periodEnd = max($customFrom, $customTo);
    $periodDays = max(1, (int) ((strtotime($periodEnd) - strtotime($periodStart)) / 86400) + 1);
} else {
    $isCustom = false;
    $periodDays = match($period) {
        '30d' => 30,
        '90d' => 90,
        default => 7,
    };
    $periodStart = date('Y-m-d', strtotime("-{$periodDays} days"));
    $periodEnd = $today;
}

try {
    // Today's stats
    $pageviewsToday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $pageviewsYesterday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$yesterday])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $uniqueVisitors = $db->query("SELECT COUNT(DISTINCT session_id) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $realtime = $db->query("SELECT COUNT(*) as cnt FROM analytics_sessions WHERE last_activity_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $fraudEvents = $db->query("SELECT COUNT(*) as cnt FROM analytics_fraud_events WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;

    $range = [$periodStart, $periodEnd];
    // Period totals
    $totalPageviews = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ?", $range)->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $totalVisitors = $db->query("SELECT COUNT(DISTINCT session_id) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ?", $range)->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $avgDaily = $periodDays > 0 ? round($totalPageviews / $periodDays) : 0;
    
    // Traffic chart data
    $trafficData = $db->query("SELECT DATE(created_at) as day, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY day ASC", $range)->fetchAll(\PDO::FETCH_ASSOC);
    
    // Top pages
    $topPages = $db->query("SELECT path, COUNT(*) as views FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY path ORDER BY views DESC LIMIT 10", $range)->fetchAll(\PDO::FETCH_ASSOC);
    
    // Device breakdown
    $devices = $db->query("SELECT device, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY device ORDER BY cnt DESC", $range)->fetchAll(\PDO::FETCH_ASSOC);
    
    // Browser breakdown
    $browsers = $db->query("SELECT browser, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY browser ORDER BY cnt DESC", $range)->fetchAll(\PDO::FETCH_ASSOC);
    
    // Geographic data
    $countries = $db->query("SELECT country, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) BETWEEN ? AND ? AND country IS NOT NULL AND country != '' GROUP BY country ORDER BY cnt DESC LIMIT 10", $range)->fetchAll(\PDO::FETCH_ASSOC);
    
    // Recent sessions
    $recentSessions = $db->query("SELECT * FROM analytics_sessions ORDER BY last_activity_at DESC LIMIT 10")->fetchAll(\PDO::FETCH_ASSOC);
    
    // Calculate percentage change
    $change = $pageviewsYesterday > 0 ? round(($pageviewsToday - $pageviewsYesterday) / $pageviewsYesterday * 100) : 0;
    
} catch (\Throwable $e) {
    $pageviewsToday = 0; $pageviewsYesterday = 0; $uniqueVisitors = 0; $realtime = 0; $fraudEvents = 0;
    $totalPageviews = 0; $totalVisitors = 0; $avgDaily = 0;
    $trafficData = []; $topPages = []; $devices = []; $browsers = []; $countries = []; $recentSessions = [];
    $change = 0;
}
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
      $maxViews = max(array_column($trafficData, 'cnt'));
      $maxViews = max($maxViews, 1);
    ?>
    <div class="flex items-end gap-1 h-48">
      <?php foreach ($trafficData as $day): ?>
        <?php
          $height = $maxViews > 0 ? round(($day['cnt'] / $maxViews) * 100) : 0;
          $dayName = date('D', strtotime($day['day']));
          $dayDate = date('M j', strtotime($day['day']));
        ?>
        <div class="flex-1 flex flex-col items-center group">
          <div class="relative w-full flex justify-center">
            <div class="absolute -top-8 bg-surface-container-highest text-on-surface px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
              <?= (int) $day['cnt'] ?> views
            </div>
          </div>
          <div class="w-full bg-primary-container rounded-t" style="height: <?= max($height, 2) ?>%">
            <div class="w-full bg-secondary h-full rounded-t transition-all duration-300 group-hover:brightness-110"></div>
          </div>
          <span class="font-code-sm text-code-sm text-on-surface-variant mt-2 text-[10px]"><?= $dayName ?></span>
        </div>
      <?php endforeach; ?>
    </div>
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