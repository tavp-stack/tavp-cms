<?php

declare(strict_types=1);

namespace Tavp\Cms\Content;

/**
 * A single field on a content type (a column in BREAD terms).
 */
class Field
{
    /**
     * @param array<string,mixed> $options extra options (select values, relation target, etc.)
     */
    public function __construct(
        public readonly string $name,
        public readonly FieldType $type,
        public readonly bool $required = false,
        public readonly mixed $default = null,
        public readonly ?string $from = null,
        public readonly array $options = [],
    ) {
    }

    /**
     * Build a Field from a config array.
     *
     * @param array<string,mixed> $config
     */
    public static function fromArray(array $config): self
    {
        $type = FieldType::tryFrom((string) ($config['type'] ?? 'text')) ?? FieldType::Text;

        return new self(
            name: (string) $config['name'],
            type: $type,
            required: (bool) ($config['required'] ?? false),
            default: $config['default'] ?? null,
            from: $config['from'] ?? null,
            options: is_array($config['options'] ?? null)
                ? $config['options']
                : (isset($config['options']) ? [$config['options']] : []),
        );
    }

    public function label(): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $this->name));
    }

    /**
     * The schema a tavphub/Filament-style form renderer consumes.
     *
     * @return array<string,mixed>
     */
    public function toFormSchema(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label(),
            'control' => $this->type->control(),
            'required' => $this->required,
            'default' => $this->default,
            'options' => $this->options,
        ];
    }
}
