{% extends 'hub/layouts/admin.volt' %}

{% block content %}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Settings</h1>
</div>

<form method="post" action="{{ config('hub.admin_prefix', '/admin') }}/settings" class="space-y-8 max-w-2xl">
    <input type="hidden" name="group" value="general">

    <div class="rounded-lg bg-gray-900 border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">General</h2>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Site Name</label>
                <input type="text" name="settings[name]" value="{{ settings['general.name'] | default('') }}"
                    class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Site Description</label>
                <textarea name="settings[description]" rows="3"
                    class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ settings['general.description'] | default('') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Site URL</label>
                <input type="url" name="settings[url]" value="{{ settings['general.url'] | default('') }}"
                    class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <div class="rounded-lg bg-gray-900 border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">SEO</h2>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Default Title Suffix</label>
                <input type="text" name="settings[seo_title_suffix]" value="{{ settings['seo.title_suffix'] | default('') }}" placeholder="— My Site"
                    class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Default Meta Description</label>
                <textarea name="settings[seo_meta_description]" rows="2"
                    class="w-full rounded-lg border border-gray-700 bg-gray-900 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ settings['seo.meta_description'] | default('') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">Save Settings</button>
    </div>
</form>
{% endblock %}
