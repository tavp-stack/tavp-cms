{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<h1 class="text-2xl font-bold mb-6">{{ heading }}</h1>

<form method="post" action="{{ action }}" class="space-y-6 max-w-2xl">
    <div class="rounded-lg bg-gray-900 border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">Team Settings</h2>
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">Team Name</label>
            <input type="text" name="name" value="{{ team['name'] | default('') }}" required
                class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
    </div>

    {% if members is defined and members is not empty %}
    <div class="rounded-lg bg-gray-900 border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">Members</h2>
        <table class="w-full">
            <thead class="bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-xs text-gray-400">Name</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-400">Email</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-400">Role</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-400"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                {% for m in members %}
                    <tr>
                        <td class="px-4 py-2 text-sm text-white">{{ m['name'] | default('Unknown') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-400">{{ m['email'] | default('-') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-400">{{ m['role'] }}</td>
                        <td class="px-4 py-2 text-sm">
                            <form method="post" action="/admin/teams/{{ team['id'] }}/members/{{ m['id'] }}/remove" class="inline">
                                <button type="submit" class="text-red-400 hover:underline text-xs">Remove</button>
                            </form>
                        </td>
                    </tr>
                {% endfor %}
            </tbody>
        </table>
    </div>
    {% endif %}

    <div class="rounded-lg bg-gray-900 border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">Add Member</h2>
        <div class="flex gap-3">
            <input type="email" name="email" placeholder="user@example.com" required
                class="flex-1 rounded-lg border border-gray-700 bg-gray-900 px-4 py-2 text-white text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <select name="role" class="rounded-lg border border-gray-700 bg-gray-900 px-4 py-2 text-white text-sm">
                <option value="member">Member</option>
                <option value="admin">Admin</option>
                <option value="owner">Owner</option>
            </select>
            <button type="submit" formaction="/admin/teams/{{ team['id'] | default('0') }}/members" class="rounded-lg bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700">Add</button>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">Save Team</button>
        <a href="/admin/teams" class="rounded-lg border border-gray-700 px-6 py-3 text-gray-300 hover:bg-gray-800">Cancel</a>
    </div>
</form>
{% endblock %}
