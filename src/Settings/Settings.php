<?php

declare(strict_types=1);

namespace Tavp\Cms\Settings;

/**
 * Site settings — a simple grouped key/value store with an in-memory cache.
 */
class Settings
{
    /** @var array<string,mixed>|null */
    private ?array $cache = null;

    /**
     * @param \Closure():array<string,mixed> $loader loads all settings as key => value
     * @param \Closure(string,mixed):void   $writer persists a single key
     */
    public function __construct(
        private readonly \Closure $loader,
        private readonly \Closure $writer
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->cache ??= ($this->loader)();

        return $this->cache[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        ($this->writer)($key, $value);

        $this->cache ??= [];
        $this->cache[$key] = $value;
    }

    /**
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->cache ??= ($this->loader)();
    }

    public function forget(): void
    {
        $this->cache = null;
    }
}
