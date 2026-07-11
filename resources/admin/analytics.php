<?php
/** @var array $__counts */

// Get analytics data
$db = app('db');
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$weekAgo = date('Y-m-d', strtotime('-7 days'));

try {
    // Today's stats
    $pageviewsToday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $pageviewsYesterday = $db->query("SELECT COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$yesterday])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $uniqueVisitors = $db->query("SELECT COUNT(DISTINCT session_id) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $realtime = $db->query("SELECT COUNT(*) as cnt FROM analytics_sessions WHERE last_activity_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    $fraudEvents = $db->query("SELECT COUNT(*) as cnt FROM analytics_fraud_events WHERE DATE(created_at) = ?", [$today])->fetchAll(\PDO::FETCH_ASSOC)[0]['cnt'] ?? 0;
    
    // Weekly traffic (last 7 days)
    $weeklyTraffic = $db->query("SELECT DATE(created_at) as day, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) >= ? GROUP BY DATE(created_at) ORDER BY day ASC", [$weekAgo])->fetchAll(\PDO::FETCH_ASSOC);
    
    // Top pages
    $topPages = $db->query("SELECT path, COUNT(*) as views FROM analytics_page_visits WHERE DATE(created_at) = ? GROUP BY path ORDER BY views DESC LIMIT 5", [$today])->fetchAll(\PDO::FETCH_ASSOC);
    
    // Device breakdown
    $devices = $db->query("SELECT device, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ? GROUP BY device ORDER BY cnt DESC", [$today])->fetchAll(\PDO::FETCH_ASSOC);
    
    // Browser breakdown
    $browsers = $db->query("SELECT browser, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ? GROUP BY browser ORDER BY cnt DESC", [$today])->fetchAll(\PDO::FETCH_ASSOC);
    
    // Geographic data
    $countries = $db->query("SELECT country, COUNT(*) as cnt FROM analytics_page_visits WHERE DATE(created_at) = ? AND country IS NOT NULL GROUP BY country ORDER BY cnt DESC LIMIT 5", [$today])->fetchAll(\PDO::FETCH_ASSOC);
    
    // Recent sessions
    $recentSessions = $db->query("SELECT * FROM analytics_sessions ORDER BY last_activity_at DESC LIMIT 5")->fetchAll(\PDO::FETCH_ASSOC);
    
    // Calculate percentage change
    $change = $pageviewsYesterday > 0 ? round(($pageviewsToday - $pageviewsYesterday) / $pageviewsYesterday * 100) : 0;
    
} catch (\Throwable $e) {
    $pageviewsToday = 0; $pageviewsYesterday = 0; $uniqueVisitors = 0; $realtime = 0; $fraudEvents = 0;
    $weeklyTraffic = []; $topPages = []; $devices = []; $browsers = []; $countries = []; $recentSessions = [];
    $change = 0;
}
?>

<div class="flex justify-between items-center mb-gutter">
  <div>
    <h1 class="font-headline-xl text-headline-xl">Analytics</h1>
    <p class="text-on-surface-variant">Real-time analytics, page views, and user behavior insights.</p>
  </div>
  <a href="/admin" class="text-secondary font-label-caps text-label-caps hover:underline">&larr; Dashboard</a>
</div>

<!-- Metrics Cards -->
<section class="grid grid-cols-1 md:grid-cols-4 gap-gutter mb-gutter">
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">PAGEVIEWS TODAY</p>
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
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">UNIQUE VISITORS</p>
    <h3 class="font-headline-xl text-headline-xl"><?= (int) $uniqueVisitors ?></h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">REAL-TIME (5 min)</p>
    <h3 class="font-headline-xl text-headline-xl text-secondary"><?= (int) $realtime ?></h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">FRAUD EVENTS</p>
    <h3 class="font-headline-xl text-headline-xl text-error"><?= (int) $fraudEvents ?></h3>
  </div>
</section>

<!-- Traffic Chart + Top Pages -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-gutter mb-gutter">
  <!-- Weekly Traffic Chart -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Traffic (7 Days)</h3>
    <?php if (!empty($weeklyTraffic)): ?>
      <?php
        $maxViews = max(array_column($weeklyTraffic, 'cnt'));
        $maxViews = max($maxViews, 1);
      ?>
      <div class="space-y-3">
        <?php foreach ($weeklyTraffic as $day): ?>
          <?php
            $height = $maxViews > 0 ? round(($day['cnt'] / $maxViews) * 100) : 0;
            $dayName = date('D', strtotime($day['day']));
            $dayDate = date('M j', strtotime($day['day']));
          ?>
          <div class="flex items-center gap-3">
            <div class="w-16 text-right">
              <span class="font-code-sm text-code-sm text-on-surface-variant"><?= $dayName ?></span><br>
              <span class="font-code-sm text-code-sm text-on-surface-variant text-[10px]"><?= $dayDate ?></span>
            </div>
            <div class="flex-1 bg-primary-container h-6 rounded overflow-hidden">
              <div class="bg-secondary h-full rounded transition-all duration-500" style="width: <?= $height ?>%"></div>
            </div>
            <div class="w-12 text-right">
              <span class="font-code-sm text-code-sm text-secondary"><?= (int) $day['cnt'] ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant text-center py-8">No data yet</p>
    <?php endif; ?>
  </div>

  <!-- Top Pages -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Top Pages</h3>
    <?php if (!empty($topPages)): ?>
      <div class="space-y-4">
        <?php foreach ($topPages as $page): ?>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary text-sm">insert_link</span>
              <span class="font-code-sm text-code-sm text-on-surface"><?= $this->e($page['path']) ?></span>
            </div>
            <span class="font-label-caps text-label-caps text-on-surface-variant"><?= (int) $page['views'] ?> views</span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant">No page data yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Device + Browser + Geographic -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mb-gutter">
  <!-- Device Breakdown -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Devices</h3>
    <?php if (!empty($devices)): ?>
      <div class="space-y-4">
        <?php
          $totalDevices = array_sum(array_column($devices, 'cnt'));
          foreach ($devices as $device):
            $pct = $totalDevices > 0 ? round($device['cnt'] / $totalDevices * 100) : 0;
        ?>
          <div>
            <div class="flex justify-between mb-1">
              <span class="font-body-md text-on-surface"><?= $this->e(ucfirst($device['device'] ?? 'Unknown')) ?></span>
              <span class="font-code-sm text-code-sm text-on-surface-variant"><?= $pct ?>%</span>
            </div>
            <div class="w-full bg-primary-container h-2 rounded-full overflow-hidden">
              <div class="bg-secondary h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant">No device data yet.</p>
    <?php endif; ?>
  </div>

  <!-- Browser Breakdown -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Browsers</h3>
    <?php if (!empty($browsers)): ?>
      <div class="space-y-4">
        <?php
          $totalBrowsers = array_sum(array_column($browsers, 'cnt'));
          foreach ($browsers as $browser):
            $pct = $totalBrowsers > 0 ? round($browser['cnt'] / $totalBrowsers * 100) : 0;
        ?>
          <div>
            <div class="flex justify-between mb-1">
              <span class="font-body-md text-on-surface"><?= $this->e($browser['browser'] ?? 'Unknown') ?></span>
              <span class="font-code-sm text-code-sm text-on-surface-variant"><?= $pct ?>%</span>
            </div>
            <div class="w-full bg-primary-container h-2 rounded-full overflow-hidden">
              <div class="bg-secondary h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant">No browser data yet.</p>
    <?php endif; ?>
  </div>

  <!-- Geographic Data -->
  <div class="bg-surface-container p-6 border border-outline-variant">
    <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Top Countries</h3>
    <?php if (!empty($countries)): ?>
      <div class="space-y-4">
        <?php
          $totalCountries = array_sum(array_column($countries, 'cnt'));
          foreach ($countries as $country):
            $pct = $totalCountries > 0 ? round($country['cnt'] / $totalCountries * 100) : 0;
        ?>
          <div>
            <div class="flex justify-between mb-1">
              <span class="font-body-md text-on-surface"><?= $this->e($country['country'] ?? 'Unknown') ?></span>
              <span class="font-code-sm text-code-sm text-on-surface-variant"><?= $pct ?>%</span>
            </div>
            <div class="w-full bg-primary-container h-2 rounded-full overflow-hidden">
              <div class="bg-secondary h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-on-surface-variant">No geographic data yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Recent Sessions -->
<section class="bg-surface-container p-6 border border-outline-variant">
  <h3 class="font-headline-lg text-headline-lg text-secondary mb-6">Recent Sessions</h3>
  <?php if (!empty($recentSessions)): ?>
    <div class="overflow-x-auto">
      <table class="w-full text-body-md">
        <thead class="bg-surface-container-high">
          <tr>
            <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Session ID</th>
            <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">IP</th>
            <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Device</th>
            <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Browser</th>
            <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Pages</th>
            <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant">Last Activity</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentSessions as $session): ?>
            <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
              <td class="px-4 py-3 font-code-sm text-code-sm"><?= $this->e(substr($session['session_id'], 0, 20)) ?>...</td>
              <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($session['ip_address'] ?? '-') ?></td>
              <td class="px-4 py-3 text-on-surface-variant"><?= $this->e($session['device'] ?? '-') ?></td>
              <td class="px-4 py-3 text-on-surface-variant"><?= $this->e($session['browser'] ?? '-') ?></td>
              <td class="px-4 py-3 font-code-sm text-code-sm text-secondary"><?= (int) ($session['page_views'] ?? 0) ?></td>
              <td class="px-4 py-3 font-code-sm text-code-sm text-on-surface-variant"><?= $this->e($session['last_activity_at'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="text-on-surface-variant">No session data yet.</p>
  <?php endif; ?>
</section>
