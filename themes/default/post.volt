{% extends 'layouts/app.volt' %}

{% block content %}
<article class="prose max-w-none">
    <h1 class="text-3xl font-bold">{{ content['title'] }}</h1>
    <p class="mt-2 text-sm text-gray-500">
        {% if content['published_at'] is defined and content['published_at'] %}
            {{ content['published_at'] | date('d M Y') }}
        {% endif %}
    </p>
    {% if content['featured_image'] is defined and content['featured_image'] %}
        <img src="{{ content['featured_image'] }}" alt="{{ content['title'] }}" class="my-6 rounded-lg">
    {% endif %}
    <div class="mt-6">
        {{ content['body'] }}
    </div>
</article>
{% endblock %}
