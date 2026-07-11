{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">{{ heading }}</h1>
</div>

<form method="post" action="{{ action }}" class="space-y-6 max-w-3xl">
    <!-- Menu name -->
    <div class="rounded-lg bg-gray-900 border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">Menu Settings</h2>
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">Menu Name</label>
            <input type="text" name="name" value="{{ menu['name'] | default('') }}" required
                class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
    </div>

    <!-- Menu items -->
    <div class="rounded-lg bg-gray-900 border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">Menu Items</h2>

        <div id="menu-items" class="space-y-3">
            {% for item in items %}
                <div class="flex gap-3 items-center p-3 bg-gray-800 rounded-lg" data-id="{{ item['id'] }}">
                    <input type="hidden" name="items[{{ loop.index0 }}][parent_id]" value="{{ item['parent_id'] }}">
                    <input type="text" name="items[{{ loop.index0 }}][label]" value="{{ item['label'] }}" placeholder="Label"
                        class="flex-1 rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm">
                    <input type="text" name="items[{{ loop.index0 }}][url]" value="{{ item['url'] }}" placeholder="/path"
                        class="flex-1 rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm font-mono">
                    <select name="items[{{ loop.index0 }}][parent_id]" class="rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm">
                        <option value="0">— Top level —</option>
                        {% for other in items %}
                            {% if other['id'] != item['id'] %}
                                <option value="{{ other['id'] }}" {% if other['id'] == item['parent_id'] %}selected{% endif %}>{{ other['label'] }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                    <button type="button" onclick="this.closest('[data-id]').remove()" class="text-red-400 hover:text-red-300">✕</button>
                </div>
            {% endfor %}
        </div>

        <!-- Add new item -->
        <div class="mt-4 flex gap-3">
            <input type="text" id="new-label" placeholder="Label" class="flex-1 rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm">
            <input type="text" id="new-url" placeholder="/path" class="flex-1 rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm font-mono">
            <button type="button" onclick="addMenuItem()" class="rounded bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700">+ Add</button>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">Save Menu</button>
        <a href="/admin/menus" class="rounded-lg border border-gray-700 px-6 py-3 text-gray-300 hover:bg-gray-800">Cancel</a>
    </div>
</form>

<script>
let itemIndex = {{ items | length }};
function addMenuItem() {
    const label = document.getElementById('new-label').value;
    const url = document.getElementById('new-url').value;
    if (!label) return;

    const html = `
        <div class="flex gap-3 items-center p-3 bg-gray-800 rounded-lg" data-id="new-${itemIndex}">
            <input type="hidden" name="items[${itemIndex}][parent_id]" value="0">
            <input type="text" name="items[${itemIndex}][label]" value="${label}" class="flex-1 rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm">
            <input type="text" name="items[${itemIndex}][url]" value="${url}" class="flex-1 rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm font-mono">
            <select name="items[${itemIndex}][parent_id]" class="rounded border border-gray-700 bg-gray-900 px-3 py-2 text-white text-sm">
                <option value="0">— Top level —</option>
            </select>
            <button type="button" onclick="this.closest('[data-id]').remove()" class="text-red-400 hover:text-red-300">✕</button>
        </div>
    `;
    document.getElementById('menu-items').insertAdjacentHTML('beforeend', html);
    itemIndex++;
    document.getElementById('new-label').value = '';
    document.getElementById('new-url').value = '';
}
</script>
{% endblock %}
