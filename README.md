# TAVP CMS

A content management system built on the TAVP Stack. WordPress-familiar,
Voyager-style admin, thin and fast — with pluggable storage, headless API,
and a modern feature set that rivals any CMS on the market.

> **Version: 0.2.0 (Mature)** — ZeroVer `0.MINOR.PATCH`.

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

## Features

### Core (v0.1.0 Genesis)
- Pages & Posts
- Media library
- Menu builder (nestable)
- Site settings
- Theming (Volt + Tailwind), driver-agnostic
- Custom content types (BREAD)
- OTP passwordless admin authentication

### Mature (v0.2.0)
- **Headless REST API** — full CRUD + pagination + search + taxonomy via `/api/cms`
- **Taxonomy** — categories (hierarchical) + tags (flat), attached to any content type
- **Content revisions** — automatic version history on every save, one-click rollback
- **Full-text search** — search across all content types from admin UI and API
- **Field validation** — server-side rules (`required`, `min`, `max`, `email`, `unique`, etc.)
- **Cached storage** — read-through cache (file or in-memory) wrapping the storage layer
- **SEO & sitemap** — per-record meta fields, auto-generated `sitemap.xml`
- **Webhooks** — fire HTTP POST on content created/updated/deleted
- **Scheduled publishing** — set `published_at` and publish via cron (`tavp cms:publish`)
- **RBAC-lite** — email-to-role mapping with permission patterns in admin
- **CLI** — `php bin/cms` for types, make:type, api:token, search:reindex, taxonomy
- **Blog index** — `/blog` listing, `/category/{slug}`, `/tag/{slug}` archives
- **More field types** — password, email, url, color, tags, repeater, seo-editor

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

## Quick start

### 1. Configure

```bash
# .env
CMS_STORAGE=database
CMS_ADMIN_EMAILS=you@example.com
CMS_API_TOKENS=your-secret-token-here
APP_URL=https://mysite.com
```

### 2. Run migrations

```bash
tavp migrate
```

### 3. Admin

Visit `/admin`, sign in with your allowed email via OTP.

### 4. Headless API

```bash
# List content types
curl -H "Authorization: Bearer YOUR_TOKEN" https://mysite.com/api/cms/types

# Browse posts
curl -H "Authorization: Bearer YOUR_TOKEN" https://mysite.com/api/cms/post

# Create a post
curl -X POST -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title":"Hello","slug":"hello","body":"World","status":"published"}' \
  https://mysite.com/api/cms/post

# Search
curl -H "Authorization: Bearer YOUR_TOKEN" "https://mysite.com/api/cms/search?q=hello"

# List taxonomy terms
curl -H "Authorization: Bearer YOUR_TOKEN" https://mysite.com/api/cms/taxonomy/category
```

### 5. CLI

```bash
php bin/cms help
php bin/cms types
php bin/cms make:type product
php bin/cms api:token my-app
php bin/cms search:reindex
```

## API Reference

All endpoints require a Bearer token via `Authorization: Bearer <token>`.

### Content

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cms/types` | List content types |
| GET | `/api/cms/{type}` | Browse records (paginated) |
| GET | `/api/cms/{type}/{id}` | Read a record |
| POST | `/api/cms/{type}` | Create a record |
| PUT | `/api/cms/{type}/{id}` | Update a record |
| DELETE | `/api/cms/{type}/{id}` | Delete a record |

### Query parameters (browse)

| Param | Default | Description |
|-------|---------|-------------|
| `page` | 1 | Page number |
| `per_page` | 15 | Items per page (max: 100) |
| `status` | — | Filter by status (draft, published) |

### Search

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cms/search?q={query}` | Full-text search |
| GET | `/api/cms/search?q={query}&type=post` | Search within type |

### Taxonomy

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cms/taxonomy/{type}` | List terms (category, tag) |
| POST | `/api/cms/taxonomy` | Create a term |

### Revisions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cms/{type}/{id}/revisions` | Revision history |
| POST | `/api/cms/{type}/{id}/rollback/{ts}` | Rollback to revision |

## Content types

Define content types in `config/cms.php` or use the CLI:

```bash
php bin/cms make:type product
```

Each type has fields with validation rules:

```php
'fields' => [
    ['name' => 'title', 'type' => 'text', 'required' => true, 'rules' => ['max:200']],
    ['name' => 'slug', 'type' => 'slug', 'from' => 'title', 'rules' => ['unique']],
    ['name' => 'price', 'type' => 'number', 'rules' => ['numeric']],
    ['name' => 'body', 'type' => 'richtext'],
    ['name' => 'status', 'type' => 'select', 'options' => ['draft', 'published']],
],
```

### Available field types

