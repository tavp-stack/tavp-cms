<?php /** Analytics Dashboard */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Analytics</h2>
  <a href="/analytics" target="_blank" class="text-secondary font-label-caps text-label-caps hover:underline">View Full Dashboard &rarr;</a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-gutter mb-gutter">
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">PAGEVIEWS TODAY</p>
    <h3 class="font-headline-xl text-headline-xl" id="stat-pageviews">--</h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">UNIQUE VISITORS</p>
    <h3 class="font-headline-xl text-headline-xl" id="stat-visitors">--</h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">REAL-TIME</p>
    <h3 class="font-headline-xl text-headline-xl text-secondary" id="stat-realtime">--</h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">FRAUD EVENTS</p>
    <h3 class="font-headline-xl text-headline-xl text-error" id="stat-fraud">--</h3>
  </div>
</div>

<!-- Quick Info -->
<div class="bg-surface-container border border-outline-variant p-6">
  <h3 class="font-headline-lg text-headline-lg text-secondary mb-4">Analytics Integration</h3>
  <p class="font-body-md text-on-surface-variant mb-4">TAVP Analytics is integrated. Track page views, user behavior, and fraud detection.</p>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-surface-container-low p-4 border border-outline-variant">
      <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">STATUS</p>
      <p class="font-code-sm text-secondary">Active</p>
    </div>
    <div class="bg-surface-container-low p-4 border border-outline-variant">
      <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">TRACKER</p>
      <code class="font-code-sm text-secondary">/js/tracker.js</code>
    </div>
    <div class="bg-surface-container-low p-4 border border-outline-variant">
      <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">API</p>
      <code class="font-code-sm text-secondary">/api/analytics</code>
    </div>
  </div>
</div>
