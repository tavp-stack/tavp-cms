{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Media Library</h1>
</div>

<!-- Upload form -->
<div class="rounded-lg bg-gray-900 border border-gray-800 p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Upload File</h2>
    <form method="post" action="{{ config('hub.admin_prefix', '/admin') }}/media/upload" enctype="multipart/form-data">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <input type="file" name="file" required
                    class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">Upload</button>
        </div>
    </form>
</div>

<!-- Media list -->
<div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Preview</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            {% for item in media %}
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4">
                        {% if item['mime_type'] is defined and item['mime_type'] matches '/image/' %}
                            <img src="/{{ item['path'] }}" alt="{{ item['name'] }}" class="h-10 w-10 rounded object-cover">
                        {% else %}
                            <div class="h-10 w-10 rounded bg-gray-800 flex items-center justify-center text-gray-500 text-xs">File</div>
                        {% endif %}
                    </td>
                    <td class="px-6 py-4 text-sm text-white">{{ item['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ item['mime_type'] | default('-') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ (item['size'] / 1024) | number_format(1) }} KB</td>
                    <td class="px-6 py-4 text-sm">
                        <a href="/{{ item['path'] }}" target="_blank" class="text-blue-400 hover:underline mr-3">View</a>
                        <form method="post" action="{{ config('hub.admin_prefix', '/admin') }}/media/{{ item['id'] }}/delete" class="inline">
                            <button type="submit" class="text-red-400 hover:underline" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            {% endfor %}
            {% if media is empty %}
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">No media files yet.</td>
                </tr>
            {% endif %}
        </tbody>
    </table>
</div>
{% endblock %}
