<?php /** Analytics Dashboard */ ?>
<div class="flex justify-between items-center mb-gutter">
  <h2 class="font-headline-xl text-headline-xl">Analytics</h2>
  <a href="/admin/analytics" class="text-secondary font-label-caps text-label-caps hover:underline">Refresh</a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-gutter mb-gutter">
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">PAGEVIEWS TODAY</p>
    <h3 class="font-headline-xl text-headline-xl">0</h3>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-2">Tracker not yet installed</p>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">UNIQUE VISITORS</p>
    <h3 class="font-headline-xl text-headline-xl">0</h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">REAL-TIME</p>
    <h3 class="font-headline-xl text-headline-xl text-secondary">0</h3>
  </div>
  <div class="bg-surface-container p-6 border border-outline-variant performance-card">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">FRAUD EVENTS</p>
    <h3 class="font-headline-xl text-headline-xl text-error">0</h3>
  </div>
</div>

<!-- How to Enable -->
<div class="bg-surface-container border border-outline-variant p-6">
  <h3 class="font-headline-lg text-headline-lg text-secondary mb-4">How to Enable Analytics</h3>
  <p class="font-body-md text-on-surface-variant mb-4">Add the tracker script to your layout to start collecting data.</p>

  <div class="bg-surface-container-low border border-outline-variant rounded p-4 mb-4">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">STEP 1: Add tracker to layout</p>
    <code class="font-code-sm text-code-sm text-secondary">&lt;script src="/js/tracker.js" defer&gt;&lt;/script&gt;</code>
  </div>

  <div class="bg-surface-container-low border border-outline-variant rounded p-4 mb-4">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">STEP 2: Configure in .env</p>
    <code class="font-code-sm text-code-sm text-secondary">ANALYTICS_ENABLED=true</code>
  </div>

  <div class="bg-surface-container-low border border-outline-variant rounded p-4">
    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">API ENDPOINT</p>
    <code class="font-code-sm text-code-sm text-secondary">/api/analytics</code>
    <p class="font-code-sm text-code-sm text-on-surface-variant mt-1">Track page views and events via POST requests.</p>
  </div>
</div>
