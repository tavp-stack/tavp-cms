{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<h1 class="text-2xl font-bold mb-6">Invoices</h1>

<div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Gateway</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            {% for inv in invoices %}
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4 text-sm text-white">{{ inv['name'] | default('-') }} ({{ inv['email'] | default('-') }})</td>
                    <td class="px-6 py-4 text-sm text-yellow-400">${{ inv['amount'] }} {{ inv['currency'] }}</td>
                    <td class="px-6 py-4">
                        <span class="rounded-full px-2 py-1 text-xs {% if inv['status'] == 'paid' %}bg-green-900 text-green-300{% else %}bg-yellow-900 text-yellow-300{% endif %}">{{ inv['status'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ inv['gateway'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ inv['created_at'] }}</td>
                </tr>
            {% endfor %}
            {% if invoices is empty %}
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">No invoices yet.</td>
                </tr>
            {% endif %}
        </tbody>
    </table>
</div>
{% endblock %}
