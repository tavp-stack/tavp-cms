{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Teams</h1>
    <a href="/admin/teams/create" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">+ New Team</a>
</div>

<div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Owner ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            {% for team in teams %}
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4 text-sm text-white">{{ team['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ team['owner_id'] }}</td>
                    <td class="px-6 py-4 text-sm">
                        <a href="/admin/teams/{{ team['id'] }}/edit" class="text-blue-400 hover:underline mr-3">Edit</a>
                        <form method="post" action="/admin/teams/{{ team['id'] }}/delete" class="inline">
                            <button type="submit" class="text-red-400 hover:underline" onclick="return confirm('Delete team?')">Delete</button>
                        </form>
                    </td>
                </tr>
            {% endfor %}
            {% if teams is empty %}
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">No teams yet.</td>
                </tr>
            {% endif %}
        </tbody>
    </table>
</div>
{% endblock %}
