<?php

declare(strict_types=1);

namespace Tavp\Cms\Content;

/**
 * A content type definition (Voyager BREAD / WordPress post type).
 *
 * Content types can be declared in config/cms.php or created at runtime
 * from the admin UI and persisted through the active storage driver.
 */
class ContentType
{
    /**
     * @param array<int,Field> $fields
     */
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $singular,
        public readonly array $fields,
        public readonly string $icon = 'document',
        public readonly string $route = '/{slug}',
    ) {
    }

    /**
     * Build a ContentType from a config array.
     *
     * @param array<string,mixed> $config
     */
    public static function fromArray(string $name, array $config): self
    {
        $fields = array_map(
            static fn (array $f) => Field::fromArray($f),
            $config['fields'] ?? []
        );

        return new self(
            name: $name,
            label: (string) ($config['label'] ?? ucfirst($name) . 's'),
            singular: (string) ($config['singular'] ?? ucfirst($name)),
            fields: $fields,
            icon: (string) ($config['icon'] ?? 'document'),
            route: (string) ($config['route'] ?? '/{slug}'),
        );
    }

    public function field(string $name): ?Field
    {
        foreach ($this->fields as $field) {
            if ($field->name === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Columns shown in the admin list (Browse) view.
     *
     * @return array<int,string>
     */
    public function browseColumns(): array
    {
        $preferred = ['title', 'slug', 'status'];
        $names = array_map(fn (Field $f) => $f->name, $this->fields);

        $columns = array_values(array_intersect($preferred, $names));

        return $columns ?: array_slice($names, 0, 3);
    }

    /**
     * The full form schema for Add/Edit views.
     *
     * @return array<int,array<string,mixed>>
     */
    public function formSchema(): array
    {
        return array_map(fn (Field $f) => $f->toFormSchema(), $this->fields);
    }
}
