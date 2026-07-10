<?php

declare(strict_types=1);

namespace Tavp\Cms\Content;

/**
 * The field types a content type can use.
 *
 * This is the palette an editor sees when defining a custom content type
 * (Voyager BREAD / Filament form schema). "blocks" enables Twill/Gutenberg
 * style block-based bodies.
 */
enum FieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case RichText = 'richtext';
    case Slug = 'slug';
    case Number = 'number';
    case Boolean = 'boolean';
    case Select = 'select';
    case Date = 'date';
    case DateTime = 'datetime';
    case Media = 'media';
    case Relation = 'relation';
    case Blocks = 'blocks';
    case Json = 'json';
    case Password = 'password';
    case Email = 'email';
    case Url = 'url';
    case Color = 'color';
    case Tags = 'tags';
    case Repeater = 'repeater';
    case Seo = 'seo';

    /**
     * Whether values of this type are stored as structured JSON rather
     * than a scalar string.
     */
    public function isStructured(): bool
    {
        return match ($this) {
            self::Blocks, self::Json, self::Media, self::Relation,
            self::Tags, self::Repeater, self::Seo => true,
            default => false,
        };
    }

    /**
     * Whether this field supports multiple values.
     */
    public function isMultiple(): bool
    {
        return match ($this) {
            self::Tags, self::Relation => true,
            default => false,
        };
    }

    /**
     * The tavpblocks/tavphub form control used to edit this field.
     */
    public function control(): string
    {
        return match ($this) {
            self::Text, self::Slug, self::Email, self::Url => 'input',
            self::Password => 'password',
            self::Textarea => 'textarea',
            self::RichText => 'editor',
            self::Number => 'number',
            self::Boolean => 'toggle',
            self::Select => 'select',
            self::Date => 'date',
            self::DateTime => 'datetime',
            self::Media => 'media-picker',
            self::Relation => 'relation-picker',
            self::Blocks => 'block-editor',
            self::Json => 'code',
            self::Color => 'color',
            self::Tags => 'tags-input',
            self::Repeater => 'repeater',
            self::Seo => 'seo-editor',
        };
    }
}
