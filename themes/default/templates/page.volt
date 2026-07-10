{% extends 'layouts/app.volt' %}

{% block content %}
<article class="prose max-w-none">
    <h1 class="text-3xl font-bold">{{ content['title'] }}</h1>
    {% if content['featured_image'] is defined and content['featured_image'] %}
        <img src="{{ content['featured_image'] }}" alt="{{ content['title'] }}" class="my-6 rounded-lg">
    {% endif %}
    <div class="mt-6">
        {{ content['body'] }}
    </div>
</article>
{% endblock %}
