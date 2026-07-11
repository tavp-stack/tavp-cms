{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<h1 class="text-2xl font-bold mb-6">Billing</h1>

<!-- Stats -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="rounded-lg bg-gray-900 border border-gray-800 p-5">
        <div class="text-sm text-gray-400">Active Subscriptions</div>
        <div class="text-3xl font-bold text-green-400">{{ stats['active'] }}</div>
    </div>
    <div class="rounded-lg bg-gray-900 border border-gray-800 p-5">
        <div class="text-sm text-gray-400">Cancelled</div>
        <div class="text-3xl font-bold text-gray-500">{{ stats['cancelled'] }}</div>
    </div>
    <div class="rounded-lg bg-gray-900 border border-gray-800 p-5">
        <div class="text-sm text-gray-400">Total Revenue</div>
        <div class="text-3xl font-bold text-yellow-400">${{ stats['total_revenue'] | number_format(2) }}</div>
    </div>
</div>

<!-- Subscriptions -->
<div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-800">
        <h2 class="text-lg font-semibold">Subscriptions</h2>
    </div>
    <table class="w-full">
        <thead class="bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Plan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Gateway</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            {% for sub in subscriptions %}
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4 text-sm text-white">{{ sub['name'] | default('-') }} ({{ sub['email'] | default('-') }})</td>
                    <td class="px-6 py-4 text-sm text-gray-300">{{ sub['plan'] }}</td>
                    <td class="px-6 py-4">
                        <span class="rounded-full px-2 py-1 text-xs {% if sub['status'] == 'active' %}bg-green-900 text-green-300{% else %}bg-gray-700 text-gray-400{% endif %}">{{ sub['status'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ sub['gateway'] }}</td>
                    <td class="px-6 py-4 text-sm">
                        {% if sub['status'] == 'active' %}
                            <form method="post" action="/admin/billing/subscriptions/{{ sub['id'] }}/cancel" class="inline">
                                <button type="submit" class="text-red-400 hover:underline" onclick="return confirm('Cancel subscription?')">Cancel</button>
                            </form>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            {% if subscriptions is empty %}
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">No subscriptions yet.</td>
                </tr>
            {% endif %}
        </tbody>
    </table>
</div>
{% endblock %}
