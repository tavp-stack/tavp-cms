{% extends 'layouts/app.volt' %}

{% block content %}
<h1 class="text-3xl font-bold mb-8">Blog</h1>

{% if posts is defined and posts|length %}
    <div class="space-y-8">
        {% for post in posts %}
            <article class="border-b border-gray-100 pb-8">
                <h2 class="text-xl font-semibold">
                    <a href="/blog/{{ post['slug'] }}" class="hover:text-blue-600">{{ post['title'] }}</a>
                </h2>
                {% if post['excerpt'] is defined and post['excerpt'] %}
                    <p class="mt-2 text-gray-600">{{ post['excerpt'] }}</p>
                {% endif %}
                <div class="mt-2 text-sm text-gray-400">
                    {% if post['created_at'] is defined %}
                        {{ post['created_at'] }}
                    {% endif %}
                </div>
            </article>
        {% endfor %}
    </div>
{% else %}
    <p class="text-gray-500">No posts yet.</p>
{% endif %}
{% endblock %}
