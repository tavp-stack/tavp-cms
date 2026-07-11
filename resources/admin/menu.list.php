{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Menus</h1>
    <a href="/admin/menus/create" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">+ New Menu</a>
</div>

<div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            {% for menu in menus %}
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4 text-sm text-white">{{ menu['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400 font-mono">{{ menu['slug'] }}</td>
                    <td class="px-6 py-4 text-sm">
                        <a href="/admin/menus/{{ menu['id'] }}/edit" class="text-blue-400 hover:underline mr-3">Edit</a>
                        <form method="post" action="/admin/menus/{{ menu['id'] }}/delete" class="inline">
                            <button type="submit" class="text-red-400 hover:underline" onclick="return confirm('Delete menu and all items?')">Delete</button>
                        </form>
                    </td>
                </tr>
            {% endfor %}
            {% if menus is empty %}
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">No menus yet.</td>
                </tr>
            {% endif %}
        </tbody>
    </table>
</div>
{% endblock %}
