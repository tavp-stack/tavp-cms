<?php

declare(strict_types=1);

namespace Tavp\Cms\Theme;

/**
 * Theme manager — resolves the active front-end theme (Volt + Tailwind).
 *
 * A theme is a folder under /themes containing a theme.json manifest,
 * layouts/, and templates/. Switching themes is a config change; no code
 * changes required.
 */
class ThemeManager
{
    public function __construct(
        private readonly string $themesPath,
        private readonly string $active = 'default'
    ) {
    }

    public function active(): string
    {
        return $this->active;
    }

    public function path(string $file = ''): string
    {
        $base = rtrim($this->themesPath, '/') . '/' . $this->active;

        return $file === '' ? $base : $base . '/' . ltrim($file, '/');
    }

    public function viewsPath(): string
    {
        return $this->path('templates');
    }

    public function layoutsPath(): string
    {
        return $this->path('layouts');
    }

    /**
     * Read the theme manifest (theme.json).
     *
     * @return array<string,mixed>
     */
    public function manifest(): array
    {
        $file = $this->path('theme.json');

        if (!is_file($file)) {
            return ['name' => $this->active];
        }

        $data = json_decode((string) file_get_contents($file), true);

        return is_array($data) ? $data : ['name' => $this->active];
    }

    /**
     * Resolve a template file within the active theme, e.g. "page" or "post".
     */
    public function template(string $name): ?string
    {
        $file = $this->viewsPath() . '/' . $name . '.volt';

        return is_file($file) ? $file : null;
    }
}