| Type | Control | Description |
|------|---------|-------------|
| `text` | input | Plain text |
| `textarea` | textarea | Multi-line text |
| `richtext` | editor | Rich text editor |
| `slug` | input | URL slug (auto-generated from source field) |
| `number` | number | Numeric value |
| `boolean` | toggle | Yes/no |
| `select` | select | Dropdown selection |
| `date` | date | Date picker |
| `datetime` | datetime | Date + time picker |
| `media` | media-picker | File/image upload |
| `relation` | relation-picker | Related content |
| `blocks` | block-editor | Block-based content |
| `json` | code | Raw JSON |
| `password` | password | Password input |
| `email` | input (email) | Email input |
| `url` | input (url) | URL input |
| `color` | color | Color picker |
| `tags` | tags-input | Tag list |
| `repeater` | repeater | Repeating field groups |
| `seo` | seo-editor | SEO meta fields |

### Validation rules

| Rule | Example | Description |
|------|---------|-------------|
| `required` | `'required'` | Field must be present and non-empty |
| `min:N` | `'min:3'` | Minimum length (strings) |
| `max:N` | `'max:200'` | Maximum length (strings) |
| `email` | `'email'` | Must be valid email |
| `url` | `'url'` | Must be valid URL |
| `unique` | `'unique'` | Slug must be unique (checked by BreadManager) |
| `numeric` | `'numeric'` | Must be numeric |
| `in:a,b,c` | `'in:draft,published'` | Must be in the list |
| `regex:/pattern/` | `'/^[a-z]+$/'` | Must match regex |

## Custom content types (BREAD)

Page and Post ship by default. Define your own content types — with fields
and a form schema — from config or the admin UI (Voyager-style BREAD). Fields
include `text`, `richtext`, `slug`, `select`, `media`, `relation`, `blocks`
(Twill/Gutenberg-style block editor) and more.

## Structure

```
config/cms.php                     storage driver, content types, media, theme,
                                   taxonomy, api, cache, webhooks, revisions,
                                   search, seo, publishing, roles
src/Storage/                       ContentStore + DatabaseStore + FlatFileStore
src/Content/                       ContentType, Field, FieldType, Validator,
                                   ValidationException
src/Bread/                         BreadManager (Browse/Read/Edit/Add/Delete)
src/Taxonomy/                      TaxonomyManager + Term + DatabaseTaxonomyFactory
src/Revisions/                     RevisionStore (versioning + rollback)
src/Search/                        SearchEngine (full-text across types)
src/Cache/                         CachedContentStore (read-through decorator)
src/Media/                         MediaLibrary
src/Menu/                          MenuBuilder
src/Settings/                      Settings
src/Theme/                         ThemeManager
src/Api/                           ApiController + ApiTokenService + ApiModule
src/Webhooks/                      WebhookManager + DatabaseWebhookFactory
src/Seo/                           SitemapController
src/Publishing/                    PublishScheduler
src/Auth/                          RbacGuard (RBAC-lite)
src/Console/                       CLI commands
src/Admin/                         AdminController, AuthController,
                                   ContentController, DashboardController,
                                   RevisionController, SearchController,
                                   TaxonomyController
database/migrations/               contents, content_types, media, menus,
                                   settings, taxonomy_terms, content_taxonomy,
                                   content_revisions, webhooks,
                                   webhook_deliveries, api_tokens
resources/admin/                   Admin UI templates
themes/default/                    Volt + Tailwind default theme
bin/cms                            CLI entry point
routes/web.php                     Front-end routes (blog, taxonomy archives,
                                   page catch-all)
```

## Front-end routes

| Route | Description |
|-------|-------------|
| `/blog` | Blog index (published posts) |
| `/blog/{slug}` | Single post |
| `/category/{slug}` | Category archive |
| `/tag/{slug}` | Tag archive |
| `/{slug}` | Page (catch-all) |
| `/sitemap.xml` | Auto-generated sitemap |

## Configuration reference

See `config/cms.php` for all options. Key settings:

| Key | Default | Description |
|-----|---------|-------------|
| `CMS_STORAGE` | `database` | Storage driver |
| `CMS_CACHE_ENABLED` | `true` | Enable read-through cache |
| `CMS_CACHE_DRIVER` | `file` | Cache backend (file, array) |
| `CMS_CACHE_TTL` | `300` | Cache TTL in seconds |
| `CMS_API_PREFIX` | `api/cms` | API route prefix |
| `CMS_API_TOKENS` | — | Comma-separated bearer tokens |
| `CMS_REVISIONS_ENABLED` | `true` | Enable version history |
| `CMS_REVISIONS_LIMIT` | `50` | Max revisions per record |
| `CMS_SEARCH_ENABLED` | `true` | Enable search indexing |
| `CMS_TAXONOMY_ENABLED` | `true` | Enable categories + tags |
| `CMS_SEO_ENABLED` | `true` | Enable sitemap + SEO fields |
| `CMS_WEBHOOKS_ENABLED` | `true` | Enable webhook dispatch |
| `CMS_PUBLISHING_ENABLED` | `true` | Enable scheduled publishing |

## License

MIT
