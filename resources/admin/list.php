<?php /** @var \Tavp\Cms\Content\ContentType $type @var array $records */ ?>
<?php
$columns = $type->browseColumns();
$rowData = [];
foreach ($records as $r) {
    $cells = [];
    foreach ($columns as $col) {
        $v = $r[$col] ?? '';
        $cells[$col] = is_scalar($v) ? (string) $v : '';
    }
    $rowData[] = [
        'id' => (string) ($r['id'] ?? ''),
        'cells' => $cells,
        'edit' => '/admin/c/' . $type->name . '/' . ($r['id'] ?? '') . '/edit',
        'delete' => '/admin/c/' . $type->name . '/' . ($r['id'] ?? '') . '/delete',
    ];
}
?>
<div x-data='{
  q: "",
  sortKey: "",
  sortDir: "asc",
  rows: <?= htmlspecialchars(json_encode($rowData, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES) ?>,
  get filtered() {
    let r = this.rows.filter(row =>
      this.q === "" || Object.values(row.cells).join(" ").toLowerCase().includes(this.q.toLowerCase())
    );
    if (this.sortKey) {
      r = [...r].sort((a, b) => {
        let x = (a.cells[this.sortKey] || "").toLowerCase();
        let y = (b.cells[this.sortKey] || "").toLowerCase();
        let n = parseFloat(x), m = parseFloat(y);
        if (!isNaN(n) && !isNaN(m)) { x = n; y = m; }
        return (x < y ? -1 : x > y ? 1 : 0) * (this.sortDir === "asc" ? 1 : -1);
      });
    }
    return r;
  },
  toggleSort(k) {
    if (this.sortKey === k) { this.sortDir = this.sortDir === "asc" ? "desc" : "asc"; }
    else { this.sortKey = k; this.sortDir = "asc"; }
  }
}'>
  <div class="flex justify-between items-center mb-gutter">
    <h2 class="font-headline-xl text-headline-xl"><?= $this->e($type->label) ?></h2>
    <a href="/admin/c/<?= $this->e($type->name) ?>/create" class="bg-secondary text-on-secondary py-3 px-6 rounded font-label-caps text-label-caps hard-step-shadow hover:brightness-110 active:translate-y-[1px] transition-all">
      + NEW <?= strtoupper($this->e($type->singular)) ?>
    </a>
  </div>

  <div class="mb-4 flex items-center gap-3">
    <div class="relative flex-1 max-w-md">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
      <input type="text" x-model="q" placeholder="Cari <?= $this->e(strtolower($type->label)) ?>..."
             class="w-full bg-surface-container border border-outline-variant rounded pl-10 pr-4 py-2.5 font-body-md text-body-md focus:border-secondary focus:outline-none transition-colors">
    </div>
    <span class="font-code-sm text-code-sm text-on-surface-variant" x-text="filtered.length + ' / ' + rows.length"></span>
  </div>

  <div class="bg-surface-container border border-outline-variant overflow-hidden">
    <table class="w-full text-body-md">
      <thead class="bg-surface-container-high border-b border-outline-variant">
        <tr>
          <?php foreach ($columns as $col): ?>
            <th class="px-4 py-3 text-left font-label-caps text-label-caps text-on-surface-variant cursor-pointer select-none hover:text-secondary transition-colors"
                @click="toggleSort('<?= $this->e($col) ?>')">
              <span class="inline-flex items-center gap-1">
                <?= $this->e(ucwords(str_replace('_', ' ', $col))) ?>
                <span class="material-symbols-outlined text-[16px]" x-show="sortKey === '<?= $this->e($col) ?>'" x-text="sortDir === 'asc' ? 'arrow_upward' : 'arrow_downward'"></span>
              </span>
            </th>
          <?php endforeach; ?>
          <th class="px-4 py-3 text-right font-label-caps text-label-caps text-on-surface-variant">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template x-if="filtered.length === 0">
          <tr><td colspan="<?= count($columns) + 1 ?>" class="px-4 py-8 text-center text-on-surface-variant font-body-md">
            <span x-text="rows.length === 0 ? 'No <?= $this->e(strtolower($type->label)) ?> yet.' : 'Tidak ada hasil untuk pencarian ini.'"></span>
          </td></tr>
        </template>
        <template x-for="row in filtered" :key="row.id">
          <tr class="border-t border-outline-variant hover:bg-surface-container-high/50 transition-colors">
            <?php foreach ($columns as $col): ?>
              <td class="px-4 py-3 font-body-md" x-text="row.cells['<?= $this->e($col) ?>']"></td>
            <?php endforeach; ?>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <a :href="row.edit" class="text-secondary font-label-caps text-label-caps hover:underline mr-3">Edit</a>
              <form method="post" :action="row.delete" class="inline" onsubmit="return confirm('Delete this item?')">
                <button class="text-error font-label-caps text-label-caps hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</div>
