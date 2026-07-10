# TAVP CMS

A content management system built on the TAVP Stack. WordPress-familiar,
Voyager-style admin, thin and fast — with pluggable storage.

> **Version: 0.1.0 (Genesis)** — ZeroVer `0.MINOR.PATCH`.

## Philosophy

Thin. Light. Not bloated. TAVP CMS composes the existing TAVP modules instead
of reinventing them:

| Concern | Module |
|---------|--------|
| Foundation (routing, ORM, Volt) | `tavp/core` |
| Admin / back-office | `tavp/tavphub` |
| Authentication | `tavp/tavpid` |
| UI components | `tavp/tavpblocks` |

## Pluggable storage

You are not locked into a database. Choose the driver in `config/cms.php`:

- **database** — WordPress/Voyager style, via Phalcon ORM (`CMS_STORAGE=database`)
- **flatfile** — Statamic style, Markdown + YAML front matter (`CMS_STORAGE=flatfile`)

The rest of the CMS behaves identically regardless of driver.

## Custom content types (BREAD)

Page and Post ship by default. Define your own content types — with fields
and a form schema — from config or the admin UI (Voyager-style BREAD). Fields
include `text`, `richtext`, `slug`, `select`, `media`, `relation`, `blocks`
(Twill/Gutenberg-style block editor) and more.

## Features (Genesis 0.1.0)

- Pages & Posts
- Media library
- Menu builder (nestable)
- Site settings
- Theming (Volt + Tailwind), driver-agnostic
- Custom content types (BREAD)

## Requirements

- PHP 8.3+
- Phalcon 5.16+
- `tavp/core` ^1.1

## Install

```bash
composer create-project tavp/cms my-site
cd my-site
tavp migrate
tavp serve
```

## Structure

```
config/cms.php          storage driver, content types, media, theme
src/Storage/            ContentStore + DatabaseStore + FlatFileStore
src/Content/            ContentType, Field, FieldType
src/Bread/              BreadManager (Browse/Read/Edit/Add/Delete)
src/Media/              MediaLibrary
src/Menu/               MenuBuilder
src/Settings/           Settings
src/Theme/              ThemeManager
database/migrations/    contents, content_types, media, menus, settings
themes/default/         Volt + Tailwind default theme
```

## License

MIT
