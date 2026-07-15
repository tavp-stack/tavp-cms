<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">Redirects</h1>
    <a href="/admin/seo" class="text-gray-400 hover:text-white">← Back</a>
</div>

<div class="bg-gray-800 rounded-lg p-6 mb-6">
    <h2 class="text-lg font-bold text-white mb-4">Add Redirect</h2>
    <form method="POST" action="/admin/seo/redirects" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-gray-300 text-sm mb-1">From URL</label>
            <input type="text" name="from_url" placeholder="/old-page" class="w-full bg-gray-700 text-white rounded px-3 py-2" required>
        </div>
        <div class="flex-1">
            <label class="block text-gray-300 text-sm mb-1">To URL</label>
            <input type="text" name="to_url" placeholder="/new-page" class="w-full bg-gray-700 text-white rounded px-3 py-2" required>
        </div>
        <div class="w-32">
            <label class="block text-gray-300 text-sm mb-1">Status</label>
            <select name="status_code" class="w-full bg-gray-700 text-white rounded px-3 py-2">
                <option value="301">301 (Permanent)</option>
                <option value="302">302 (Temporary)</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-yellow-500 text-black rounded hover:bg-yellow-400">Add</button>
    </form>
</div>

<div class="bg-gray-800 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-700">
                <th class="text-left text-gray-400 text-sm px-4 py-3">From</th>
                <th class="text-left text-gray-400 text-sm px-4 py-3">To</th>
                <th class="text-left text-gray-400 text-sm px-4 py-3">Status</th>
                <th class="text-left text-gray-400 text-sm px-4 py-3">Hits</th>
                <th class="text-left text-gray-400 text-sm px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($redirects)): ?>
            <tr><td colspan="5" class="text-gray-500 px-4 py-4 text-center">No redirects found.</td></tr>
            <?php else: ?>
            <?php foreach ($redirects as $r): ?>
            <tr class="border-b border-gray-700">
                <td class="text-white px-4 py-3"><?= $this->e($r['from_url']) ?></td>
                <td class="text-white px-4 py-3"><?= $this->e($r['to_url']) ?></td>
                <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded <?= $r['status_code'] == 301 ? 'bg-green-900 text-green-300' : 'bg-blue-900 text-blue-300' ?>"><?= $r['status_code'] ?></span></td>
                <td class="text-gray-400 px-4 py-3"><?= $r['hits'] ?? 0 ?></td>
                <td class="px-4 py-3">
                    <form method="POST" action="/admin/seo/redirects/delete" class="inline" onsubmit="return confirm('Delete this redirect?')">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
