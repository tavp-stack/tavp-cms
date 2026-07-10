<!DOCTYPE html>
<html lang="{{ site_lang | default('en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ page_title | default(site_title) }}</title>
    <meta name="description" content="{{ page_description | default(site_description) }}">
    <link rel="stylesheet" href="/assets/app.css">
    <script defer src="/assets/app.js"></script>
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased">
    <header class="border-b border-gray-100">
        <nav class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <a href="/" class="text-lg font-semibold">{{ site_title | default('TAVP CMS') }}</a>
            <ul class="flex gap-6 text-sm">
                {% for item in menu %}
                    <li><a href="{{ item['url'] }}" class="hover:text-blue-600">{{ item['label'] }}</a></li>
                {% endfor %}
            </ul>
        </nav>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10">
        {% block content %}{% endblock %}
    </main>

    <footer class="mx-auto max-w-5xl px-4 py-10 text-sm text-gray-500">
        &copy; {{ 'now' | date('Y') }} {{ site_title | default('TAVP CMS') }}
    </footer>
</body>
</html>
